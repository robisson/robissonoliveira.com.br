---
title: "Building a high-throughput ledger on Amazon DynamoDB: From requirements to 23,000 TPS"
description: "A Go ledger on Amazon DynamoDB: microbatching, exactly-once effects, recovery, and balance slices, measured up to 23,217 TPS on one account."
pubDate: 2026-07-10
tags: [Amazon DynamoDB, Distributed Systems, Go, Architecture, Performance]
language: en
---

*AWS DATABASE BLOG  ·  DRAFT FOR REVIEW*

*by **Robisson Oliveira**  \|  on **10/07/2026**  \|  in Amazon DynamoDB, Advanced (300), Technical How-to*

A ledger has one item that every transaction on an account must change: the account's balance. In [Amazon DynamoDB](https://aws.amazon.com/dynamodb/), a single item lives on a single partition, and a partition serves up to [1,000 write units per second](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/burst-adaptive-capacity.html). Most accounts never come near that. A hot account does, and quickly. A marketplace's collection account, a large merchant, or the account that funds a digital wallet can receive thousands of transactions per second, and every one of them is aimed at the same item.

However, the number of transactions an account processes and the number of times its balance item is written don't have to be the same number. In our tests, one account processed 23,217 transactions per second (TPS) while its balance item took 3.9 writes per second, on one [AWS Fargate](https://aws.amazon.com/fargate/) task with 4 vCPU. Every transaction was durable in DynamoDB before its caller received a response, and every run was checked against an expectation computed without asking the service.

In this post, we show how we built that ledger in Go, starting from its requirements and invariants and adding one design decision at a time, each one forced by the limit the previous one exposed. The code excerpts come from the implementation, every number comes from a benchmark report, and the experiments that proved us wrong are included, because several of them taught us more than the ones that worked.

## Requirements and invariants

The service has two operations that matter: post a transaction, and read a balance. A transaction carries a client-generated transaction_id, an account_id, an integer amount in minor units, and a type, CREDIT or DEBIT. The response reports the decision, the balance before and after, and the transaction's sequence number in the account's history.

The following request credits 15.00 (1500 minor units) to an account:

```bash
curl -s -X POST http://<ledger-endpoint>/v1/transactions \
  -H 'Content-Type: application/json' \
  -d '{"transaction_id":"tx-1","account_id":"acct-1","amount":1500,"type":"CREDIT"}'
```

You should see output similar to the following:

```json
{"transaction_id":"tx-1","account_id":"acct-1","sequence":1,
 "balance_before":0,"balance_after":1500,
 "batch_id":"d1788c2c-5060-4d41-9bc8-7072b363df59",
 "status":"APPLIED","duplicate":false,"latency_ms":104}
```

A client that resends the same transaction_id gets the original decision back with "duplicate": true. A debit the account can't cover returns HTTP 422, and it returns 422 again on every retry.

We wrote the invariants down before writing any code and put them in priority order. Then we agreed on the rule the rest of the project followed: a change that raises throughput and weakens an invariant isn't an improvement, however good the benchmark looks. Table 1 lists the invariants.

| **Invariant** | **What it means** | **How it's enforced** |
| --- | --- | --- |
| Non-negative balance | A debit applies only if the balance after it is zero or more | A sequential fold in memory, and a condition on the write |
| Exactly one financial effect per transaction_id | Retries, redeliveries, and crashes never move money twice | Idempotency records, fencing, and a strict recovery order |
| Durable before confirmed | A 200 response means the write is in DynamoDB | The caller waits for TransactWriteItems to return |
| One authoritative balance | The balance is stored, never summed from history | One balance item per account, or one set of slices |
| Integer money | Minor units, checked arithmetic, no floating point | A dedicated money.Minor type |
| No lost or doubled update | Two writers can't both win against the same state | A version and an owner epoch in one condition expression |
| Terminal rejections | A refused debit stays refused when it's retried | Rejections are recorded exactly like applications |

<p class="ledger-caption">Table 1: The ledger's invariants, in priority order</p>

Two rows are less obvious than they look. The authoritative-balance row rules out deriving the balance from history, which event-sourced designs often do. Here, history is a record of what happened, and a debit is only ever checked against the balance item. The terminal-rejection row exists because without it, a debit refused at 10:00 could succeed at 10:05 after a credit arrived, and the outcome of a transaction ID would depend on when the client happened to retry.

Performance targets came second. We set the throughput target per account rather than per table, because spreading load across many accounts is exactly what DynamoDB already does well, and one hot account is where ledger designs break. For authorization, the target was a P99 latency under 100 ms on a single account, with the full durable path in place.

## Access patterns

In DynamoDB, the access patterns decide the data model. Table 2 lists all of the ledger's patterns with their frequency, because frequency decides which ones can afford to be expensive.

| **#** | **Access pattern** | **Frequency** | **Operation** |
| --- | --- | --- | --- |
| A1 | Read the authoritative balance | At activation and after a conflict | Strongly consistent GetItem |
| A2 | Apply a batch: move the balance, store every outcome, leave a recovery marker | Once per batch | TransactWriteItems |
| A3 | Check whether a transaction_id was already decided | Once per transaction | Strongly consistent BatchGetItem, 100 keys per call |
| A4 | Load a batch's outcome after a timeout or a crash | Rare | Query on the batch's partition |
| A5 | Write a batch's history | Once per batch | BatchWriteItem |
| A6 | Write the durable idempotency records | Once per transaction | BatchWriteItem |
| A7 | Find batches whose history was never finished | Once per sweep interval | Query on a sparse GSI |
| A8 | Read an account's history over a sequence range | Client read | Query per history shard, in parallel |
| A9 | Take ownership of an account and fence the previous owner | Once per activation | Conditional UpdateItem |

<p class="ledger-caption">Table 2: Access patterns and how often each one runs</p>

Only two of the nine scale with the transaction rate: the idempotency lookup (A3) and the idempotency record (A6). Everything else scales with the batch rate. That split shaped the whole model. Work done once per batch can afford a transaction, a condition expression, and several items, because a busy account's batch carries hundreds or thousands of transactions. Work done once per transaction has to spread across partitions and stay off the path the caller waits on whenever it can.

The balance read (A1) is rarer than you might expect. The account's single writer keeps the balance it last committed in memory and uses it as the opening balance of the next batch. The commit's condition expression checks the version the batch was computed against, so a stale copy can be wrong, but it can never be believed.

None of the patterns uses a Scan. Recovery is the obvious candidate for one, and it uses a sparse index instead.

## Why a hot account is a write-rate problem

The straightforward design turns each transaction into its own DynamoDB transaction: one TransactWriteItems call that updates the balance with a condition, writes a history row, and writes an idempotency record. It's correct. It also writes the balance item once per transaction, so the balance item's write rate is the account's transaction rate.

DynamoDB performs [two underlying writes for every item in a transaction](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/transactions.html), one to prepare and one to commit, so a transactional update of an item up to 1 KB consumes 2 write units. [Adaptive capacity](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/burst-adaptive-capacity.html) can isolate a heavily used item on a partition of its own, and that partition still tops out at 1,000 write units per second. One balance item can therefore absorb at most about 500 transactional updates per second. That's arithmetic from documented limits rather than a measurement, and it's well over an order of magnitude below the rate we wanted per account.

The standard answer to a hot key is [write sharding](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/data-modeling-blocks.html): split the value into N items with suffixed keys, spread the writes, and add the items up on read. For a vote counter, it's the right tool. For a balance, it swaps a hot partition for a harder problem. Reading a consistent total is solvable, because TransactGetItems reads all N items in one serializable operation. What doesn't survive is the non-negativity check. With one item, "this debit fits" is a condition expression that DynamoDB evaluates atomically with the write. With N items, it's a computation over a set, and no condition expression spans a set of items.

