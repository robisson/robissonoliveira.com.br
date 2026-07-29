---
title: "Resilience beyond the obvious: The principle of constant work"
seoTitle: "Resilience beyond the obvious: The principle of constant work"
description: "Learn how the Constant Work principle avoids bimodal behavior, reduces dangerous fallbacks, and improves distributed application resilience."
pubDate: "2026-07-29"
tags: ["Resilience", "Distributed Systems", "AWS", "Reliability", "Architecture"]
series: "Cloud Resilience"
language: "en"
---

**TLDR;** This post is about one of the things that most blew my mind since I started working at AWS with large-scale distributed systems: **many of the approaches our engineering intuition swears will increase resilience are exactly the ones that make us more vulnerable.** Fallbacks, emergency modes, "plan Bs" that only run when something goes wrong — they look like prudence, but they hide a trap. In this post we will discuss an alternative to that: the constant work principle.

Let me start with a question that sounds silly. When your system fails, does it start doing **more** work or **less**? Stop and think about it for a second. Most systems we design answer "more" — when the cache goes down, we hit the database; when a node dies, the others take over its load; when something errors, we log ten times as much. And that's the problem: we design systems that do **more work at precisely the worst possible moment**, when they're already under stress. It's like a car whose brakes require more force the faster you go.

That inversion is the root of what we call **bimodal behavior** — and understanding it changes the way you design your application's architecture.

Developing distributed systems presents unique challenges that are quite different from those found in monolithic systems or applications running on a single server. Distributed systems are, by nature, composed of multiple components that operate across different servers, regions, or even continents. This means that distributed systems must deal with network latencies, component failures, data inconsistencies, and load variability at a much larger scale than centralized systems.

The complexity of these systems is compounded by the need to guarantee high availability, consistency, and resilience, even when parts of the system fail or become inaccessible. These failures can range from a single server that stops responding, to an entire Availability Zone going offline. The challenge, therefore, is not just to build systems that work under normal conditions, but that keep operating reliably and predictably even under the most adverse conditions.

However, one of the most subtle challenges in developing distributed systems is avoiding the creation of **bimodal behaviors**. These behaviors arise when a system switches between normal operating modes and fallback modes in response to failures or changes in load. Although it may seem like a good idea to have distinct modes for handling adverse conditions, in practice these modes frequently introduce new problems, making the system more complex, less predictable, and — paradoxically — less resilient.

## What are bimodal behaviors?

**Bimodal behaviors** refer to a system's ability to operate in two distinct modes: a normal mode, where the system works as expected, and a failure or fallback mode, which is activated in response to failures, overload, or other adverse conditions that are not always failures. The idea behind bimodal behaviors is to provide an alternative path for executing critical operations when the normal mode is unavailable, is not viable, or when a task that runs only occasionally is in operation.

For example, consider an e-commerce service that normally accesses product information from an in-memory cache. If the cache fails or becomes unavailable, the system can enter a fallback mode where it queries the database directly to obtain the needed information. This is a classic example of bimodal behavior. When everything is operating well, the application feeds from the cache, but when the cache fails for any reason or starts showing a high *cache miss* rate, that's when the problems begin. Let's look at the diagram below:

![Bimodal behavior: in normal mode the cache absorbs the traffic and the database stays protected; when the cache fails, 100% of the traffic slams the database, which becomes overloaded and triggers a cascading failure](/blog/2026-07-29-how-the-constant-work-principle-increases-application-resilience/constant-work/en/image-01.png)

Other examples of bimodal behavior are:

- **Occasional resource consumption** - processes that run only periodically and are shut down when not in use. This can create bimodal behavior if the system's load peak coincides with the periodic process running, overloading the system.
- **Garbage collection in Java** - The unpredictable nature of Java's garbage collection can introduce bimodal performance, with some requests suffering significant delays due to GC pauses.
- **Error log output -** log output when the system is operating normally has, for example, 1kb of logs being generated, but when a failure occurs it has a 10kb log output because it is printing much more information about what is happening. This can also be a problem if the application is already overloaded, especially with I/O.

## Problems with Bimodal Behaviors

Although bimodal behaviors may seem like a logical solution for handling failures, they introduce a series of challenges and risks:

1. **Increased complexity:** Having distinct operating modes adds complexity to the system. This complexity manifests both in the code and in the operational logic, making the system harder to understand, maintain, and test.
2. **Unpredictability:** Transitions between modes can be hard to predict and test. A fallback mode may work well in isolated tests but behave unexpectedly in production, especially under high load.
3. **Amplified failures:** The fallback mode may end up amplifying the impact of a failure instead of mitigating it. For example, if the fallback is to query a database directly instead of using a cache, a massive cache failure can overload the database and cause a cascading failure.
4. **Testing difficulty:** Adequately testing bimodal behavior is hard, because the fallback mode may rarely be activated in production. When activated, the real conditions may be very different from the test conditions, leading to unforeseen failures.

## And fallbacks? They can hurt more than they help

When discussing resilience, it is impossible not to talk about **fallbacks**. And before going deeper, let's start with a few related definitions: fallback and failover. **Failover** means executing the activity again on a different copy of the endpoint or, preferably, running multiple parallel copies of the activity to increase the chances that at least one of them succeeds. **Fallback** means using a different mechanism to obtain the same result.

Fallback strategies are frequently seen as a solution for increasing the resilience of distributed systems. The logic behind fallbacks is simple: if one component of the system fails, another can take over its functions, ensuring the system keeps operating. However, in practice, fallbacks often hurt more than they help, especially in complex distributed systems.

