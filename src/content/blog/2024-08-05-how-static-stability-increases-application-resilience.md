---
title: "How static stability increases your application's resilience"
seoTitle: "How static stability increases your application's resilience"
description: "How the concept of static stability and a few related patterns can make distributed applications more resilient."
pubDate: 2024-08-05
tags: ["AWS", "Resilience", "Reliability", "Microservices", "High Availability"]
series: "Cloud Resilience"
language: "en"
---

![Article image 1](/blog/estabilidade-estatica/image-01.jpg)

**TLDR;** After almost 5 years without writing anything here, I decided to write again. Last time, I was writing a series of articles focused on frontend, mainly ReactJs and deeper JavaScript topics. Now, working at AWS as a Principal Cloud Application Architect, I am much closer to the context of **cloud computing and application resilience**, and that is what I will start writing about from now on.

With people, companies, teams, and applications increasingly connected 24 hours a day, the market increasingly demands that applications and services remain always operating and without failures. At least that is what every customer wants and expects when using a service. But being much more pragmatic, “always operating and without failures” does not exist. We are increasingly moving from centralized to distributed contexts, in teams, applications, and businesses.

In this increasingly distributed context, I really like Amazon CTO Werner Vogels' phrase: “Everything fails all the time.” It is with this mindset that we need to design our applications to be more resilient.

## What is resilience in software development?

You can find different definitions for this, but AWS defines resilience as follows:

“Resilience is the ability of an application to resist or recover from disruptions, including those related to infrastructure, dependent services, misconfigurations, transient network issues, and load spikes.” [_https://aws.amazon.com/resilience_](https://aws.amazon.com/resilience)

In future posts I will write more broadly about how to develop applications with a focus on resilience. For now, for an application to be resilient, we need to design it for high availability. AWS defines [availability](https://docs.aws.amazon.com/wellarchitected/latest/reliability-pillar/availability.html) as the percentage of time that an application is available for use, and high availability as the ability of an application to continue operating when partial failures of its components or common errors happen. To achieve high availability, the most common practices are redundancy and replication of application components. If your application is running on AWS, it “has high availability” when it operates across multiple Availability Zones.

## What is static stability?

Static stability is a concept widely used at AWS to increase the resilience of critical applications. This [excellent article](https://aws.amazon.com/builders-library/static-stability-using-availability-zones/) has examples of how AWS uses this concept in Amazon EC2, and in this post I will give more examples of how you can use it in your application.

> “**Static stability** is the ability of an application to continue operating without changing **its state** when its dependencies are totally or partially unavailable.”

An application can be highly available and still not be statically stable. In other words, when its dependencies become unavailable, it can also become unavailable. Some approaches for an application to have static stability are:

- segregate your applications into independent services;
- pre-provision resources;
- avoid circular dependencies;
- keep the current state of the application and prioritize asynchronous communication;
- prioritize asynchronous communication.

With the definitions in place, let's move to examples for a better understanding.

## Segregate your applications into independent services

AWS separates most services between control plane and data plane. These terms come from network routers. The router data plane, which is its main function, moves packets based on rules. But routing policies need to be created and distributed from somewhere, and that is where the control plane comes in.

In the case of Amazon DynamoDB, for example, when you create a table you are using control plane APIs and functions, and when you insert records into the table you are using data plane APIs and functions.

![Article image 2](/blog/estabilidade-estatica/image-02.png)

In the diagram above we also have the diagram of [how AWS recommends a SaaS application should be architected](https://docs.aws.amazon.com/whitepapers/latest/saas-architecture-fundamentals/control-plane-vs.-application-plane.html). The key point here is that you segregate your application into independent services. In AWS services, the control plane is independent from the data plane. If a failure happens in the data plane, the control plane is not affected and continues operating normally, and vice versa. This is an example of static stability. Although the control plane and data plane are part of the same application, if one becomes unavailable the other continues operating normally without any configuration or “state” change. Good examples of how data plane and control plane can communicate are in this Amazon Builders Library article: [Avoiding overload in distributed systems by putting the smaller service in control](https://aws.amazon.com/pt/builders-library/avoiding-overload-in-distributed-systems-by-putting-the-smaller-service-in-control/).

**What if this control plane and data plane split does not make sense for my application?** Even then, the same principles of modularity and segregation into independent services apply. I would say the tip here is to separate the critical APIs of your applications from the others. For example, suppose you have a credit card payment authorization application. It is useful to keep the API that authorizes payments separate from the service that configures a new credit card for the customer.

![Article image 3](/blog/estabilidade-estatica/image-03.png)

## Pre-provision resources (over provisioned)

This is a controversial item, but let's take it calmly. Robisson! Isn't one of the great advantages of cloud exactly paying only for the capacity I need and being able to scale resources up or down when necessary? The short answer is YES, but as with everything in software development, it depends. The idea here is that some approaches to static stability may only be worth the cost and complexity for your most critical applications. You should weigh the pros and cons of each approach I am suggesting in this post.

To make it clearer, let's look at an example of an application with and without high availability, and with and without static stability.

## Without high availability and without static stability

In the image below we can see an example of a very simple application composed of an EC2 instance that accesses an RDS database. But what happens if the EC2 instance or the database becomes unavailable for any reason? The application becomes unavailable to its customers.

![Article image 4](/blog/estabilidade-estatica/image-04.png)

This application does not have high availability, because it cannot continue operating if any of its components fails.

## High availability without static stability

Now let's improve the architecture of our application so that it has high availability. We will start running our application in more than one Availability Zone, placing EC2 instances redundantly and also the database. In addition, we will add a load balancer to distribute traffic across multiple zones and use Amazon EC2 Auto Scaling to ensure that we have instances running in more than one AZ in the quantity we want, which in this example is two instances.

![Article image 5](/blog/estabilidade-estatica/image-05.png)

Now our application can continue operating through a series of failures that can happen, such as:

- Unavailability of an AZ
- Failure of an application instance
- Failure of a database instance
- Transient network failures, where the load balancer can route traffic to another AZ
- Excess load, where EC2 Auto Scaling can provision more instances if it sees that the current instances are overloaded

But even with high availability, this application **does not have static stability**. To illustrate, suddenly AZ1 becomes unavailable. What happens to the behavior of your application? The following happens:

- The Load Balancer, through health checking, notices that your instances in AZ1 are failing and stops routing traffic to that AZ1.
- If before you had all traffic distributed across 3 AZs and 3 instances, now it is distributed across only 2 instances.
- Auto Scaling, through health checking, notices that an instance became unavailable and provisions a new instance in one of the other two AZs that are still available.
- RDS notices that the primary instance in AZ1 became unavailable and performs failover to the instance in AZ2.

At first, everything worked well here. When an AZ failed, your application continued operating. But there are important details that prevent this approach from achieving static stability:

- **The state of the application had to change**, both the load balancer and auto scaling had to change their configurations to evacuate traffic from an AZ and to provision a new instance.
- This **state change** **takes time**, from seconds to minutes. Can your applications tolerate this unavailability or overload for that time?
- This state change often **requires using AWS control plane APIs**, such as Auto Scaling, which uses EC2 control plane APIs when provisioning a new instance. Now imagine that at the same time AZ1 becomes unavailable, the EC2 control plane also becomes unavailable. What happens? Your application will not get a new instance provisioned to replace the missing AZ1.
- If you have instances provisioned across three AZs to support 100% of your traffic, losing one AZ means losing 33% of capacity, and that may overload the remaining instances before Auto Scaling can respond and provision new instances.

For mission-critical applications, even seconds of unavailability can have a major impact on the customer experience or the business as a whole. In addition, every example above becomes worse when you are working with a system that already operates at large scale. Imagine an application that operates with 500 instances in each AZ and that AZ becomes unavailable. Now there is a need to provision 500 instances as quickly as possible, while also supporting the traffic from those 500 instances that became unavailable.

Now let's see how we can mitigate these problems by adding static stability to our application.

## High availability with static stability

The image below shows the architecture required to have high availability and static stability. Let's now see what changes when the same failures happen in this architecture, such as an AZ failure highlighted in red in the image below.

![Article image 6](/blog/estabilidade-estatica/image-06.png)

First, let's look at the changes in our architecture to achieve static stability. Now we have one load balancer per AZ. But this does not need to be so literal, because the main point is to eliminate cross-AZ traffic. With AWS ELB you can [disable cross-zone traffic](https://docs.aws.amazon.com/elasticloadbalancing/latest/application/disable-cross-zone.html), or configure three load balancers and assign one to each AZ, keeping each one outside the same failure domain. With this change, we reduce cross-AZ traffic and, in the event of an AZ failure, through [Route 53 Health Checking](https://docs.aws.amazon.com/Route53/latest/DeveloperGuide/dns-failover.html), we can stop sending traffic to that AZ without changing configuration, or state, at the moment of failure.

If before the application had 3 EC2 instances, one in each AZ, to support 100% of traffic, now it has six, two in each AZ. This way, even if one AZ fails, there are still enough instances to support 100% of traffic without needing to scale the application with EC2 Auto Scaling. This approach also partially protects the application if it receives a traffic spike above normal, because there is margin to continue operating normally without needing to scale the application.

You do not necessarily need to provision 200% of your traffic. It is more a matter of calculating how much capacity you need versus the number of AZs you are using and the maximum percentage of traffic required. As in the [Well-Architected Framework example](https://docs.aws.amazon.com/wellarchitected/latest/reliability-pillar/rel_withstand_component_failures_static_stability.html):

![Article image 7](/blog/estabilidade-estatica/image-07.png)

This approach is valid when the financial and reputational impact of the business is greater than the cost of pre-provisioning resources. That is why it is not for every business application, but for the most critical ones that justify this approach.

## Avoid circular dependencies

A circular dependency happens when two or more modules or software components depend on each other, directly or indirectly, creating a dependency cycle. This can be problematic because it can lead to maintenance difficulties, build and runtime issues, greater code management complexity, and prevent dependent systems from recovering if one of them fails. The image below shows a simple example of a circular dependency.

![Article image 8](/blog/estabilidade-estatica/image-08.png)

When designing a resilient application, avoiding circular dependencies is fundamental to achieving static stability. When components are interdependent, a failure in one can quickly propagate to others, making the entire system vulnerable. On the other hand, by avoiding these dependencies, we ensure that each component can operate independently, even if other parts of the system are experiencing problems. This allows the application to continue working correctly, or at least degrade gracefully, without state changes or emergency interventions.

For example, imagine a payment service that depends on an authentication service to validate transactions, while the authentication service also depends on the payment service to charge authentication fees. If one of these services fails, the other will also be affected, creating a single point of failure. By designing the architecture to avoid this interdependence, we can ensure that the payment service can continue processing transactions even if the authentication service is unavailable, using alternative mechanisms or degraded operating modes.

In short, by avoiding circular dependencies, we increase the application's ability to resist failures and improve its overall resilience. This is particularly crucial in distributed and complex environments, where static stability can be the difference between a minor disruption and a catastrophic system failure.

## Keep the current state of the application

Keeping the current state of the application, also known as **state immutability**, is a practice that involves avoiding changes to the internal state of components or services in response to external events or failures. This approach contributes significantly to static stability, because it ensures that the application can continue operating in a known and safe state, even during interruptions or failures.

When an application keeps its state immutable, it becomes more predictable and easier to manage. In situations where an external dependency becomes unavailable, the application can choose to return precomputed results or operate in a limited mode instead of trying to change its state or perform actions that may fail due to dependency unavailability. This avoids scenarios where unexpected or inconsistent state changes can lead to additional errors or unpredictable behavior.

In addition, by keeping the current state, the application can be quickly restored to a known functional state after a failure. This is particularly important in distributed systems, where state recovery can be complex and prone to additional failures if not carefully managed.

**Example approaches**

- **Cache services:** A classic example is using caches to store responses from database queries or external services. If the backend service is temporarily unavailable, the application can return cached data, keeping the current state of data presented to the customer instead of showing a failure or trying to change state.
- **Feature Flags:** Another approach is using feature flags to control application behavior. These flags can safely enable or disable features without needing to modify the internal state of the application. If a new feature is causing problems, it can be disabled instantly without impacting the application's overall state.
- **Immutable Configurations:** Keeping application configurations in immutable files, such as configuration files in containers or cloud environments, ensures that unintended or uncontrolled changes do not affect application behavior. This also enables easy rollback to a previous known configuration if something goes wrong.
- **EC2 Instance:** When an EC2 instance is restarted, manually or automatically by the system, for example after a maintenance event, it can restart with the same operating system, configurations, and data present in the attached EBS volumes. This allows the instance to keep its previous state without data or configuration loss. In the event of control plane failures, it may not be possible to provision new instances, but the current ones continue working. Even if an error causes the instance to restart, it can start with the previous state.

## Prioritize asynchronous communication

Prioritizing asynchronous communication in software architectures involves separating components that communicate through messages or events instead of direct synchronous calls. In this model, systems do not need to wait for an immediate response to continue their operations. Instead, they can send a message or event to another service or component and continue their own tasks while the message is processed independently.

![Article image 9](/blog/estabilidade-estatica/image-09.png)

Asynchronous communication allows components to be decoupled, which means that the unavailability or increased latency of one service does not directly impact other services. If a service is temporarily unavailable, messages can be stored in a queue or buffer and processed as soon as the service becomes available again, without affecting the system as a whole.

In an asynchronous system, if a service fails to process a message, the system can implement automatic retry mechanisms, such as a message queue with [exponential backoff](https://aws.amazon.com/pt/builders-library/timeouts-retries-and-backoff-with-jitter/). This allows the system to keep trying to process the message until the failure is resolved, increasing resilience and ensuring operations are eventually completed.

Asynchronous communication makes horizontal scalability easier, because messages can be processed by multiple service instances. This allows workload to be distributed efficiently and the number of instances to be dynamically adjusted as needed to handle demand spikes. In addition, new features or services can be added or updated independently, without interrupting existing systems.

In synchronous systems, a service can be blocked waiting for a response from another service, which can lead to performance issues and bottlenecks. Asynchronous communication removes these blocks, allowing services to operate independently and efficiently, improving system responsiveness.

**Example approaches**

- **Messaging with Amazon SQS:** A classic example is using Amazon Simple Queue Service (SQS) to implement message queues between services. For example, an e-commerce order service can put a message in a queue when an order is created, while payment, inventory, and shipping services consume those messages asynchronously to process the order. This allows the order service to continue operating even if other services are temporarily unavailable.
- **Events with Amazon SNS:** Amazon Simple Notification Service (SNS) can be used to publish events that other services or systems can subscribe to and process asynchronously. For example, a health monitoring system can publish events when it detects a problem, and several services can react to those events, such as sending notifications, starting mitigation procedures, or recording logs.
- **Event-driven architectures in general:** In an event-driven architecture, services react to events emitted by other services. For example, a social network application can emit events when a new post is created, and other services such as notifications, analytics, or activity feeds can process those events independently, without depending on a synchronous response.

By prioritizing asynchronous communication, systems become more resilient and stable, able to handle failures and workload variations without compromising availability and functionality. This approach is especially useful in distributed environments, where network latency and service availability can vary.

## Conclusion

Static stability is a fundamental concept in resilient systems architecture. It refers to the ability of an application to keep its state and continue operating even when some of its dependencies fail or become unavailable. This characteristic is crucial to ensure service continuity, minimize interruptions, and provide a consistent customer experience, especially in distributed and highly available environments.

## Key points to achieve static stability:

- **Service segregation:** Divide the application into independent components, such as separating the control plane from the data plane. This helps limit the impact of failures to a specific part of the system, allowing other components to continue operating normally.
- **Resource pre-provisioning:** Allocate enough resources to support expected traffic even in failure scenarios. For example, use redundant instances and avoid depending on automatic scaling during critical moments to ensure the application can handle demand spikes without problems.
- **Avoid circular dependencies:** Design the system so that components do not depend on each other in a cycle. This prevents situations where a failure in one component can cause cascading failures in other components, compromising system stability.
- **Keep the current state of the application:** Ensure that the application's state does not unexpectedly change in response to failures. This allows the system to be quickly restored to a known and functional state.
- **Prioritize asynchronous communication:** Adopt event-driven architectures to decouple components. This allows services to send and receive messages without waiting for immediate responses, making the system more fault tolerant and more flexible in terms of scalability.

## When to use static stability

Static stability should be considered essential in critical and highly available systems, where even small periods of downtime can have significant consequences, such as in financial systems, healthcare, e-commerce, or communication infrastructure. It is particularly useful in distributed environments, where network latency and service availability can vary, and in scenarios where fast failure recovery is crucial to maintaining service continuity.

In addition, static stability is important in situations where the financial or reputational impact of a failure is high, justifying the investment in more robust design practices. In systems where customer experience must remain consistent even during internal failures, static stability can be the difference between an imperceptible disruption and a significantly negative customer experience.