So we kept one authoritative balance item per account and went after the write rate instead. Figure 1 shows the two shapes side by side. (Near the end of this post, we do split an account's funds across items, but in a form that keeps a condition on every write and moves money between items inside one transaction, and only as an opt-in path.)

<a href="/blog/high-throughput-ledger/image1.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image1.png" alt="Figure 1: The balance item&#x27;s write rate with one write per transaction and with one write per batch" width="3200" height="1120" loading="lazy" /></a>

<p class="ledger-caption">Figure 1: The balance item's write rate with one write per transaction and with one write per batch</p>

## Solution overview

The ledger is a Go service that runs as one [Amazon Elastic Container Service (Amazon ECS)](https://aws.amazon.com/ecs/) task on AWS Fargate with Graviton (4 vCPU and 16 GiB), behind an internal Network Load Balancer, with one DynamoDB table and one global secondary index (GSI). Figure 2 shows the path of a transaction through it.

<a href="/blog/high-throughput-ledger/image2.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image2.png" alt="Figure 2: The synchronous path ends at one TransactWriteItems call per batch, and everything after it is asynchronous and recoverable" width="3880" height="1780" loading="lazy" /></a>

<p class="ledger-caption">Figure 2: The synchronous path ends at one TransactWriteItems call per batch, and everything after it is asynchronous and recoverable</p>

The caller's response waits for exactly one DynamoDB write: the TransactWriteItems call that commits the caller's whole batch. Everything after it (the history, the durable idempotency records, and marking the batch complete) happens asynchronously. It's recoverable because the commit leaves a durable marker behind in the same transaction.

The code that decides whether money moves is a pure Go function with no dependency on DynamoDB, HTTP, or the clock. The DynamoDB access sits in adapters behind interfaces, written with the [AWS SDK for Go v2](https://docs.aws.amazon.com/sdk-for-go/v2/developer-guide/welcome.html). The excerpts in this post come from that code, with logging, metrics, and some error handling removed.

## Step 1: Decouple the transaction rate from the write rate

Microbatching collapses a window of an account's transactions into one conditional write. Each account gets a batcher with a bounded queue. The first transaction to arrive opens a window, which closes after BATCH_WINDOW or when the batch reaches MAX_BATCH_SIZE, whichever comes first. The executor folds the whole window against the balance in memory, commits the result with one TransactWriteItems call, and only then answers every caller in the window. The balance item's write rate becomes the commit rate, and the commit rate no longer depends on how many transactions arrive.

The following simplified excerpt shows the collection loop, with shutdown handling and the idempotency prefetch removed:

```go
for {
    // Block until there is at least one transaction: an idle account
    // must not spin on a timer.
    first := <-b.queue
    pending := append(collected[:0], first)
 
    // The window starts at the first arrival, so the oldest caller in a
    // batch waits at most one window.
    timer := time.NewTimer(b.settings.Window)
    reason := CloseReasonWindow
 
collect:
    for len(pending) < b.settings.MaxSize {
        select {
        case req := <-b.queue:
            pending = append(pending, req)
        case <-timer.C:
            break collect
        }
    }
    timer.Stop()
    if len(pending) >= b.settings.MaxSize {
        reason = CloseReasonSize
    }
    // A right-sized copy, because the batch outlives this iteration.
    b.dispatchBounded(ctx, detach(pending), reason)
}
```

When an account's queue is full, a new transaction gets HTTP 429 instead of waiting, which is the service's backpressure.

Figure 3 follows one window of three transactions through the batcher and the fold. The third transaction is rejected because the fold evaluates each transaction against the running balance, not against the opening one.

<a href="/blog/high-throughput-ledger/image3.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image3.png" alt="Figure 3: Three transactions, one fold, one conditional write" width="3400" height="2256" loading="lazy" /></a>

<p class="ledger-caption">Figure 3: Three transactions, one fold, one conditional write</p>

Two quantities describe the result. If R is the number of commits per second and N is the average batch size, the account processes R × N transactions per second, and its balance item takes R writes per second. Under load, the batch grows instead of the commit rate, so a busier account writes its balance item less often per transaction. Table 3 shows that across a ladder of 60-second runs on one account.

| **Achieved TPS** | **Client concurrency** | **Batch size** | **Balance writes/s** | **Transactions per balance write** | **P90** | **P99** |
| --- | --- | --- | --- | --- | --- | --- |
| 979 | 500 | 137 | 7.1 | 137.1 | 171 ms | 309 ms |
| 4,791 | 2,000 | 828 | 5.8 | 828.4 | 240 ms | 362 ms |
| 9,485 | 4,000 | 1,990 | 4.8 | 1,989.6 | 318 ms | 502 ms |
| 18,744 | 12,000 | 5,493 | 3.4 | 5,493.5 | 632 ms | 857 ms |
| 23,217 † | 20,000 | 5,956 | 3.9 | 5,955.7 | 825 ms | 1.022 s |

<p class="ledger-caption">Table 3: The throughput ladder on one account, BATCH_WINDOW=100ms and MAX_BATCH_SIZE=6000, integrity verified in every run. † With GOMEMLIMIT=12GiB and GOGC=400, discussed later.</p>

Figure 4 plots the amortization column. At the top of the ladder, the balance item took 3.9 writes per second, which is under 8 write units per second against its partition's 1,000. It was never the constraint in any run we made.

<a href="/blog/high-throughput-ledger/image4.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image4.png" alt="Figure 4: Transactions per balance-item write rise with load" width="2200" height="1040" loading="lazy" /></a>

<p class="ledger-caption">Figure 4: Transactions per balance-item write rise with load</p>

The latency columns are the price. Every caller waits for its window to close and for the commit, so the window puts a floor under latency, and at the top of the ladder a batch of about 6,000 entries made the cycle (window plus commit) 257 ms against a 100 ms window. This configuration is the one to pick when throughput per account matters and a P90 of a few hundred milliseconds is acceptable. It's the wrong one for a tight P99 budget, which turned out to need a different design.

### Client concurrency sets the batch

Above saturation, the batch isn't produced by the window at all. It's whatever the clients have in flight. In the 9,485 TPS run, the largest batch was exactly 4,000, which was the client concurrency, against a MAX_BATCH_SIZE of 6,000. The amortization ratio is therefore a property of the client population as much as of the configuration, and anyone who reproduces Table 3 at a different concurrency will correctly get different ratios.

This matters when you load test. [Little's Law](https://en.wikipedia.org/wiki/Little%27s_law) says the number of requests in flight equals the arrival rate times the time each one spends in the system, so we used a floor of 3 × λ × W for offered concurrency, where λ is the target rate and W is the measured P50 latency. Below that floor, our load generator limited throughput before the service did.

### Collecting the next window while the commit is in flight

The first version of the batcher didn't open the next window until the previous commit returned, so each cycle cost the window plus the commit. At 23,360 TPS, a run recorded 2.8 balance writes per second, which is a 357 ms cycle against a 100 ms window. For 72% of every cycle, no window was open.

We changed the dispatch so that every batch passes through a commit gate with one permit per account. Commits stay strictly serial, so no two conditional writes ever race on the balance item's version, and the next window is collected while the previous commit is in flight. The cycle becomes the greater of the window and the commit instead of their sum. Table 4 shows three runs of each design at the same offered load.

| **Design** | **TPS, three runs** | **Cycle** | **Batch size (P50)** | **P90** |
| --- | --- | --- | --- | --- |
| Window, then commit | 18,817 / 18,615 / 18,845 | 286 / 278 / 303 ms | 5,320 / 5,232 / 5,795 | 530 / 530 / 706 ms |
| Window during commit | 18,755 / 18,747 / 18,840 | 112 / 118 / 109 ms | 2,103 / 2,213 / 2,055 | 289 / 350 / 253 ms |

<p class="ledger-caption">Table 4: Overlapping the window with the commit, BATCH_WINDOW=100ms, client concurrency 10,000</p>

P90 fell by half and throughput didn't move, which is what you'd expect above saturation, where offered load sets throughput and the commit rate sets latency. The cost shows up in amortization. Balance writes went from 3.5 to 8.9 per second, and the batch roughly halved, to about 2,100 transactions per balance write. At about 18 write units per second, the balance item still used under 2% of its partition's limit, and a wider window buys the amortization back when you need it. Table 3 was measured before this change.

## Step 2: Give each account one logical writer, and make the fold pure

Microbatching needs one place where an account's transactions meet. Inside the task, a router hashes the account ID with FNV-1a to pick a processor, and one goroutine per account owns that account's queue, window, and dispatch. There's no lock around the balance, because only one goroutine ever computes against it.

The router is deliberately not trusted for correctness. If routing were ever wrong, the cost would be a failed conditional write, never a wrong balance, because DynamoDB evaluates the commit's condition no matter which process sent it. Fencing, in step 4, depends on that property.

Inside the writer, the decision to move money is a pure function. It takes an opening balance and an ordered list of transactions, and it returns a new balance, a commit record, and one result per transaction. It reads no clock, performs no I/O, and never mutates its input. The following simplified excerpt is the core of the engine:

```go
// Apply folds an ordered batch of transactions onto a balance. It is the
// only place in the system where money moves, and it is a pure function.
func Apply(in Input) (Outcome, error) {
    if err := validateInput(in); err != nil {
        return Outcome{}, err
    }
    fold := newFold(in)
    for _, tx := range in.Transactions {
        fold.next(tx)
    }
    return fold.outcome(), nil
}
 
func (f *fold) next(tx transaction.Transaction) {
    // A retry inside the same window gets the same answer, not a second effect.
    if previous, duplicate := f.seen[tx.ID]; duplicate {
        f.results = append(f.results, previous.AsDuplicate())
        return
    }
    result := f.decide(tx)
    f.seen[tx.ID] = result
    f.results = append(f.results, result)
    f.entries = append(f.entries, result)
}
 
func (f *fold) decide(tx transaction.Transaction) transaction.Result {
    result := transaction.Result{
        TransactionID: tx.ID,
        Amount:        tx.Amount,
        BatchID:       f.in.BatchID,
        BalanceBefore: f.running,
        BalanceAfter:  f.running,
    }
    snapshot := account.Balance{AccountID: tx.AccountID, Amount: f.running}
    next, err := snapshot.CanApply(tx.SignedAmount()) // checked int64 arithmetic
    if err != nil {
        return reject(result, rejectReasonFor(err)) // insufficient funds or overflow
    }
    // A rejection consumes no sequence number, so the sequence space
    // contains exactly the transactions that moved money.
    f.sequence++
    f.running = next
    result.Status = transaction.StatusApplied
    result.Sequence = f.sequence
    result.BalanceAfter = next
    return result
}
```

With a balance of 100 and two debits of 80 in one window, the first debit is applied and leaves 20, and the second is rejected. The two are never evaluated against the same starting balance, so the balance can't reach -60. The seen map collapses a retry that arrives inside the same window into the answer already decided for it.

Purity isn't a style preference here. After an optimistic conflict, the executor reloads the balance and runs the fold again, and a debit that fitted the first time can legitimately be rejected the second time. That's only safe if the same input always produces the same output. It also means the financial rules are unit tested without infrastructure. The domain package imports nothing but the Go standard library.

Money is int64 minor units wrapped in a named type whose Add and Sub report overflow instead of wrapping. A credit that would overflow becomes a rejection with reason AMOUNT_OVERFLOW, and no floating-point type appears anywhere in the domain code.

## Step 3: Commit the batch as one conditional transaction

The commit is the only synchronous write, and it has to make the batch's result durable as one fact. It's a single TransactWriteItems call with three kinds of item:

- The balance item, updated with the closing balance, version, and sequence, conditional on the version and owner epoch the batch was computed against.

- The batch record, written with attribute_not_exists on its key, carrying every transaction's outcome. When the outcomes don't fit in one item, they go into additional part items.

- A pending-materialization marker, which records that this batch's history and idempotency records aren't written yet.

The following excerpt from the commit builder shows the items and their conditions, with the attribute name and value maps omitted:

```go
items = append(items, types.TransactWriteItem{Update: &types.Update{
    TableName: aws.String(s.table),
    Key: map[string]types.AttributeValue{
        attrPK: stringAttr(balancePK(commit.AccountID)), // ACCOUNT#<id>
        attrSK: stringAttr(skBalance),                   // BALANCE
    },
    UpdateExpression: aws.String(
        "SET #balance = :balance, #version = :newVersion, " +
            "#sequence = :sequence, #updatedAt = :updatedAt"),
    // This is the entire concurrency control of the system, in two clauses.
    ConditionExpression: aws.String("#version = :expectedVersion AND #ownerEpoch = :expectedEpoch"),
}})
 
// The batch record: BATCH#<batch_id> / HEADER, with the entries inline
// when they fit in one item.
items = append(items, types.TransactWriteItem{Put: &types.Put{
    TableName:                aws.String(s.table),
    Item:                     s.headerItem(commit, parts, inline),
    ConditionExpression:      aws.String("attribute_not_exists(#pk)"),
    ExpressionAttributeNames: map[string]string{"#pk": attrPK},
}})
 
if !inline { // Larger batches: one PART#<nnnn> item per chunk of entries.
    for index, part := range parts {
        items = append(items, types.TransactWriteItem{Put: &types.Put{
            TableName: aws.String(s.table),
            Item:      s.partItem(commit, index, part),
        }})
    }
}
 
// The durable pointer to unfinished work.
items = append(items, types.TransactWriteItem{Put: &types.Put{
    TableName: aws.String(s.table),
    Item:      s.pendingItem(commit),
}})
```

The two clauses on the balance item carry the system's whole concurrency control. version makes the update a compare-and-swap, so a lost update is impossible even if two processes compute against the same balance. owner_epoch is a fencing token, covered in step 4. Putting both on the same item lets one condition check, in one round trip, that the computation is still valid and that this process may still write.

Storing the outcomes inside the commit is what makes a crash between the commit and the response recoverable. If the balance moved, every transaction's outcome exists at the same instant, in the batch record, and nothing about an individual result depends on memory that a crash can lose.

### Fitting a batch into one transaction

DynamoDB limits a transaction to [100 items and 4 MB](https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_TransactWriteItems.html), and an item to 400 KB. Three decisions keep a large batch inside those limits:

- The entries of a small batch ride inline on the batch record, which makes the commit three items. A larger batch splits its entries into part items of BATCH_ENTRIES_PER_ITEM (250 by default) each.

- An entry stores only what can't be derived: ID, direction, amount, decision, and a reject reason. Sequence numbers, per-entry balances, and timestamps are reconstructed by folding the entries in order from the batch's opening values, which took an entry from 82 billed bytes to 33.

- The service checks its configuration against all three limits at startup, so an oversized combination fails at boot instead of under load. (Our first version checked only the 100-item limit, and a setting that would have exceeded 400 KB per item passed.)

A count isn't a size, either. On the opt-in path described later, entries always ride inline, and a client that sends long transaction IDs can push a batch past 400 KB at an entry count the startup check accepted. The batcher on that path also closes a batch early on its estimated encoded size.

### A client request token per attempt

TransactWriteItems accepts a ClientRequestToken that makes the call idempotent for [10 minutes](https://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_TransactWriteItems.html). The SDK retries a failed call internally with an identical input, and the token de-duplicates those retries. The obvious token is the batch ID, and it's wrong. The executor keeps one batch ID across its optimistic retries, and after a conflict it recomputes the write against a reloaded balance, so the parameters change while the batch ID doesn't. DynamoDB answers a reused token with changed parameters with IdempotentParameterMismatch, which would turn the first conflict into a hard failure. The token is derived per attempt instead:

```go
// commitToken is stable within one attempt, so the SDK's internal retries
// are de-duplicated, and distinct across optimistic retries, because the
// version a commit conditions on only rises.
func commitToken(req outbound.CommitRequest) string {
    sum := sha256.Sum256([]byte(fmt.Sprintf("%s|%d|%d|%d|%d|%d",
        req.Commit.BatchID,
        req.Expected.Version, req.Expected.OwnerEpoch,
        req.Closing.Version, req.Closing.Sequence, req.Closing.Amount.Int64())))
    // 32 hex characters, within the 36-character ClientRequestToken limit.
    return hex.EncodeToString(sum[:16])
}
```

Every AWS run we made recorded zero optimistic conflicts, which is why a token equal to the batch ID survived as long as it did. Data-level de-duplication never depended on the token. It comes from the batch record's attribute_not_exists condition, and the next two steps rely on that.

The commit's capacity is paid once per batch, so its cost per transaction shrinks as the batch grows. On a cold account, where a batch is a single transaction, DynamoDB reported exactly 7 write units per commit.

## Step 4: Fence stale writers with an owner epoch

One writer per account is a routing property, and routing can be wrong. A process can pause for a long garbage collection, lose its network for a few seconds, or overlap with its replacement during a deployment. When it wakes up, it still believes it owns its accounts.

Fencing makes that belief harmless. Before a process serves an account, it claims it by atomically incrementing the account's owner_epoch, and every commit it makes afterward is conditional on that epoch. The following excerpt shows the claim:

```go
out, err := s.client.UpdateItem(ctx, &awsdynamodb.UpdateItemInput{
    TableName: aws.String(s.table),
    Key: map[string]types.AttributeValue{
        attrPK: stringAttr(balancePK(accountID)),
        attrSK: stringAttr(skBalance),
    },
    UpdateExpression:    aws.String("SET #ownerEpoch = #ownerEpoch + :one, #owner = :owner, #updatedAt = :now"),
    ConditionExpression: aws.String("attribute_exists(#pk)"),
    ExpressionAttributeNames: map[string]string{
        "#pk":         attrPK,
        "#ownerEpoch": attrOwnerEpoch,
        "#owner":      attrOwner,
        "#updatedAt":  attrUpdatedAt,
    },
    ExpressionAttributeValues: map[string]types.AttributeValue{
        ":one":   numberAttr(1),
        ":owner": stringAttr(processorID),
        ":now":   timeAttr(s.clock.Now()),
    },
    ReturnValues: types.ReturnValueAllNew,
})
```

A superseded process computes its next batch against its old epoch, and its commit fails the condition. It can't resume writing after another process has taken over, however long it was paused.

### Activation order is part of correctness

Claiming ownership is the first of four steps a process takes before it serves an account's first batch, and their order is load bearing:

- Claim ownership, which fences every earlier owner.

- Open an activation window in the idempotency layer, which discards cached "never decided" answers observed under the previous owner and refuses to record new ones.

- Recover every batch this account committed but never materialized, which writes their history and idempotency records.

- Close the window, re-read the balance, and confirm that the epoch hasn't moved.

Only after step 3 does "there is no idempotency record for this ID" mean "this ID was never decided". Serving earlier would let a redelivered transaction that a crashed process had already committed be applied a second time. The following simplified excerpt shows the sequence:

```go
func (e *Executor) ensureActivated(ctx context.Context, accountID string, state *accountState) error {
    if state.activated {
        return nil
    }
    // 1. Fence every previous owner.
    balance, err := e.claimOwnership(ctx, accountID)
    if err != nil {
        return err
    }
    // 2. Cached absences were observed under a previous owner: drop them,
    //    and refuse new ones until recovery has run.
    e.deps.Guard.BeginActivation(accountID)
 
    // 3. Finish every batch this account committed but never materialized.
    if err := e.deps.Recoverer.RecoverAccount(ctx, accountID); err != nil {
        e.deps.Guard.EndActivation(accountID)
        return fmt.Errorf("recover account %q before serving: %w", accountID, err)
    }
    // 4. Only now does a missing idempotency record mean "never decided".
    e.deps.Guard.EndActivation(accountID)
 
    refreshed, err := e.deps.Balances.Load(ctx, accountID)
    if err != nil {
        return fmt.Errorf("reload balance of %q after recovery: %w", accountID, err)
    }
    if refreshed.OwnerEpoch != balance.OwnerEpoch {
        return fmt.Errorf("account %q was taken over during activation: %w",
            accountID, outbound.ErrOptimisticConflict)
    }
    state.balance = refreshed
    state.activated = true
    return nil
}
```

We model-checked this design with [P](https://p-org.github.io/P/), a language for modeling systems as communicating state machines, and fencing is where the model first paid for itself. With version-only concurrency control and no epoch, a takeover scenario with two processes violated exactly-once in 55.83% of the schedules the checker explored. With the epoch carried on every item a commit touches, the same scenario was clean over 10,000 schedules.

Fencing makes two concurrent owners safe, and it doesn't make them productive. In a test with two processes competing for one account, they passed the epoch back and forth, and one batch in 80 exhausted its retries. We run one task that owns every account it serves. Scaling out means assigning accounts to tasks with a lease or a coordinator, which the fencing token protects but doesn't replace.

## Step 5: Treat an ambiguous commit as unknown, not failed

A timeout doesn't tell you whether DynamoDB applied a write. The request may have failed before it arrived, or it may have committed and lost its response on the way back. A ledger that treats every error as "nothing happened" eventually applies some transactions twice, because clients retry transactions whose money already moved.

The executor settles the question by asking whether its own batch exists. The batch record is written with attribute_not_exists, so a strongly consistent read of the batch ID has a definite answer. Figure 5 shows the decisions that follow a failed commit.

<a href="/blog/high-throughput-ledger/image5.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image5.png" alt="Figure 5: How the executor resolves a failed commit" width="3000" height="2000" loading="lazy" /></a>

<p class="ledger-caption">Figure 5: How the executor resolves a failed commit</p>

The following simplified excerpt shows the same logic in the retry loop:

```go
startedCommit := time.Now()
err = e.deps.Committer.Commit(ctx, outbound.CommitRequest{
    Expected: state.balance, Closing: outcome.Closing, Commit: outcome.Commit,
})
commitDuration := time.Since(startedCommit)
if err == nil {
    return e.afterCommit(ctx, state, outcome, txs, resolution, commitDuration, attempt)
}
 
// A timeout doesn't say whether DynamoDB applied the write, so ask
// whether this batch id exists.
committed, found, checkErr := e.loadOwnBatch(ctx, batchID)
switch {
case checkErr == nil && found:
    // It landed. Answer every caller from the durable batch.
    return e.afterCommittedElsewhere(ctx, state, committed, txs, resolution)
case checkErr != nil:
    // Unknown, not failed. Stop serving until re-activation has recovered it.
    state.activated = false
    return nil, fmt.Errorf("%w: batch %s", ErrCommitOutcomeUnknown, batchID)
}
 
// Absent is only absent as of that read. Only a refused condition proves
// that nothing was written, so any other error also fails closed.
if !errors.Is(err, outbound.ErrOptimisticConflict) {
    state.activated = false
    return nil, fmt.Errorf("%w: batch %s", ErrCommitOutcomeUnknown, batchID)
}
 
// A genuine conflict: reload the balance and recompute the whole batch.
if err := e.refresh(ctx, accountID, state); err != nil {
    return nil, err
}
```

Two branches deserve a closer look. When the confirmation read itself fails, which is likely when the store is unwell, the outcome is unknown, and the executor says so. It returns ErrCommitOutcomeUnknown and marks the account for re-activation. The next batch for that account runs recovery first, which finds the batch through its marker and writes its idempotency records, so the client's retry is answered as a duplicate. Reporting a plain failure would be unsafe for a specific reason: the in-memory record of the decision is written only after a confirmed commit, and the durable record comes later still, so a retry would find neither and resolve the ID as fresh.

The second branch is quieter. A batch that's absent is only absent as of that read, and a commit whose response was lost may still land afterward. Only a transaction that DynamoDB cancelled because a condition was refused proves that nothing was written. Any other error with an absent batch also fails closed.

This is where the model checker found the defect that justified the whole exercise. On the opt-in path described later, several batches of one account commit concurrently, and each claims its transaction IDs while it decides them. A commit landed, its response was lost, and the confirmation read also failed. The process then released its claims, and a concurrent batch waiting on one of them was told the ID was fresh and applied the same money again. It takes three independent failures in one order, and the checker reached it in 24.43% of its schedules. Code review hadn't found it. The fix strands the claims instead of releasing them, so a waiting batch fails closed until recovery has written the durable record.

## Step 6: Keep idempotency exact without a write per transaction on the critical path

Idempotency has two layers. Each account keeps an in-memory index of the decisions it made recently, which answers most duplicates at no storage cost. Behind it is a durable record per transaction, with partition key TX#&lt;transaction_id&gt;, sort key DEDUP, and a [Time to Live (TTL)](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/TTL.html) attribute. A miss in memory falls through to a strongly consistent BatchGetItem, because a duplicate must never be missed just because a replica hadn't caught up. Figure 6 shows the resolution for one transaction ID.

<a href="/blog/high-throughput-ledger/image6.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image6.png" alt="Figure 6: Resolving one transaction ID" width="3000" height="2120" loading="lazy" /></a>

<p class="ledger-caption">Figure 6: Resolving one transaction ID</p>

The durable records aren't written in the commit. They're written asynchronously, together with history, using BatchWriteItem. The tempting alternative is to let the history row double as the idempotency record, keyed by transaction ID and written with a conditional put. It saves a write per transaction, and it puts a conditional write per transaction back on the path the caller waits on, which is exactly what batching removed. We rejected it.

Writing the records late is safe because of three mechanisms, and each one was added after a specific failure:

- **Pinning.** An in-memory decision stays pinned until its durable record exists. Before pinning, a small cache and a slow materialization could evict a decision in the gap between the commit and the durable record, and a redelivery then missed both layers and applied the transaction again. An independent review found it, and it's the only outright invariant violation this project has had. The test for the fix fails when the fix is reverted.

- **Activation order.** A new owner recovers an account's unfinished batches before it serves the account, so a durable miss means "never decided".

- **Fail-closed backpressure.** When too many decisions are waiting for their durable record, the service refuses new work with HTTP 503 rather than widening the window in which memory is the only record.

Resolution happens inside the retry loop, not once before it. A conflict can mean that this process was fenced and the new owner already committed some of these very transactions, and reusing the first answer would apply them again. Rejections get idempotency records too. Otherwise, the outcome of a transaction ID would depend on when it was retried.

The durable key is global. A transaction ID belongs to one account, and a lookup that finds the ID recorded for a different account is refused with HTTP 409 instead of being answered with another account's decision.

### Taking the lookup off the critical path

The durable lookup depends only on the transaction ID, so it doesn't have to wait for the window to close. The batcher warms it in chunks of 100, one BatchGetItem per chunk, while the window is still collecting. Our first implementation of that prefetch made the service slower. A prefetch and the resolve that followed it sometimes looked up the same ID twice, the store was already the bottleneck, and P90 went from 511 ms to 928 ms at 1.5 lookups per transaction. Claiming each lookup, so that exactly one fetch happens per ID, brought it back to exactly 1.0. (At the low-latency configuration, batches of about 30 never fill a chunk, so there the lookup runs when the window closes.)

Two smaller DynamoDB details surfaced here. BatchGetItem rejects a request that repeats a key, and a client retrying inside one window legitimately produces repeats, so the keys are de-duplicated before the call. Before that fix, one run produced 648 HTTP 500 errors. And the lookup is the one cost a batch never amortizes: we measured exactly 1.000 read unit per transaction. A run at 20,000 TPS needs about 20,000 read units per second for this lookup alone, so size reads with the same care as writes.

The design held under deliberate duplication. In a run where 25% of deliveries were duplicates, the service recognized 71,976 of them and produced zero double effects.

## Step 7: Move history off the critical path, and make recovery cheap

After the commit, the executor hands the batch to an in-process channel and answers its callers. The hand-off never blocks:

```go
func (c *Channel) Publish(_ context.Context, commit batch.Commit) error {
    select {
    case c.commits <- commit:
        c.published.Add(1)
        return nil
    default:
        c.dropped.Add(1)
        return ErrBufferFull
    }
}
```

A full channel drops the batch, counts the drop, and moves on. Nothing is lost, because the pending marker is already durable. Blocking instead would let a slow history writer add latency to money movement. The same rule applies to observability. Log and metric records go through a bounded asynchronous writer, because on Fargate stdout is a pipe, and a plain write blocks when the pipe fills. In our tests, 10,000 writes against a sink that never drains finish in 2.48 ms, with the dropped records counted.

Materialization workers drain the channel. Each batch gets its history block and idempotency records, and only then is it marked complete. The following simplified excerpt shows the order:

```go
func (s *Service) Materialize(ctx context.Context, commit batch.Commit) error {
    entries := s.deps.Sharder.EntriesFor(commit.AppliedEntries())
    if err := s.deps.Ledger.WriteEntries(ctx, entries); err != nil {
        return fmt.Errorf("write ledger entries of batch %s: %w", commit.BatchID, err)
    }
    if err := s.deps.Dedup.Record(ctx, dedupRecordsFor(commit)); err != nil {
        return fmt.Errorf("record dedup entries of batch %s: %w", commit.BatchID, err)
    }
    // The durable records exist, so these decisions no longer need pinning.
    s.deps.DedupObserver.DurableDedupWritten(commit)
 
    // Last. Marking first would let a crash erase the only pointer
    // to unfinished work.
    if err := s.deps.Marker.MarkMaterialized(ctx, commit); err != nil {
        return fmt.Errorf("mark batch %s materialized: %w", commit.BatchID, err)
    }
    return nil
}
```

Writing history and idempotency records before marking the batch means that a crash in the middle leaves the marker in place, and the work is redone. Every write is idempotent by key, because every key is fixed at commit time, so redoing work is always safe. Marking complete is its own small TransactWriteItems call. It sets the batch record's status to COMPLETED and deletes the marker, so the record and the marker can't disagree. Figure 7 shows the whole asynchronous path, including recovery.

<a href="/blog/high-throughput-ledger/image7.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image7.png" alt="Figure 7: Asynchronous materialization, with the sweeper as the safety net" width="3460" height="2028" loading="lazy" /></a>

<p class="ledger-caption">Figure 7: Asynchronous materialization, with the sweeper as the safety net</p>

### A sparse index for recovery

A recovery sweeper runs on a timer and queries a [sparse global secondary index](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/bp-indexes-general-sparse-indexes.html), gsi1-pending, to find batches whose hand-off was dropped or whose worker crashed. Only pending markers carry the index's key attributes, so the index contains exactly the outstanding work and returns to empty when the system is caught up. Recovery costs the size of the backlog, not the size of history. The index projects keys only, because the marker's keys already carry the account and batch IDs.

The index's sort key starts with the marker's creation time in nanoseconds, zero-padded to 19 digits, so "older than the grace period" is a key condition rather than a filter:

```go
out, err := s.client.Query(ctx, &awsdynamodb.QueryInput{
    TableName:              aws.String(s.table),
    IndexName:              aws.String(IndexPending), // gsi1-pending, KEYS_ONLY
    KeyConditionExpression: aws.String("#gsi1pk = :shard AND #gsi1sk < :cutoff"),
    ExpressionAttributeNames: map[string]string{
        "#gsi1pk": attrGSI1PK,
        "#gsi1sk": attrGSI1SK,
    },
    ExpressionAttributeValues: map[string]types.AttributeValue{
        ":shard":  stringAttr(pendingGSI1PK(shard)), // PENDING#<shard>
        ":cutoff": stringAttr(fmt.Sprintf("%0*d", numericWidth, olderThan.UnixNano())),
    },
    ScanIndexForward: aws.Bool(true), // oldest first: it bounds the lag
    Limit:            aws.Int32(int32(limit)),
})
```

The grace period exists because of a measured failure. Without it, the sweeper re-materialized batches the live workers were still processing and added 14–24% redundant writes to the resource that was already the constraint. The grace period now follows the observed materialization lag: when twice the lag's P99 is longer than the configured grace, the sweeper uses that instead, up to 10 minutes. Because a GSI is eventually consistent, a completed batch's marker can still appear in the index briefly after it's deleted, so the sweeper reads the batch record with a strongly consistent read and skips completed batches.

### The marker has to stay its own item

The marker looks like it could be folded onto the batch record to save an item in the commit. We tried it on the opt-in path, and it removed a query we depended on. The marker is read two ways: by recovery shard through the index, and by account when a new owner recovers an account before serving it. The batch record's partition key is BATCH#&lt;batch_id&gt;, which no account ID can derive, so folding the marker onto it silently removed the by-account query. Activation found nothing to recover, a missing idempotency record stopped meaning "never decided", and after a restart, a committed but unmaterialized batch was applied a second time. The test that caught it failed on its first run with a double spend of 5,000.

That build was fast. Twenty benchmark runs reached up to 13,623 TPS at a P99 of 72 ms, and none of them count, because the build could move money twice. Restoring the marker cost about 6% of throughput and 15–35 ms of P99.

The marker's partition key needed one more change. With one key per account, every commit writes the same partition. On the default path at about 22 commits per second, that never mattered. On the opt-in path at about 400 commits per second, it's 800 write units per second on one partition key against a limit of 1,000, and it measured as a fourfold collapse in the batch rate. The key now carries a bucket derived from the batch ID, ACCOUNT#&lt;id&gt;#PENDING#&lt;bucket&gt;, and activation reads the buckets in parallel.

### History in blocks

History is one item per batch, not one per transaction. A block carries the batch's applied entries, keyed ACCOUNT#&lt;id&gt;#SHARD#&lt;n&gt;, with n derived from the batch ID by FNV-1a modulo LEDGER_SHARD_COUNT (32 by default), and sorted by BLOCK#&lt;first sequence, zero-padded&gt;#&lt;batch_id&gt;. Moving from one item per entry to one per batch cut measured write capacity from 2.449–2.466 to 1.526–1.548 write units per transaction across three runs each, a 37% reduction, at about 11,600 TPS with throughput and materialization lag unchanged. We had predicted a saving of 0.94 units per transaction, and we measured 0.92.

Spreading blocks across shards keeps a hot account's history off a single partition, and physical placement carries no meaning, because order comes from the sequence number. A history read queries every shard in parallel and merges by sequence in memory. Range reads get more expensive, and that's the right side of the trade for a ledger, which writes history constantly and reads it rarely. It also means changing the shard count only changes where new blocks land.

The batch ID at the end of the sort key isn't decoration. Once the opt-in path gave each slice of an account its own sequence counter, sequence numbers stopped being unique within an account. History was then one item per entry, keyed by sequence alone, and two entries from different slices with the same sequence number that hashed to the same shard wrote the same key. On a real run before the fix, 114,287 applied transactions produced 108,350 history rows. That's 5.2% of history lost silently, with the balance exactly right, because the balance never comes from history. BatchWriteItem overwrites an existing item with the same key, so there was no error to see. A uniqueness guarantee that rests on numbers happening to be unique belongs in the key.

Materialization kept up in the long runs. Over 15 minutes at about 11,500 TPS, the lag's P99 stayed between 0.106 and 0.111 seconds, and ledger entries matched applied transactions to within 0.0017%. The small surplus is the sweeper redoing a batch that a worker was already finishing, which the idempotent keys absorb.

## The resulting table design

All of the item types live in one table. Table 5 lists them with the keys the code builds.

| **Item** | **Partition key** | **Sort key** | **Written by** | **API** |
| --- | --- | --- | --- | --- |
| Balance | ACCOUNT#&lt;id&gt; | BALANCE | The commit, and ownership claims | TransactWriteItems, UpdateItem |
| Batch record | BATCH#&lt;batch_id&gt; | HEADER | The commit, then completion | TransactWriteItems |
| Batch part | BATCH#&lt;batch_id&gt; | PART#&lt;nnnn&gt; | The commit, only for batches too large to inline | TransactWriteItems |
| Pending marker | ACCOUNT#&lt;id&gt;#PENDING#&lt;bucket&gt; | BATCH#&lt;batch_id&gt; | The commit; deleted at completion | TransactWriteItems |
| History block | ACCOUNT#&lt;id&gt;#SHARD#&lt;n&gt; | BLOCK#&lt;first sequence&gt;#&lt;batch_id&gt; | Materialization | BatchWriteItem |
| Idempotency record | TX#&lt;transaction_id&gt; | DEDUP | Materialization, with TTL | BatchWriteItem |
| Reservation slice (opt-in) | ACCOUNT#&lt;id&gt;#RESV#&lt;n&gt; | RESV | The commit | TransactWriteItems |

<p class="ledger-caption">Table 5: Item types. The sparse index gsi1-pending has partition key PENDING#&lt;recovery shard&gt; and sort key &lt;created_at in nanoseconds&gt;#&lt;batch_id&gt;, projects keys only, and holds only pending markers.</p>

Three partition shapes appear, and each answers a different question. The balance uses one key per entity, because the authoritative state has to live in one place. Batches and idempotency records use keys with natural high cardinality, a UUID and a client-generated ID, which spread without any extra work. History, markers, and slices use a prefixed, spread key, because the account is the hot thing, so the account alone can't be the partition key. Those three spread for different reasons. History spreads by hash because its placement means nothing. Markers spread by a hash of the batch ID because what needs spreading is the commit rate. Slices spread by explicit index because which slice a commit touches decides whether two commits collide. Figure 8 shows how the items relate.

<a href="/blog/high-throughput-ledger/image8.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image8.png" alt="Figure 8: One account&#x27;s items and the shared items they relate to" width="3600" height="1600" loading="lazy" /></a>

<p class="ledger-caption">Figure 8: One account's items and the shared items they relate to</p>

We chose one table for operational reasons, not for atomicity. A DynamoDB transaction can span tables in the same account and Region, and its 100-item limit applies to the transaction however many tables it touches. What one table buys is one set of alarms, one capacity configuration, and one backup policy, where a commit spanning four tables would be throttled by whichever table was hottest. The cost is that the table has no schema beyond its key attributes. Our defenses are that every key string is built in one Go file, next to the functions that parse it, and that the service calls DescribeTable at startup and refuses to run against a table whose shape doesn't match. It deliberately has no permission to create the table.

A few smaller conventions came from mistakes:

- Numeric key components are zero-padded to 19 digits, the width of an int64, so lexicographic order equals numeric order and a Query returns history in sequence order without sorting.

- Every attribute name in every expression goes through ExpressionAttributeNames. Names such as sequence are [DynamoDB reserved words](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/ReservedWords.html), and our first end-to-end test failed with ValidationException because of them. Aliasing every name means adding an attribute to an expression can never bring that failure back.

- Every read whose answer decides whether money moves is strongly consistent.

### What a transaction costs

We measured capacity by asking DynamoDB rather than by counting items. On the hot path, three runs at about 11,600 TPS on one account consumed 1.548, 1.541, and 1.526 write units per transaction, plus 1.000 read unit per transaction for the idempotency lookup. Table 6 shows the other end of the spectrum: cold accounts that each send about one transaction per second, where every batch holds a single transaction and nothing amortizes.

| **Path** | **Write units per transaction** | **Of which on the index** | **What it is** |
| --- | --- | --- | --- |
| Commit | 7.000 | 1.0 | Three transactional items at 2 units each, plus the index entry |
| Completion | 5.000 | 1.0 | Two transactional items at 2 units each, plus the index delete |
| History | 1.000 |  | One block holding one entry |
| Idempotency record | 1.000 |  | One record |
| Balance | 0.008 |  | Activation, spread over about 120 transactions per account |
| Total | 14.008, plus 1.008 read units | 2.000 |  |

<p class="ledger-caption">Table 6: Measured capacity per transaction for cold accounts, 300 accounts at one transaction per second each, three runs agreeing to the third decimal</p>

Of the 14 write units, 12 (86%) are fixed per batch, and only 2 are truly per transaction: one history entry and one idempotency record. The hot path's 1.53 is the same fixed cost spread over a large batch.

## Step 8: Remove the commit queue by splitting an account's funds into slices

Microbatching took one account past 20,000 TPS, and the P99 target was still out of reach. Our first latency campaign changed only configuration, and the best result across three consecutive runs was about 1,900 TPS at a P99 of 127–140 ms. Shrinking the window from 100 ms to 10 ms took 36% off the median and 25% off P90, and neither 10 ms nor 5 ms moved P99 at all (359, 378, and 358 ms). The tail sat near 130 ms whatever we changed.

The diagnosis came from switching batching off. With MAX_BATCH_SIZE=1, a caller's latency is the window plus one commit, and varying the client concurrency varies only the queue. Table 7 shows three rows that explain the floor.

| **MAX_BATCH_SIZE** | **Client concurrency** | **Achieved TPS** | **P50** | **P99** | **Commits/s** |
| --- | --- | --- | --- | --- | --- |
| 1 | 1 | 24 | 25 ms | 59 ms | 24.2 |
| 1 | 2 | 25 | 24 ms | 84 ms | 24.9 |
| 1 | 12 | 39 | 272 ms | 1.18 s | 38.6 |

<p class="ledger-caption">Table 7: The commit path with batching disabled</p>

With one request in flight, the commit path answered at a P99 of 59 ms, so DynamoDB wasn't what stood between the design and 100 ms. With 12 in flight, the median was 272 ms and nothing about the service had changed. At 38.6 commits per second, Little's Law gives 12 ÷ 38.6 = 311 ms, against a measured P90 of 309 ms. The tail was a queue in front of a serial commit loop.

The loop is serial because of the condition expression. Every commit for an account is a compare-and-swap on the same item, and commit N+1 must name the version that commit N produces, which doesn't exist until N lands. Sending N+1 early only makes it fail. Figure 9 shows the wait.

<a href="/blog/high-throughput-ledger/image9.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image9.png" alt="Figure 9: Why commits against one balance item serialize" width="3040" height="1976" loading="lazy" /></a>

<p class="ledger-caption">Figure 9: Why commits against one balance item serialize</p>

To stop serializing, we had to stop having one contended item.

### Reservation slices

The opt-in path, enabled by setting RESERVATION_SHARDS above zero, splits an account's funds across N items with partition keys ACCOUNT#&lt;id&gt;#RESV#&lt;n&gt;. Each slice carries its own available amount, version, sequence counter, and owner_epoch. An account can have up to COMMIT_CONCURRENCY batches in flight at once. Each batch is assigned a slice, round robin, and commits against that slice alone, so two commits for one account contend only when they draw on the same slice. Figure 10 shows two batches committing against different slices, one of them borrowing.

<a href="/blog/high-throughput-ledger/image10.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image10.png" alt="Figure 10: Batches commit against their own slice, and a batch that needs more borrows inside its commit" width="3400" height="1240" loading="lazy" /></a>

<p class="ledger-caption">Figure 10: Batches commit against their own slice, and a batch that needs more borrows inside its commit</p>

This is write sharding, so it has to answer the objection from the start of this post: no condition expression spans a set of items. The answer is to keep a condition on every item the commit writes. The slice a batch commits against is conditional on its version, its owner epoch, and a durable floor:

```go
ConditionExpression: aws.String(
    "#version = :expectedVersion AND #available >= :requiredOpening " +
        "AND #ownerEpoch = :ownerEpoch"),
```

The floor comes from the batch's own trajectory:

```go
// RequiredOpening reports the smallest opening amount from which applying
// deltas in order never goes below zero.
func RequiredOpening(deltas []money.Minor) money.Minor {
    var running, lowest money.Minor
    for _, delta := range deltas {
        running += delta
        if running < lowest {
            lowest = running
        }
    }
    return -lowest // lowest is never positive, so this is never negative
}
```

The floor is the lowest point of the batch's trajectory, not the sum of its debits and not its net. A batch of [−5,000, +5,000] needs 5,000 on hand, because it dips before it recovers. A batch of [+5,000, −5,000] needs nothing, because its credit funds its debit. Getting this wrong fails in both directions, and the model has a case for each. A floor of zero lets a slice dip below zero in the middle of a batch (caught in 99.87% of schedules), and a floor that sums the debits refuses batches the account can fund (99.97%).

### Borrowing, so that a debit the account can afford is authorized

Our first version of this path refused any debit larger than one slice's share, such as 5,000 against an account holding 10,000 in four slices of 2,500. It had 6.6 times the throughput of the configuration-only design at a 39% lower P99, and it wasn't usable. An authorizer that refuses money the account holds gives a correct balance and a useless answer.

A batch whose slice can't fund it now borrows from other slices inside the same transaction. Each donor is one more conditional update, pinned on its own version and on still holding what it lends:

```go
UpdateExpression: aws.String(
    "SET #available = #available - :amount, #version = :newVersion, " +
        "#updatedAt = :updatedAt"),
ConditionExpression: aws.String(
    "#version = :expectedVersion AND #available >= :amount AND #ownerEpoch = :ownerEpoch"),
```

A borrowing commit carries three items plus one per donor, against the 100-item limit. That's also why the slice count is capped at 64. Claiming an account fences every one of its slices in one TransactWriteItems call, because a partial fence would let a superseded owner keep writing to the slices it didn't reach, and a consistent balance read is a TransactGetItems over the balance item and every slice.

This version, with borrowing and with the recovery marker restored, measured lower than the first. Correct recovery cost about 20% of the throughput and about 10 ms of P99, and the first version's numbers had been measured on a build that traded a double spend for them.

### Tuning the slices

With slices in place, the tail depended on two numbers: the slice count S and the in-flight batches per account C. A batch holds its slice for the duration of its commit, so collision pressure grows roughly as C²/2S. A mutex profile under load attributed 99.23% of all lock delay to the per-slice lock. Table 8 shows the sweep, with three runs per configuration at a fixed offered load.

| **C** | **S** | **C²/2S** | **P99, three runs** | **P50** |
| --- | --- | --- | --- | --- |
| 24 | 32 | 9.0 | 136 / 152 / 191 ms | 26–27 ms |
| 12 | 32 | 2.2 | 165 / 125 / 85 ms | 28–30 ms |
| 8 | 32 | 1.0 | 115 / 111 / 99 ms | 33–35 ms |
| 12 | 64 | 1.1 | 99 / 102 / 89 ms | 28–29 ms |
| 8 | 64 | 0.5 | 85 / 108 / 102 ms | 33 ms |

<p class="ledger-caption">Table 8: Slice count and commit concurrency, 9,500 TPS requested, client concurrency 900</p>

Lowering concurrency alone trades the median for the tail, because fewer batches in flight means larger batches and fewer commits. Adding slices is what makes low collision pressure free. Less contention isn't monotonically better, either. C=8, S=64 has the lowest pressure in the table and a worse tail than C=12, S=64, because below some point the constraint becomes too little concurrency.

With C=12 and S=64, the account reached 11,588 TPS inside the P99 target. Table 9 shows the three consecutive runs.

| **Run** | **Achieved TPS** | **P50** | **P90** | **P99** | **Commits/s** | **Transactions per commit** | **Throttles** |
| --- | --- | --- | --- | --- | --- | --- | --- |
| 1 | 11,399 | 30 ms | 48 ms | 95 ms | 351.5 | 32.4 | 0 |
| 2 | 11,588 | 30 ms | 43 ms | 84 ms | 372.5 | 31.1 | 0 |
| 3 | 11,587 | 30 ms | 43 ms | 76 ms | 369.7 | 31.3 | 0 |

<p class="ledger-caption">Table 9: One account on the slice path, BATCH_WINDOW=2ms, MAX_BATCH_SIZE=2000, RESERVATION_SHARDS=64, COMMIT_CONCURRENCY=12, client concurrency 1,100, integrity verified in every run</p>

Over 15 minutes per run, 10.3 to 10.4 million transactions each, the same configuration held about 11,500 TPS at a P99 of 92–106 ms. Two of the three runs came in under 100 ms, so we treat about 11,500 TPS as the edge of the target rather than a comfortable operating point. At about 10,600 TPS, the 60-second P99 was 81–86 ms.

The window behaves differently on this path. With several batches in flight, it stops being a latency knob and becomes a throughput knob: one batch per window caps an account at 1/BATCH_WINDOW commits per second, which is 500 at 2 ms, and we measured 377 to 402. Halving the window to 1 ms raised the commit rate and shrank the batches, so throughput stayed flat while more commits contended for the same slices, and the P99 rose to 164–404 ms.

### What slices cost

The slice path is off by default because it charges something at every level:

- History order is per slice rather than per account, and each entry's balance before and after is scoped to its slice.

- A balance read costs a TransactGetItems over N + 1 items, at 2 read units per item, instead of one GetItem.

- Borrowing takes an account-wide exclusive lock, which serializes the account again. When most batches must borrow, the account drops to about 40 commits per second, and we measured 1,034–1,275 TPS at a P50 of 636–776 ms.

- Activation fences every slice. With 1,000 accounts activating at the same moment, the first-touch P50 was 2.566 seconds with 64 slices, against 336 ms on the single-item path.

- Several batches of one account can be undecided at the same time, so the idempotency layer claims transaction IDs per batch. Before it did, 16 deliveries of one ID at COMMIT_CONCURRENCY=8 moved money eight times, once per concurrent slot. The test that pins the fix sets MAX_BATCH_SIZE=1, because deliveries that land in one batch are collapsed by the fold and never reach the cross-batch path.

The right slice count depends on the account's balance relative to its typical transaction, not on its rate. At equilibrium, each slice holds about the balance divided by S, so slicing a thin balance finely turns ordinary debits into borrowing. Table 10 shows one account at about 11,000 TPS with transactions of 150.00.

| **Balance ÷ typical transaction** | **Slices** | **P99** | **Borrowing commits** |
| --- | --- | --- | --- |
| 666 | 64 | 424 ms | 740 |
| 666 | 8 | 198 ms | 67 |
| 6,666 | 64 | 117 ms | 0 |
| 66,666 | 64 | 129 ms | 0 |

<p class="ledger-caption">Table 10: Slice count against the ratio of balance to typical transaction</p>

At the lowest ratio, 64 slices was the worst setting we measured. The rule we use is S = min(64, balance ÷ typical transaction). The slice count is persisted on each account's balance item rather than read from configuration, so accounts laid out over different counts share one process.

### Keeping a per-type balance map inside the slice item

Real accounts don't hold one number. A checking account might hold the customer's own funds and an overdraft facility, and a debit must draw on them in a fixed order that belongs to the account, not to the request. The ledger models this as business balance types folded along a chain, such as SAVINGS then OVERDRAFT. A restricted type, such as the overdraft, is authorized only against its own capacity and never against the account's total.

The modeling question is where the per-type amounts live. Giving each type its own item would make a debit that spills from one type to the next a commit across several partition keys, and we had already measured that shape. Commits that span slices ran at 1,164 TPS against 10,385 for commits that don't, at a P50 of 692 ms against 29 ms. So the per-type amounts are a map attribute, type_balances, on the slice item. The fold computes the per-type deltas in memory, and the commit sets the map in the same update and under the same condition as available, so the whole chain still resolves to one conditional write. The following simplified excerpt is the debit fold:

```go
func FoldDebit(chain Chain, balances Balances, amount money.Minor, restrictedOnly map[string]bool) (FoldResult, error) {
    deltas := make(map[string]money.Minor)
    remaining := amount
    for _, t := range chain.Order { // for example SAVINGS, then OVERDRAFT
        if remaining <= 0 {
            break
        }
        // Capacity outside the posting's entitlement is invisible to it,
        // however much the account holds in total.
        if restrictedOnly != nil && !restrictedOnly[t] {
            continue
        }
        available := balances[t] // an absent type has zero capacity
        if available <= 0 {
            continue
        }
        take := available
        if take > remaining {
            take = remaining
        }
        deltas[t] = -take
        remaining -= take
    }
    if remaining > 0 {
        // Terminal: the account lacks the funds across the chain, and the
        // engine records a durable rejection.
        return FoldResult{}, fmt.Errorf("%w: %s short", ErrInsufficientChain, remaining)
    }
    return FoldResult{Deltas: deltas, Aggregate: -amount}, nil
}
```

We measured it at the low-latency configuration with an overdraft facility funded. In 60-second runs with the facility spread across every slice, the account measured 11,578 TPS at a P99 of 89 ms. With the same facility concentrated on one slice, it measured 2,562 TPS at a P99 of 1.25 seconds, because every batch assigned to another slice had to borrow to reach it. Sustained for 15 minutes with the facility spread, the account held 11,679 TPS at a P99 of 80 ms with zero spanning commits. The fold cost nothing we could measure, and borrowing cost almost everything. When you split a value across items, put the capacity a debit will draw on where the batches are assigned.

## Measuring it

Every number in this post comes from a benchmark report written by the load generator. Each report records its own window's consumed capacity and throttle counts, so a capacity-limited run can't be mistaken for a slow service. We ran every measurement with the following setup:

- One ECS task on AWS Fargate with Graviton, 4 vCPU and 16 GiB, behind an internal Network Load Balancer.

- Load from a c7g.4xlarge [Amazon Elastic Compute Cloud (Amazon EC2)](https://aws.amazon.com/ec2/) instance in the same VPC, never from a workstation.

- One DynamoDB table with one sparse GSI, in on-demand capacity mode unless noted otherwise.

- Runs of 60 seconds unless stated otherwise, and runs of 15 minutes to check that a rate is sustainable.

- A fresh account for every run, with pending markers drained and the task restarted before measuring.

- An integrity check against an expectation computed from the workload plan without consulting the service.

- Three consecutive runs for any latency claim, with all three reported.

The last rule is there because a single run is a sample, not a result. One run on the slice path reached 10,602 TPS at a P99 of 99 ms, and we don't quote it as the operating point. On identical code on the same day, we later measured P99 values of 65, 78, 90, 94, and 121 ms at the same configuration, so a change worth less than about 20 ms at that point can't be read from three runs per arm.

### Three operating points

Latency and throughput on one account are a configuration choice, not a fixed property of the design. Table 11 shows the three points we measured.

| | **Throughput** | **Low latency** | **Minimum latency** |
| --- | --- | --- | --- |
| Write path | Single balance item | Reservation slices | Reservation slices |
| BATCH_WINDOW / MAX_BATCH_SIZE | 100 ms / 6,000 | 2 ms / 2,000 | 5 ms / 2,000 |
| RESERVATION_SHARDS / COMMIT_CONCURRENCY | 0 / 1 | 64 / 12 | 64 / 12 |
| Client concurrency | 10,000 | 1,100 | 110 |
| Achieved TPS, three runs | 18,755 / 18,747 / 18,840 | 11,399 / 11,588 / 11,587 | 1,035 / 1,033 / 1,035 |
| Tail, three runs | P90 289 / 350 / 253 ms | P99 95 / 84 / 76 ms | P99 61 / 61 / 61 ms |
| Transactions per balance write | About 2,100 | 31–32 | 6.0 |

<p class="ledger-caption">Table 11: Three measured operating points on one account. The minimum-latency runs used a table provisioned at 10,000 write units and 6,000 read units, with zero throttling.</p>

The throughput column's peak is higher than its three-run rate. Requested at 25,000 TPS with client concurrency 20,000, the same path delivered 23,217 TPS at a P99 of 1.022 s with zero throttling, and a later run delivered 23,360 TPS with 74,437 write throttle events, which makes the second one a throughput figure and not a latency one. Both runs predate the overlapping window, and 23,217 TPS was the highest rate we requested, not a plateau we found. The task was never CPU bound at that point: its peak was 2.07 of its 4 vCPU.

The minimum-latency column is the floor we know how to reach, and about 40 ms of its P99 is DynamoDB's own transactional commit tail. We predicted it before running it. With no queue in front of the commit, a caller's P99 is about the window plus the commit's P99, and the service's own metrics put the commit's P99 at 40–51 ms, so we expected 53–61 ms. Figure 11 shows the window sweep at about 976 TPS, which ran against our intuition.

<a href="/blog/high-throughput-ledger/image11.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image11.png" alt="Figure 11: P99 against BATCH_WINDOW at about 976 TPS on the slice path" width="2200" height="1040" loading="lazy" /></a>

<p class="ledger-caption">Figure 11: P99 against BATCH_WINDOW at about 976 TPS on the slice path</p>

A larger window gave a lower tail, up to a point. Going from a 1 ms window to a 5 ms window adds at most 4 ms of waiting, and it cut the commit rate from 477 to 171 per second, which cut the chance of two commits colliding on a slice from about 15% to about 5%. The commit tail is what a caller's P99 is made of, so the best window sits inside the range rather than at its smallest value. At this rate, the single-balance-item path reached a P99 of 83 ms with 27.5 transactions per balance write, which makes the default path the better trade for an account capped near 1,000 TPS.

## What the measurements taught us

The design above is the one that survived. Several of the lessons that shaped it came from results that were wrong at first, and they apply to DynamoDB workloads well beyond ledgers.

### The ceiling we measured was a bug

The first push for 20,000 TPS reached 16,121 TPS on a 16 vCPU task. We fitted a cycle model to two saturated runs, cycle = 67 ms + 0.051 ms per entry, which put the asymptote at 19,501 TPS and said that 20,000 was out of reach at any batch size. Right-sizing the task to 4 vCPU then dropped the peak to 12,058 TPS, and we blamed the CPU.

A heap profile told a different story. 49.78% of every byte the process allocated came from one line in the admission prefetch, which allocated and copied a chunk of pending transactions before it acquired its concurrency slot. When no slot was free, it discarded the chunk without advancing its cursor, so the next arrival copied one element more. The work was quadratic in batch size, about 18 million discarded element slots per batch at a batch size of 6,000. The fix was to acquire the slot first:

```go
// Claim the slot before allocating anything. On the saturated path this
// returns having touched no memory at all.
select {
case b.admitSlots <- struct{}{}:
default:
    return admitted // Resolve still does the lookup when the window closes.
}
 
chunk := make([]transaction.Transaction, 0, waiting)
for _, req := range pending[admitted:] {
    chunk = append(chunk, req.tx)
}
```

A test that drives 2,000 saturated admissions measured 2.2 GB allocated before the fix and 0 bytes after. The same 4 vCPU task then reached 23,217 TPS, 44% past the 16 vCPU result. Figure 12 shows the three rounds.

<a href="/blog/high-throughput-ledger/image12.png" target="_blank" rel="noopener noreferrer"><img src="/blog/high-throughput-ledger/image12.png" alt="Figure 12: Peak throughput on one account across three measurement rounds" width="2200" height="1040" loading="lazy" /></a>

<p class="ledger-caption">Figure 12: Peak throughput on one account across three measurement rounds</p>

Both terms of the cycle model had been measuring the defect. So had the CPU diagnosis, for a different reason: Amazon CloudWatch had reported a peak of about 1.2 vCPU all along, and we'd read the one-minute average instead of the maximum. The arithmetic was fine both times, and the inputs were wrong.

### Garbage collection settings bought latency, not throughput

Go's garbage collector was pacing itself against about 1 GB of heap inside a 16 GiB task. Setting GOMEMLIMIT=12GiB and GOGC=400 cut collections from 89 to 63 per minute. At identical load, throughput stayed within noise (18,744 against 18,709 TPS), and P90 fell from 632 ms to 532 ms. The saving landed where it mattered, because the collector's assist work runs in the goroutine that allocates, and for a hot account that's the account's serial writer.

### Client concurrency is part of the experiment

Too little client concurrency limits throughput, and too much turns into tail latency. At 11,000 TPS requested on the slice path, a client concurrency of 1,100 delivered 10,717 TPS at a P99 of 138 ms, and a concurrency of 1,000 delivered 10,719 TPS at 87 ms. Throughput was the same, with 51 ms less tail. Above the rate the service can absorb, extra requests in flight just become queue depth.

The load generator needed testing too. Its pacer released transactions on a 1 ms ticker and truncated the number released per tick to an integer, so every requested rate from 1,000 to 1,999 TPS was delivered as exactly 1,000, and higher rates came up short by the same arithmetic, about 4% at 11,500 TPS. Earlier campaigns had written the shortfall off as jitter.

### On-demand capacity beat provisioned capacity for this workload

A 15-minute run on a table provisioned at 50,000 write units recorded 142,370 write throttle events while consuming exactly 50% of the provisioned capacity, and it reported a P99 of 1.251 s. The same run on an on-demand table throttled zero times, at a P99 of 92 ms. The throttling was steady from the second minute to the end, which rules out a partition split and points at per-partition limits with headroom in the aggregate. This workload concentrates history on LEDGER_SHARD_COUNT keys per account, while the idempotency records spread across millions of keys. Provisioned mode allocated partitions from the aggregate throughput, and on-demand split them in response to the key traffic it observed.

A fresh table isn't a warm one, either. A new on-demand table starts with a [warm throughput](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/warm-throughput-scenarios.html) of 12,000 read units and 4,000 write units per second, and this workload needs about 25,000 write units per second at the low-latency point. Our first four runs at 1,000 TPS on a new table read a P99 between 855 ms and 1.391 s because of 726 to 2,500 write throttle events per run. At 60,000 requests, the P99 is the 600 slowest, so a few hundred throttled writes set it by themselves. You can [pre-warm a table](https://aws.amazon.com/blogs/database/pre-warming-amazon-dynamodb-tables-with-warm-throughput/) before a load test or a launch. Plan billing-mode changes too: you can switch a table from provisioned to on-demand [up to four times in a 24-hour rolling window](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/bp-switching-capacity-modes.html), and a measurement campaign that pauses and resumes the table can spend all four.

### A throttle counter can read zero while the table throttles

BatchWriteItem returns HTTP 200 when DynamoDB declines some of its items and reports them in UnprocessedItems. One of our runs recorded 84,217 WriteThrottleEvents while ThrottledRequests stayed at exactly zero. Knowing that wasn't enough. A campaign later, our own benchmark report still decided whether a run was throttled from ThrottledRequests alone, and printed a clean verdict on a run with 6,857 write throttle events. Retry UnprocessedItems, alarm on the throttle-event metrics, and once you learn that a counter can mislead, find every place that reads it.

### Ask DynamoDB what a request costs

ReturnConsumedCapacity with INDEXES adds a few fields to a response you're already parsing, and it reports index capacity that the table's own metric doesn't show. Item arithmetic predicted 10 write units for a cold transaction. DynamoDB reported 14.008, and the difference was the two sparse-index units, one on the commit and one on the completion's delete, that the arithmetic hadn't counted.

It also settled a batching question before we built more. A cross-account coalescer already grouped the completion writes of about seven batches per call, 5,300 calls for 36,000 transactions, and completion still cost 5 write units per transaction, because each batch keeps its own marker delete and header update. The round trips fell sevenfold and the capacity didn't move, so we measure batching proposals in capacity units rather than call counts.

The same instrument taught one more lesson. When one role has two implementations, the instrument belongs to the role. Our capacity report listed the commit as instrumented, and it was, in the single-item store. On the slice path, a different store performs the commit, so three reports printed commit 0.00 W over 0 calls and understated the total by the largest fixed cost in the system. A zero that looks like a measurement is worse than a missing number.

### A global timeout isn't a hedge

Request hedging, which sends a second copy of a slow request and takes the first answer, is an effective way to cut read tail latency on DynamoDB, as [Global Payments showed](https://aws.amazon.com/blogs/database/how-global-payments-inc-improved-their-tail-latency-using-request-hedging-with-amazon-dynamodb/). Our first attempt at hedging the commit was a configuration change: set the SDK's HTTP timeout just above the commit's P95, so that a slow commit is abandoned and retried. With DYNAMODB_TIMEOUT=60ms, a client concurrency of 160 delivered 20 TPS at a P99 of 28 seconds, two orders of magnitude worse than the same load without it. The HTTP client timeout is global, so it also cut off idempotency lookups and history writes, and it covers the whole request rather than the server's service time. Integrity still passed, and no transaction was applied twice. A configuration wrong enough to cut throughput by two orders of magnitude degraded into slowness, not into wrong money.

A second attempt hedged the confirmation read instead of the commit. At a 30 ms delay, it armed on 6.0% of commits, won on 1.57%, and wasted a read on 4.4%, and the P99 didn't move, so we discarded it. The service now includes an opt-in commit hedge that sends the identical, frozen request a second time. Both copies carry the same ClientRequestToken, so DynamoDB de-duplicates them, and the batch record's attribute_not_exists condition rules out a second effect in any case. It doubles the commit's write load, and we haven't measured it on AWS yet.

### Faster but wrong doesn't count

Two builds in our reports produced results that don't count. The build that folded the recovery marker onto the batch record reached 13,623 TPS at a P99 of 72 ms and could apply a batch twice after a restart. The history key without the batch ID lost 5.2% of history with an exact balance and a passing integrity check, because the check compared balances and counted writes issued rather than rows that survived. We count rows now.

A performance number isn't a result until its correctness properties are stated next to it. Both of these would have made a better chart. One of them could move money twice, and the other lost history without an error.

### Model checking found what review missed

We model the design, not the code, in [P](https://p-org.github.io/P/), whose checker searches the interleavings of communicating state machines for one that breaks an invariant. The model has machines for the durable store, the idempotency layer, per-account activation, the committer, and materialization. Its properties are written separately from the machines, and they include exactly-once, conservation of money, non-negativity at every step of the fold, atomic account opening, terminal decisions, and a liveness property that every effect eventually becomes durable.

The suite has 61 cases, most of them in safe and unsafe pairs. An unsafe case runs the design without a fix and must fail, which proves that the monitor can see the defect. Its safe partner runs the shipped design and must be clean. Of the five defects the model reproduces, one had been found by neither testing nor review, and it's the stranded-claims interleaving from step 5, which needs three independent failures in one order.

The model also taught us what a green run means. An external audit later found three ways to apply money twice in the idempotency layer's negative cache, a component the model didn't contain. Two of the operations involved appeared in it zero times. A fourth finding came from a model that was stronger than the code, with one process-wide index where the code keeps one per account. Each time, the suite passed, because an omission in a model shows up as a pass. The model now reproduces all four, and we model any change that adds a partial-failure boundary or cached state on the money path before we benchmark it.

### Cold accounts are a different workload

Every result above is one hot account. A task holding thousands of accounts that each send about one transaction per second is a different workload: batches of one, nothing to amortize, and 14 write units per transaction instead of about 1.5. Steady-state latency there didn't depend on the account count in the range we measured, with a P99 of 52–58 ms at 300 accounts, 55 ms at 600, and 49–56 ms at 900. At 2,000 accounts, the target was missed at 115–164 ms, and the cause of that tail beyond P90 is still open.

One run at 900 accounts read a P99 of 122 ms, and it nearly became a published density limit. It was a 120-second run, so each account's first transaction, which pays for activation, made up 0.83% of all requests, just inside the 99th percentile. At 600 seconds, the share was 0.17% and the P99 returned to 49–56 ms. When an event is paid once per entity and the run is short, divide the number of entities by the number of requests before you quote a percentile.

## Considerations

Before you adopt this design, weigh what it costs and where it stops helping:

- **A latency floor.** Callers wait for a window and a durable commit. With no queue at all, the commit alone measured a P99 of 59 ms, so a P99 well below about 60 ms isn't reachable without acknowledging a transaction before it's durable, which the invariants rule out.

- **One owner per account.** Fencing makes a second owner safe, not productive, and the in-memory index that closes the cross-account idempotency window lives in one process. Scaling out means assigning accounts to tasks, with a lease or a coordinator in front of the fencing token.

- **A bounded idempotency window.** Durable records expire with their TTL (24 hours in our configuration), and DynamoDB deletes expired items asynchronously, typically within a few days. A duplicate that arrives after its record has expired can be applied again. The transaction ID is also the entire key, so a client that reuses an ID with a different amount receives the original decision.

- **Reads per transaction.** Each idempotency lookup costs one strongly consistent read unit per transaction, so read capacity needs the same sizing care as write capacity.

- **Nothing to batch.** An account that sends a transaction every few seconds pays the whole fixed cost of a batch for each transaction, 14 write units in our measurements.

- **Slices are a per-account decision.** They pay off for a hot account under a tight tail budget. For accounts that are touched rarely or hold thin balances, they cost more than they save.

- **The aggregate ceiling isn't measured.** We haven't measured how far one task goes across many hot accounts on the current build. Our recorded multi-account peak had a materialization lag of more than ten minutes, so we don't quote it as a result.

- **No authentication in the reference API.** The HTTP API we benchmarked has no authentication or authorization. Add both before it serves anything outside a test environment.

## Conclusion

A ledger doesn't need to write an account's balance item once per transaction. When one writer owns each account, it can fold a window of transactions in memory and commit the result with a single conditional TransactWriteItems call, and the hottest item in the table is then written a few times per second while the account processes thousands of transactions in each of those seconds.

Keeping that exactly-once took as much design as the batching did, and most of it is about order: what goes into the commit, what's written after it, and what a new owner must recover before it serves. If you're building a ledger, a wallet, or any system with hot aggregates on DynamoDB, start by separating the access patterns that run once per transaction from those that run once per batch, and measure the per-transaction ones in the capacity units DynamoDB reports.

To go further, see [Managing complex workflows with DynamoDB transactions](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/transactions.html), [Take advantage of sparse indexes](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/bp-indexes-general-sparse-indexes.html), and [DynamoDB burst and adaptive capacity](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/burst-adaptive-capacity.html) in the Amazon DynamoDB Developer Guide. If you've approached hot accounts differently, we'd like to hear about it in the comments.

## About the authors

### &lt;Author name&gt;

*&lt;Author name&gt; is a &lt;role&gt; at AWS. &lt;Two to three more sentences in the third person about the author's team and focus area.&gt;*