### Fallback in an e-commerce checkout

Imagine a checkout flow that normally calls a synchronous fraud detection service before approving an order. While everything is healthy, the flow is simple: the order arrives, fraud detection responds, payment is authorized, and the purchase continues. But when fraud detection starts failing, the temptation is to create a "special mode": try a second API, consult old local rules, increase the timeout, or place orders into a manual review queue.

That fallback looks prudent, but it has changed the nature of the work in the application's most critical path. Checkout stops making a synchronous and well-known decision and starts accumulating intermediate states, queues, operational exceptions, and ambiguous decisions exactly during the incident. The failure of an external service becomes a behavioral change inside your business domain.

Although this strategy may keep some sales flowing in the short term, it can lead to a series of problems:

- **Increased latency in the critical path:** longer timeouts and multiple attempts make the customer wait longer exactly when the system is already unstable.
- **Ambiguous business states:** orders become "pending manual review", "pre-approved", "authorized without fraud detection", or "waiting for reconciliation", creating operational paths that are rarely exercised.
- **Manual work and backed-up queues:** the fallback shifts load to another system or to people, accumulating an operational debt that must be paid after the incident.

Notice that the manual review queue does not remove the work; it only pushes that work into the future. If the incident lasts long enough, recovery stops being only "bring fraud detection back" and starts including a second question: who is going to pay the accumulated debt without bringing down the rest of the system?

These problems illustrate how bimodal behaviors can, paradoxically, reduce a system's resilience instead of increasing it.

The anti-pattern appears when the code tries to "save" the purchase by doing a different kind of work:

```python
# ❌ Bimodal checkout: under failure, changes the business decision
def complete_order(order):
    try:
        fraud.approve(order)
    except FraudServiceUnavailable:
        order.status = "pending_manual_review"
        review_queue.send(order)
        return "order_received"  # looks successful, but became another flow

    payment.authorize(order)
    return "order_approved"

# ✅ Explicit and uniform failure: does not create a hidden mode
def complete_order(order):
    try:
        fraud.approve(order)
    except FraudServiceUnavailable:
        raise CheckoutTemporarilyUnavailable()

    payment.authorize(order)
    return "order_approved"

```

In the first case, the system keeps accepting work it cannot complete through the normal path. In the second, it preserves the semantics of the flow: either the order goes through the known path, or it fails explicitly and in a controlled way.

### Immediate shard redistribution after failure

Another pattern that appears in distributed systems is recovery that competes with real traffic. Imagine a cluster partitioned by shards. In normal operation, each node serves its share of traffic and keeps its state warm in memory. When one node fails, the others do not just receive more requests; they also start copying partitions, recalculating ownership, rebuilding indexes, and warming local caches to absorb what was lost.

Notice the inversion: the failure reduced available capacity and, at the same time, triggered extra recovery work. The system tries to heal itself by doing more CPU, more network, more I/O, and more memory allocation exactly when it has less room to spare. The expected result was graceful degradation; the real result can be a second wave of failures, now caused by the recovery mechanism itself.

![Immediate redistribution after failure: when one node fails, the remaining nodes receive more traffic and still execute shard copy, index rebuild, and state warm-up; recovery starts competing with real traffic and can trigger a second wave of failures](/blog/2026-07-29-how-the-constant-work-principle-increases-application-resilience/constant-work/en/image-09.png)

This kind of incident highlights the central lesson of this post: the recovery mode was triggered at exactly the worst possible moment — under maximum stress — and, instead of protecting the system, became the very vector of the widespread failure.

### The fallback that deletes the customer's work

The danger of an emergency mode is not just overloading another component — it's making destructive decisions based on incomplete information. Imagine a shopping cart service that, upon receiving errors from an overloaded catalog service, interpreted "I couldn't look up this product" as "this product no longer exists" and removed items from customers' carts en masse. The fallback did *different* work from the normal path, and that different work was actively harmful. The lesson is harsh: a fallback mode must never treat "failed to obtain the data" as "data absent" — the absence of a response is not a response.

In code, the bug lives in the confusion between "I couldn't find out" and "I know it doesn't exist":

```python
# ❌ Destructive fallback: treats an error as "doesn't exist" and ACTS on it
def sync_cart(item_id):
    try:
        offer = catalog.get_offer(item_id)   # overloaded service
    except ServiceUnavailable:
        offer = None                          # BUG: failure becomes "no offer"
    if offer is None:
        cart.remove(item_id)   # destructive decision based on uncertainty
        # during a catalog incident, MILLIONS of items vanish from carts

# ✅ Preserve the work: when in doubt, don't act destructively
def sync_cart(item_id):
    try:
        offer = catalog.get_offer(item_id)
    except ServiceUnavailable:
        # "couldn't look up" ≠ "doesn't exist": leave the item as is
        return  # no destructive action while there is uncertainty
    if offer is None:      # only act when the answer is a CERTAINTY
        cart.remove(item_id)

```

The difference is between a system that, in doubt, destroys the customer's work, and one that, in doubt, preserves it.

### The recovery that brings it all down again

A detail almost nobody considers: bimodality also strikes during *recovery*. After a failure, turning the system back on "all at once" can be as dangerous as the original failure. A service that had disabled a cache layer and then reactivated it instantly to 100% of traffic saw the cache unable to absorb the sudden surge: timeouts, retry queues doubling the work, and availability dropping again. Recovery is also a mode — and the transition back to normal needs to be gradual (a ramp of 20% at a time, for example), otherwise the very act of recovering becomes a second incident.

![Instant versus gradual recovery: turning 100% of the traffic back on at once pushes the load beyond the capacity of the just-recovered component and causes a second collapse; with a gradual 20%-at-a-time ramp, traffic stays below capacity and recovery completes without incident](/blog/2026-07-29-how-the-constant-work-principle-increases-application-resilience/constant-work/en/image-02.png)

### The batch retry that multiplies the load

Retrying is healthy for transient and isolated failures — but retrying *entire batches* when only part of them failed turns a hiccup into an avalanche. Consider a client that resends a complete batch of requests on every partial failure: under stress, it can generate dozens of times the normal traffic against already-degraded components, preventing the very recovery it needs. The failure mode (aggressively retrying everything) has a radically different load profile from the normal mode — classic bimodality. The fix is granular retry (only what actually failed), with exponential backoff and a rate cap.

![Retrying the whole batch versus granular retry: when a single item in a batch of 100 fails, resending the complete batch repeatedly generates up to 20 times the load on an already-degraded component and prevents recovery; resending only the item that failed, with backoff and a rate cap, keeps the load nearly constant](/blog/2026-07-29-how-the-constant-work-principle-increases-application-resilience/constant-work/en/image-03.png)

The contrast shows up directly in the client's code:

```python
# ❌ Retry the whole batch: 1 failure resends all 100
def process_batch(items):
    while True:
        result = service.send(items)          # resends EVERYTHING
        if result.ok:
            return
        time.sleep(1)   # on partial failure, hammers the service with the full batch

# ✅ Granular retry: resend only what failed, with backoff + cap
def process_batch(items):
    pending = list(items)
    wait = 0.1
    while pending:
        failures = service.send(pending).failed_items   # only the ones that failed
        if not failures:
            return
        pending = failures
        time.sleep(min(wait, CAP))   # exponential backoff with a cap
        wait *= 2

```

In the first case, the load under failure is a multiple of the normal load. In the second, it tends to shrink each cycle — the opposite of an avalanche.

## Why do Fallbacks fail?

Think of the following metaphor. A bank bought an emergency generator and, every month, briefly started it up to "test" it — the engine caught, and everyone felt reassured. On the day the power actually went out, the generator started and, fifteen minutes later, died: the monthly test had never exercised it *under real, sustained load*. It's the perfect metaphor for fallback: a path that seems to work in superficial tests, but that is only truly called upon at the worst moment — and fails exactly there. If an emergency mode doesn't run continuously under real conditions, you don't have a contingency plan; you have an assumption.

### Latency and load overlap

One of the biggest problems with fallbacks is that they can introduce additional latency. When a system switches to a fallback mode, it generally does so in response to a failure or an overload condition. However, this transition can add extra processing time, especially if the fallback involves queries to slower components, such as a database instead of a cache.

Furthermore, as discussed earlier, overload on a fallback component can cause a cascading failure. Instead of solving the problem, the fallback can amplify it, resulting in a broader and more severe outage.

### Rare and poorly tested operating modes

Fallbacks are, by definition, operating modes that are not used regularly. This means they are less tested, both in development environments and in production. When they are activated, unforeseen failures are more likely to occur, which can lead to unexpected behaviors.

In a distributed system, where different components may be spread across various geographic regions and infrastructures, the complexity of testing and validating all fallback scenarios is enormous. Even with comprehensive testing, there is always the risk of unpredictable scenarios occurring in production.

### Cascading failures

One of the most critical failures associated with fallbacks is the cascading failure. This occurs when the fallback component is overloaded by an unexpected flow of traffic or subsequent failures, causing a series of failures that propagate throughout the entire system.

A classic example would be immediate partition redistribution after the loss of a node. The remaining nodes need to serve more traffic and, at the same time, copy state, rebuild indexes, and warm memory. If they do not have enough room to absorb both kinds of work together, recovery can bring down the very nodes that were supposed to stabilize the system.

### Implementation complexity

Implementing fallbacks often requires adding extra logic to the system, which increases code complexity and makes the system harder to maintain and evolve. This additional complexity can introduce new bugs and make the system more prone to failures.

Furthermore, the need to continuously maintain and test fallback modes adds significant operational burden, increasing costs and the difficulty of managing the system.

## There is another way to think: The Constant Work Principle

The [**constant work**](https://docs.aws.amazon.com/wellarchitected/latest/framework/rel_prevent_interaction_failure_constant_work.html) principle is based on the idea that a system should perform the same amount of work regardless of load or operating conditions. Instead of switching between modes, the system operates uniformly, performing consistent operations in all situations. This eliminates the need for fallback modes and reduces the complexity and risks associated with bimodal behaviors.

### Consistent operation

By adopting the constant work principle, a system ensures that the same amount of work is performed regardless of the number of requests or the health of the system's components. This means the system does not need to change its behavior in response to adverse conditions, making it more predictable and easier to manage.

Route 53's health check system was designed to always perform the same amount of work, regardless of the state of the targets or the number of active configurations. Health checkers constantly send aggregators a fixed-size set of results (the maximum supported), filling it with dummy entries when there are few configured health checks. This eliminates any network or processing load variation as customers add new targets. Similarly, aggregators periodically send a fixed-size table to DNS servers, which store it in memory — always the same volume of data, always the same operation.

**The most powerful result of this design is that mass failures do not change the system's behavior.** Even if an entire Availability Zone loses power and thousands of health checks fail simultaneously, health checkers, aggregators, and DNS servers keep doing exactly the same work they were already doing. When DNS servers receive a query, they always check *all* possible answers against the in-memory table — there is no mode change, no explosion of DNS updates, no increase in processing time. The code always executes the same actions; the only difference is *which* answers are selected as the result. This constant work pattern makes the system extremely reliable precisely in the most critical moments.

### Reduced variability

One of the biggest challenges in distributed systems is dealing with variability in load and operating conditions. Variability can cause performance fluctuations and increase the probability of failures. By operating in a constant manner, a system eliminates variability, maintaining stable and predictable performance.

In the case of Route 53, even if hundreds or thousands of endpoints fail simultaneously, the system performs the same work, without increasing processing time or overloading resources. This eliminates the possibility of a cascading failure, where one component fails and overloads others, leading to a system collapse.

### Anti-fragility

Systems based on constant work can become anti-fragile, meaning they can improve their performance under stress conditions. For example, in a system where less work is needed under high load (like the reduction of load in coffee urns as coffee is served), the system can become more efficient during moments of greater stress.

Another example of constant work is found in AWS Hyperplane, the system behind critical components like Network Load Balancers. When a customer makes a change to a load balancer, Hyperplane processes those changes by storing the configurations in files on Amazon S3.

These files are then loaded and applied periodically by all Hyperplane nodes, regardless of whether there are changes or not. This means the system is always processing the maximum amount of configurations, regardless of the actual number of changes. This approach avoids load spikes and ensures the system keeps operating efficiently and predictably, even under extreme conditions.

Furthermore, because Hyperplane is highly redundant, the workload is distributed uniformly across the nodes. If a node fails, the overall workload decreases, rather than increasing, ensuring the system keeps working without interruptions.

### Simple to implement and maintain

One of the biggest advantages of constant work is simplicity. Because the system operates the same way in all conditions, the logic is simpler and more direct. This not only makes the system easier to implement, but also reduces the need for complex testing and the probability of introducing new bugs.

Furthermore, the simplicity makes the system easier to maintain in the long term. Engineering teams can quickly understand how the system operates, and new features can be added without the risk of breaking existing logic.

## Constant work produces static stability

It's worth naming a concept that goes hand in hand with constant work: [**static stability**](https://www.robissonoliveira.com.br/blog/2024-08-05-how-static-stability-increases-application-resilience/). A system is statically stable when it keeps operating correctly during a failure **without changing its state** — without making new decisions, without triggering recovery paths, without depending on a control plane to "fix" things in the heat of the moment when its dependencies fail.

The relationship between the two is direct: **constant work is the mechanism that produces static stability.** Route 53 is statically stable precisely *because* it does constant work — when an Availability Zone goes down, it doesn't need to react, recalculate, or switch modes, because it was already checking all endpoints all the time. The loss of the AZ doesn't represent a new event to be handled; it just represents fewer positive responses within the same work that was already being done. That's why the two principles always appear together in AWS reliability literature: constant work is the *how*, static stability is the *result*.

![Comparison of load over time: in the reactive (bimodal) approach there is a load spike at the exact moment of failure, whereas in constant work the load stays steady, with no spikes, making the failure a non-event](/blog/2026-07-29-how-the-constant-work-principle-increases-application-resilience/constant-work/en/image-04.png)

## Failover over fallback

Here we need to separate two concepts that are often confused, and that confusion is precisely the root of many bimodal behaviors. **Fallback** is when the system, faced with a failure, starts doing *different* work — switches modes, changes the execution path, triggers logic that is normally dormant. **Failover** is when the system keeps doing *exactly the same work*, just on a healthy resource identical to the one that failed. The difference seems subtle, but it's what decides whether you're increasing or reducing resilience.

Fallback is bimodal by definition: it exists to create a second operating mode. Failover, when done well, is the opposite — it preserves uniformity. If you have three identical replicas of a service and one goes down, failover redirects traffic to the other two without changing the nature of the operation. No new code is triggered, no rare path is taken, no "emergency" logic that nobody has tested in six months comes into play. The work is the same; only *where* it happens changes.

The central point is this: **prefer failover for redundant, identical resources, and be suspicious of any fallback that changes the nature of the work.** A failover to a read-only replica of the database is safe because the replica does the same thing the primary did. But a fallback that, faced with a cache failure, starts hammering the database with the cache's access pattern is not redundancy — it's a second operating mode, with a different load profile, that nobody provisioned. That's why failover over fallback is almost always the more resilient choice: you eliminate the bimodal mode instead of creating it.

![Failover versus fallback: in failover, traffic is redirected to healthy identical replicas doing the same work (unimodal); in fallback, the system triggers a rare and different path, creating a second operating mode](/blog/2026-07-29-how-the-constant-work-principle-increases-application-resilience/constant-work/en/image-05.png)

See the difference in practice. First, the **bimodal fallback** — the dangerous pattern:

```python
# ❌ FALLBACK: on failure, changes the NATURE of the work
def get_product(product_id):
    try:
        return cache.get(product_id)          # normal mode
    except CacheUnavailable:
        # emergency mode: rare path, poorly tested,
        # with a completely different load profile.
        # If the whole cache went down, ALL calls land here
        # and the database — which never saw this volume — collapses.
        return database.query(product_id)      # fallback mode

```

Now **failover** — same work, healthy resource:

```python
# ✅ FAILOVER: on failure, the SAME work on another identical node
def get_product(product_id):
    # the list contains identical replicas of the SAME distributed cache.
    # switching nodes doesn't change the nature of the operation or the load profile.
    for node in healthy_cache_replicas():
        try:
            return node.get(product_id)
        except NodeUnavailable:
            continue  # try the next replica — identical work
    raise ServiceUnavailable()  # explicit failure, no hidden mode

```

In the first case, the cache failure *transforms* the system's behavior. In the second, it just changes **where** the same work is done — there is no second mode waiting to go wrong.

## How DynamoDB uses the constant work principle

We saw that the big problem with the cache is the bimodal behavior of the *cache miss*: while there are hits, the database is protected; when the cache empties, all the traffic slams the database at once. The right question is not "how do I make a better fallback to the database?", but "how do I make the cache stop being bimodal?".

The answer [DynamoDB adopts internally](https://www.youtube.com/watch?v=4GKXx9vIqsk&t=2400s) is to treat the cache as a structure that does **constant work**, and not as an opportunistic optimization. Instead of populating the cache on demand (which creates the fragile "hot when full, catastrophic when empty" pattern), you keep the relevant dataset — routing tables, partition metadata, membership information — always loaded and being updated at a fixed cadence, regardless of whether there is a request or not. The cache never "empties under load" because it doesn't depend on traffic to fill itself: it's updated all the time, at the same rhythm, whether the system is idle or at peak.

The practical effect is that **the cache miss stops being a load event**. There's no longer a moment when the database receives a flood of queries because the cache expired — the data is already there, always, because keeping it updated is the system's normal work, not a reaction to a failure. You pay the cost of keeping everything loaded all the time, but in exchange you eliminate the latency cliff and the cascading failure. It's the same philosophy as Route 53 checking all endpoints: always do the maximum work so that failure doesn't represent a jump in load.

In practice, the difference is in *who* feeds the cache and *when*. In the bimodal pattern, it's the user's traffic that populates the cache on demand; in the constant work pattern, a background process reloads the entire set at a fixed cadence:

```python
# ❌ Bimodal cache: populated on demand (lazy)
def get_route(key):
    value = cache.get(key)
    if value is None:              # cache miss → goes to the database
        value = database.get(key)  # on cache failure, EVERYONE lands here
        cache.set(key, value)
    return value

# ✅ Constant-work cache: always pre-loaded, fixed refresh
class ConstantCache:
    def __init__(self):
        self._data = {}
        threading.Thread(target=self._reload_forever, daemon=True).start()

    def _reload_forever(self):
        # CONSTANT work: reloads the entire dataset every 5s,
        # whether there's traffic or not. The cache never "empties under load".
        while True:
            self._data = database.load_full_table()  # fixed, bounded volume
            time.sleep(5)

    def get_route(self, key):
        return self._data.get(key)   # never goes to the database on the hot path

```

The cache miss stops existing as a load event: the data is always already there, and the failure never becomes a jump in traffic to the database.

There's also a subtler trap: using the cache *as a fallback* does not protect against the most common failure mode. Placing a cache "in front of" a service as a safety net gives a false sense of resilience. If the client queries the cache *before* the origin and the origin starts responding with **incorrect** data — not unavailable, but wrong — the cache simply memorizes and propagates the error (a *cache poisoning* effect). Cache-as-fallback only covers the case of total unavailability, which is usually the rarest failure mode; it does not protect against wrong responses, which are the most common mode. Before adopting a cache as protection, ask yourself *against which failure mode* it actually protects.

The fatal detail is usually the *order* of the lookup:

```python
# ❌ Cache queried BEFORE the origin: memorizes and propagates wrong responses
def resolve(key):
    if key in cache:
        return cache[key]          # if the origin already wrote garbage here, it propagates
    value = origin.get(key)        # origin may respond INCORRECTLY
    cache[key] = value             # cache poisoned with the error
    return value

# ✅ Cache as a fallback ONLY for unavailability, never for correctness
def resolve(key):
    try:
        value = origin.get(key)    # the origin is always the source of truth
        cache[key] = value         # cache only reflects valid responses
        return value
    except OriginUnavailable:
        return cache.get(key)      # fallback only when there is NO response

```

In the first case the cache protects against the rare mode (unavailability) but amplifies the common mode (wrong response). In the second, it covers only what a cache is really for.

## Configuration management

Configuration management is one of the places where bimodal behavior hides most easily — and the Hyperplane pattern we saw earlier is precisely the antidote, applied to a different domain. The natural temptation is to distribute configuration incrementally: when something changes, send *only the delta* to the nodes that need to know. It seems efficient, but it creates two operating modes — "no changes, zero configuration traffic" and "many changes at once, an avalanche of deltas" — and it's precisely in the second mode, during a big-change event, that the distribution mechanism collapses.

The constant work approach inverts the logic: **always distribute the full configuration, at a fixed cadence, regardless of whether there was a change.** Each node periodically downloads the entire snapshot — the complete list of routes, rules, endpoints — and applies everything, even if 99% is identical to what it already had. The volume of distribution work is constant and predictable: it's the same on a calm day and on a day when thousands of configurations changed at once.

This brings a silent additional benefit: **the configuration becomes self-correcting.** Because the full state is reapplied all the time, any divergence — a node that missed an update, a corrupted state, a partial deploy — is healed on the next cycle, without intervention. You trade the "efficiency" of sending only the delta for the robustness of a system that converges on its own to the correct state and never has a configuration-work spike.

![Configuration distribution: the incremental delta approach generates enormous spikes during big-change events, whereas the full snapshot at a fixed cadence keeps the work constant and predictable](/blog/2026-07-29-how-the-constant-work-principle-increases-application-resilience/constant-work/en/image-06.png)

In code, the contrast is clear in what each node does every cycle:

```python
# ❌ Incremental delta: bimodal — reacts to each change
def on_config_change(delta_event):
    # in a mass deploy, thousands of these events arrive at once.
    # the volume of work is unpredictable and explodes at the worst moment.
    apply_change(delta_event)

# ✅ Full snapshot at a fixed cadence: constant work
def configuration_loop():
    while True:
        snapshot = download_full_config()   # ALWAYS the entire state
        apply_config(snapshot)              # reapplies everything, even with no change
        # identical work on a calm day or a day of a thousand changes.
        # bonus: any divergence is self-corrected on the next cycle.
        time.sleep(10)

```

The work per cycle is always the same — and, because the full state is reapplied continuously, the system self-corrects without ever needing a special reconciliation path.

## Continuous backup and restore

Backup is the most classic example of *occasional* work — and, therefore, a factory of bimodal behavior. The traditional model runs a heavy job once a day or per week: for 23 hours the system does no backup at all, and for one hour it consumes I/O, CPU, and bandwidth intensely. If that peak coincides with a real load peak, the two add up and bring the system down. Worse: the *restore* is only exercised on disaster day — it's the ultimate fallback path, rarely tested, triggered at the worst possible moment.

The constant work alternative is to turn backup and restore into a continuous flow instead of events. Instead of a periodic monolithic snapshot, the system does **incremental, continuous backup** — capturing changes as they happen (for example, via streaming of the transaction log). The backup work stops having peaks: it's a thin, steady stream, always at the same rhythm, spread over time instead of concentrated in a window. It's what DynamoDB itself offers with *point-in-time recovery*: there's no "backup time", because the backup is all the time.

And the restore? The same logic applies: the more frequently you exercise restoration — ideally continuously and automatically, validating backups all the time — the less it is a rare and scary mode. **A restore that runs every day as part of normal operation is not a fallback; it's constant work.** You find out the backup is corrupted in a Tuesday test, not during the fire.

![Periodic versus continuous backup: the periodic job concentrates heavy I/O spikes in specific windows, whereas continuous streaming backup spreads the work into a thin, steady stream over time](/blog/2026-07-29-how-the-constant-work-principle-increases-application-resilience/constant-work/en/image-07.png)

## Queues, backlogs, and constant work

[Queues deserve their own discussion](https://builder.aws.com/content/3EuRcgkTP1MI0c7zM8W6HL3WIqA/avoiding-insurmountable-queue-backlogs) because they are one of the most useful and, at the same time, most deceptive abstractions in distributed systems. A queue increases durability: if the consumer fails, the message is still there. It decouples producer and consumer. It smooths short spikes. All of that is true. But one sentence needs to stay in your head: **a queue does not eliminate work; a queue moves work through time.**

When the system is healthy, that shift looks harmless. The producer publishes, the consumer processes, the queue stays small, and end-to-end latency remains low. But when the consumer slows down, a dependency starts failing, or the arrival rate exceeds the processing rate, the queue changes nature. It stops being a buffer and becomes an operational debt growing quietly.

The bimodal behavior appears during recovery. After one hour of trouble, the system comes back and tries to "catch up": increase concurrency, launch more workers, reduce delays, retry old messages, and drain the backlog as fast as possible. That sounds like the right thing to do, but it can be exactly the second incident. The database, downstream API, payment service, storage, or any dependency involved in processing starts receiving a load it would never receive in normal mode.

![Queue with bimodal backlog: producers feed the queue, the queue accumulates more than 100 thousand messages, consumers scale and retry, dependencies saturate, and retries feed the backlog again; the chart shows executed work spiking during recovery](/blog/2026-07-29-how-the-constant-work-principle-increases-application-resilience/constant-work/en/image-11.png)

*Constant work* applied to queues does not mean processing everything all the time, because the volume of messages can grow without bound. This is an important point: queues usually live in the data plane, and data planes do not have the same predictable ceiling as a set of health checks or a configuration table. So the question changes. It is not "how do I always do the maximum work?", but: **how do I keep the executed work rate constant and safe, even when the queue grows?**

The anti-pattern is letting backlog size directly control how much work the system performs:

```python
# ❌ Bimodal draining: backlog decides how aggressive the system becomes
def consume_queue():
    while True:
        backlog = queue.size()

        if backlog > 100_000:
            concurrency = 2_000       # changes mode under stress
            retry_delay = 0           # immediate retries
        else:
            concurrency = 100
            retry_delay = 1

        messages = queue.receive(max_messages=concurrency)
        for message in messages:
            try:
                process(message)      # calls database, APIs, and dependencies
                queue.delete(message)
            except Exception:
                queue.requeue(message, delay=retry_delay)

```

This code looks elastic, but it is bimodal. In normal mode, the system consumes at a comfortable rate. Under backlog, it changes its own personality: more concurrency, more aggressive retries, more pressure on dependencies, and a higher chance of turning recovery into an avalanche. The backlog starts driving the system.

A more resilient design does the opposite: it keeps a safe consumption rate, isolates workloads, treats old messages as a different class of work, and applies backpressure when dependencies are degraded.

![Queue with constant work: producers feed a queue that considers message priority and age, old messages go to backlog, TTL, or DLQ, processing goes through per-workload rate limits, consumers work at a safe rate, and dependencies receive predictable load; the chart shows executed work staying controlled](/blog/2026-07-29-how-the-constant-work-principle-increases-application-resilience/constant-work/en/image-12.png)

```python
# ✅ Constant work: safe rate, isolation, and controlled debt
def consume_queue():
    global_limit = RateLimiter(500)          # known ceiling per second
    per_customer_limit = KeyedRateLimiter(50)

    while True:
        message = queue.receive()
        customer = message.customer_id

        if message.age > timedelta(minutes=15):
            backlog_queue.send(message)      # debt separated from real time
            queue.delete(message)
            continue

        if not dependencies_healthy():
            queue.requeue(message, delay=with_jitter(30))
            continue

        if not global_limit.allow() or not per_customer_limit.allow(customer):
            queue.requeue(message, delay=with_jitter(5))
            continue

        try:
            process(message)
            queue.delete(message)
        except TransientError:
            queue.requeue(message, delay=backoff_with_jitter(message.attempts))
        except PermanentError:
            dlq.send(message)
            queue.delete(message)

```

Notice the difference: the queue may be enormous, but the system does not enter panic mode. The work executed per second stays inside a known envelope. Old messages do not compete with the hot path. A noisy customer does not consume everyone else's capacity. Slow dependencies do not receive a flood exactly while they are trying to recover. This does not make backlog irrelevant, but it turns backlog into something manageable.

In real-time systems, this often leads to a decision that feels counterintuitive: **new messages may be more valuable than old messages**. If a message has been waiting too long, maybe it should go to a backlog queue, expire through TTL, or be replaced by a later full synchronization. A "product updated" event from two hours ago may no longer be worth anything if a periodic sweep reconciles the whole state. Again, resilience is not doing all work at any cost; it is preserving the usefulness of the system under failure.

The lesson is simple and hard: if recovering a queue requires the system to work ten times harder than it normally does, you do not have constant work. You have a time bomb of delayed work. The resilient design is the one where backlog, retries, and draining do not change the nature of the system. The queue may grow. The debt may exist. But the pace at which you pay that debt must be safe, predictable, and compatible with the dependencies that sustain processing.

## Uniformity of APIs and logs

Finally, a place where bimodal behavior sneaks in almost without anyone noticing: the size and shape of what your application produces under failure. At the beginning of the article I mentioned the case of logs that jump from 1kb in normal mode to 10kb when something goes wrong. This is a treacherous bimodality, because it *increases the load exactly when the system is most fragile*. At the instant everything starts to fail — and you most need available I/O — the application decides to dump ten times more log, competing for disk and network at precisely the wrong time, turning a manageable incident into a collapse.

The same goes for APIs: a response that normally carries a lean payload, but that under error starts including huge stack traces, diagnostic objects, and embedded retries, changes the bandwidth and processing profile at the worst moment. It's the same fallback trap, just at the protocol layer.

The constant work principle applied here is to seek **uniformity of work per request, regardless of the outcome.** Error logs and success logs should have similar orders of magnitude — if you need more detail to debug, use sampling or on-demand adjustable log levels, instead of an automatic jump in volume at the worst instant. Error responses should have a predictable size, comparable to success responses. The goal is that **failure should not be more expensive, in resources, than success** — because the moment failure costs more, it becomes fuel for the cascade.

![Uniformity of work per request: in the bimodal mode the error log (10kb) is ten times larger than the success log (1kb), consuming I/O just when the system is fragile; in constant work the error log is comparable in size to the success log](/blog/2026-07-29-how-the-constant-work-principle-increases-application-resilience/constant-work/en/image-08.png)

The anti-pattern usually hides in an innocent detail of error handling:

```python
# ❌ Bimodal log: failure costs 10× more I/O than success
def handle_request(req):
    try:
        result = process(req)
        log.info("ok")                      # ~1kb
        return result
    except Exception as e:
        # during an incident, MILLIONS of these giant lines compete
        # for disk and network — exactly when I/O is scarce.
        log.error(f"failure: {traceback.format_exc()} "
                  f"context={full_state_dump(req)}")  # ~10kb
        raise

# ✅ Uniform log: error and success have similar orders of magnitude
def handle_request(req):
    try:
        result = process(req)
        log.info("ok id=%s", req.id)         # ~1kb
        return result
    except Exception as e:
        # lean, predictable-size line; the detail sits
        # behind sampling, actionable only when needed.
        log.error("failure id=%s type=%s", req.id, type(e).__name__)  # ~1kb
        if sample(rate=0.01):                # 1% with full stack
            log.debug("trace id=%s %s", req.id, traceback.format_exc())
        raise

```

The goal is simple: failure cannot cost more resources than success, otherwise the error handling itself becomes the trigger of the collapse.

## When Constant Work is Not the Answer

In our field, there is no absolute right and wrong; everything "depends" and has trade-offs. It would be dishonest to sell constant work as a silver bullet — it has a real cost, and acknowledging it is what separates a mature engineering decision from a blind fad.

### You pay for the worst case all the time

The essence of constant work is to size for the maximum and always operate at that level, even when the real load is low. This means deliberate waste of resources: CPU, memory, bandwidth, and ultimately money. In a system with very low or very sporadic load, keeping the engine always floored may not be economically justified.

### It assumes a bounded, known volume of work

The pattern works beautifully when the "total work" has a predictable ceiling: a finite number of health checks, a configuration table that fits in memory, a set of routes that doesn't grow without limit. When the volume of data to be processed can grow unbounded or has very high cardinality, "always doing the maximum work" stops being constant and simply becomes expensive and unfeasible — you're not going to pre-load a terabyte dataset "just in case".

### How to decide then?

Apply constant work where the work is bounded, the worst-case cost is acceptable, and predictability under failure is worth more than resource savings under normal conditions — typically in control planes, routing, health checking, and configuration distribution. For data planes with unbounded volume, such as event queues, prefer other tools (backpressure, cell-based isolation, per-workload limits, TTL, DLQ, and degradation that preserves uniformity). In those cases, constant work is not "process everything all the time"; it is keeping a safe and predictable processing rate. The goal is never "do constant work at any cost" — it's to **eliminate bimodality**, and constant work is one of the ways to do that, not the only one.

### Fallback vs. constant work

| Dimension | Fallback (bimodal) | Constant Work (unimodal) |
| --- | --- | --- |
| **Operating modes** | Two: normal and emergency | Just one, always the same |
| **Behavior under failure** | Changes path, triggers rare logic | Changes nothing — same work |
| **Load at the moment of failure** | Spikes (peak right when the system is fragile) | Constant, no spikes |
| **Testability** | Poor: rare mode, poorly exercised | Excellent: the only mode runs all the time |
| **Cascading-failure risk** | High: the fallback becomes the vector of collapse | Low: no transition, no amplification |
| **Code complexity** | Higher: extra detection and switching logic | Lower: one path, predictable |
| **Resource cost under normal conditions** | Lower: spends only what's needed | Higher: pays for the worst case always |
| **Best application** | Avoid when possible; ok if it preserves uniformity | Control planes, routing, config, health check |

## Conclusion

If you take a single idea from this post, let it be this: **design systems that do the same amount of work all the time — including when they fail.** The intuition of creating an "emergency mode" to handle the worst case is seductive, but it's precisely what introduces bimodal behavior: a second mode, rare, poorly tested, that is triggered at the moment of greatest stress and that amplifies the failure instead of containing it. Fallbacks, most of the time, hurt more than they help.

*Constant work* inverts the logic. Instead of reacting to failure by changing behavior, the system never changes behavior — it's already always doing the maximum work. There's no transition to go wrong, no hidden mode, no load spike at the wrong time. Route 53 checking all endpoints all the time, Hyperplane reapplying the entire configuration every cycle, a cache that updates itself regardless of traffic: they all answer that question from the beginning — when something fails, they do *the same thing as always*. And that's why they hold up.

This isn't free, and I don't want to sell you a silver bullet: you pay for the worst case all the time, and not every problem fits this mold. But for the critical paths of your system — routing, health checking, configuration distribution, control, transactional systems, or mission-critical systems — trading reactive cleverness for constant predictability is one of the architecture decisions that most increases resilience with the least complexity.

In the end, this does not mean you should never use fallbacks, caches, or the approaches mentioned here, including the ones highlighted as risky. It means you should understand their risks and accept them consciously.

## References

- [https://medium.com/@linoyzaga/scaling-applications-with-constant-work-pattern-f253176b3146](https://medium.com/@linoyzaga/scaling-applications-with-constant-work-pattern-f253176b3146)
- [https://aws.amazon.com/blogs/architecture/doing-constant-work-to-avoid-failures/](https://aws.amazon.com/blogs/architecture/doing-constant-work-to-avoid-failures/)
- [https://news.ycombinator.com/item?id=34103426](https://news.ycombinator.com/item?id=34103426)
- [https://docs.aws.amazon.com/wellarchitected/latest/reliability-pillar/rel_prevent_interaction_failure_constant_work.html](https://docs.aws.amazon.com/wellarchitected/latest/reliability-pillar/rel_prevent_interaction_failure_constant_work.html)
- [https://docs.aws.amazon.com/wellarchitected/latest/reliability-pillar/rel_withstand_component_failures_static_stability.html](https://docs.aws.amazon.com/wellarchitected/latest/reliability-pillar/rel_withstand_component_failures_static_stability.html)
- [https://aws.amazon.com/builders-library/challenges-with-distributed-systems/](https://aws.amazon.com/builders-library/challenges-with-distributed-systems/)
- [https://a-nickels-worth.dev/posts/modesharm/](https://a-nickels-worth.dev/posts/modesharm/)
- [https://aws.amazon.com/builders-library/minimizing-correlated-failures-in-distributed-systems/](https://aws.amazon.com/builders-library/minimizing-correlated-failures-in-distributed-systems/)
- [https://aws.amazon.com/builders-library/avoiding-overload-in-distributed-systems-by-putting-the-smaller-service-in-control/](https://aws.amazon.com/builders-library/avoiding-overload-in-distributed-systems-by-putting-the-smaller-service-in-control/)
- [https://aws.amazon.com/builders-library/reliability-and-constant-work/?did=ba_card&trk=ba_card](https://aws.amazon.com/builders-library/reliability-and-constant-work/?did=ba_card&trk=ba_card)
- [https://aws.amazon.com/builders-library/avoiding-insurmountable-queue-backlogs/](https://aws.amazon.com/builders-library/avoiding-insurmountable-queue-backlogs/)
- [https://www.youtube.com/watch?v=4GKXx9vIqsk&t=646s](https://www.youtube.com/watch?v=4GKXx9vIqsk&t=646s)
- [https://builder.aws.com/content/3EuRcgkTP1MI0c7zM8W6HL3WIqA/avoiding-insurmountable-queue-backlogs](https://builder.aws.com/content/3EuRcgkTP1MI0c7zM8W6HL3WIqA/avoiding-insurmountable-queue-backlogs)
