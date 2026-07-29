---
title: "Como a estabilidade estática aumenta a resiliência da sua aplicação"
seoTitle: "Como a estabilidade estática aumenta a resiliência da sua aplicação"
description: "Vamos ver como o conceito de estabilidade estática e alguns padrões dessa abordagem podem tornar as aplicações distribuídas mais resilientes."
pubDate: 2024-08-05
tags: ["AWS", "Resilience", "Reliability", "Microservices", "High Availability"]
series: "Cloud Resilience"
language: "pt-BR"
---

![Imagem 1 do artigo](/blog/estabilidade-estatica/image-01.jpg)

**TLDR;** Depois de quase 5 anos sem escrever nada aqui, resolvi voltar a escrever. Da última vez, eu estava numa série de artigos voltados ao frontend, principalmente ReactJs e assuntos mais profundos de Javascript. Agora trabalhando na AWS como Principal Cloud Application Architect, estou muito inserido no contexto de **cloud computing e resiliência de aplicações** e é sobre isso que vou começar a escrever a partir de agora.

Atualmente com as pessoas, empresas, times e aplicações cada vez mais conectadas 24 horas por dia é cada vez mais demandado pelo mercado que as aplicações e serviços desenvolvidos estejam operando sempre e sem falhas. Pelo menos isso é o desejado e esperado por qualquer cliente no momento que vai utilizar um serviço. Mas sendo bem mais pragmático o “operando sempre e sem falhas” não existe, estamos cada vez mais num contexto de migração do centralizado para o distribuído, em termos de times, aplicações e negócios.

Nesse contexto cada vez mais distribuído, gosto muito da frase do CTO da Amazon Werner Vogels que diz “Everything fail all the time” que traduzindo seria “Todas as coisas falham o tempo todo”. E é em cima desse “mindset” que precisamos fazer o design de nossas aplicações a fim de serem mais resilientes.

## O que é resiliência no contexto de desenvolvimento de software?

Você pode encontrar algumas definições diferentes sobre isso, mas a AWS define resiliência da seguinte forma:

“A resiliência é a capacidade de uma aplicação resistir ou se recuperar de interrupções, incluindo aquelas relacionadas à infraestrutura, serviços dependentes, configurações incorretas, problemas transitórios de rede e picos de carga.” [_https://aws.amazon.com/resilience_](https://aws.amazon.com/resilience)

Em outros posts mais a frente vou escrever mais sobre como desenvolver aplicações com foco em resiliência de forma mais abrangente. Por agora, para uma aplicação ser resiliente precisamos fazer o design dela para ter alta disponibilidade(high availability). A AWS define [disponibilidade](https://docs.aws.amazon.com/wellarchitected/latest/reliability-pillar/availability.html) como o percentual de tempo que uma aplicação está disponível para uso e alta disponibilidade como a capacidade de uma aplicação continuar operando quando falhas parciais de seus componentes ou erros mais comuns acontecem. Para ter alta disponibilidade, as práticas mais comuns são redundância e replicação dos componentes da aplicação. Se você tem sua aplicação rodando na AWS, ela “tem alta disponibilidade”(várias aspas aqui, porque não basta apenas isso!!!) quando está operando em múltiplas zonas de disponibilidade

## O que é estabilidade estática?

A estabilidade estática é um conceito muito utilizado na AWS para aumentar a resiliência de aplicações críticas. Neste [excelente artigo](https://aws.amazon.com/builders-library/static-stability-using-availability-zones/) tem alguns exemplos de como a AWS usa esse conceito no Amazon EC2 e vou dar mais alguns exemplos nesse post de como você pode usar para a sua aplicação.

> “**Estabilidade estática** é a capacidade de uma aplicação continuar operando sem mudar o **seu estado** quando suas dependências estão indisponíveis total ou parcialmente.”

Uma aplicação pode ter alta disponibilidade e ainda assim não ser estaticamente estável, ou seja, na indisponibilidade de suas dependências, ela também pode se tornar indisponível. Algumas abordagens para uma aplicação ter estabilidade estática são:

- segregar suas aplicações em serviços independentes;
- pré-provisionar recursos;
- evitar dependências circulares;
- manter o estado atual da aplicação e priorizar comunicação assíncrona;
- priorizar a comunicação assíncrona.

Com todas as definições postas, vamos a exemplos para um melhor entendimento.

## Segregar suas aplicações em serviços independentes

A AWS separa a maioria dos serviços entre control plane e data plane. Esses termos vêm dos roteadores de redes. O data plane do roteador, que é sua função principal, movimenta pacotes com base em regras. Mas as políticas de roteamento precisam ser criadas e distribuídas de algum lugar, e é aí que entra o control plane.

No caso do Amazon DynamoDB por exemplo, quando você está criando uma tabela, está usando APIs e funções do control plane, e quando está inserindo registros na tabela está usando APIs e funções do data plane.

![Imagem 2 do artigo](/blog/estabilidade-estatica/image-02.png)

No diagrama acima também temos o diagrama de [como a AWS recomenda que uma aplicação SAAS seja arquitetada](https://docs.aws.amazon.com/whitepapers/latest/saas-architecture-fundamentals/control-plane-vs.-application-plane.html). O ponto-chave aqui é que você segregue sua aplicação em serviços independentes, no caso dos serviços AWS, o control plane é independente do data plane. Se uma falha ocorre no data plane, o control plane não é atingido e continua operando normalmente e vice-versa. Esse é um exemplo de estabilidade estática, embora o control plane e data plane sejam parte da mesma aplicação, se um deles se torna indisponível o outro continua operando normalmente sem a necessidade de qualquer alteração de configuração ou de “estado” da mesma. Bons exemplos de como data plane e control plane podem se comunicar estão nesse artigo do Amazon Builders Library [Avoiding overload in distributed systems by putting the smaller service in control](https://aws.amazon.com/pt/builders-library/avoiding-overload-in-distributed-systems-by-putting-the-smaller-service-in-control/).

**E se para a minha aplicação não fizer sentido essa divisão entre control plane e data plane?** Ainda assim, os mesmos princípios de modularidade e segregação em serviços independentes se aplicam. Eu diria que a dica aqui é separar as APIs críticas das suas aplicações das demais. Por exemplo, digamos que você tenha uma aplicação de autorização de pagamentos por cartão de crédito. É interessante deixar a API que autoriza os pagamentos separada do serviço que configura um novo cartão de crédito para o cliente.

![Imagem 3 do artigo](/blog/estabilidade-estatica/image-03.png)

## Pré-provisionar recursos(over provisioned)

Este é um item polêmico, mas vamos com calma. Robisson! Uma das grandes vantagens da cloud não é justamente pegar somente pelo que eu preciso de capacidade e conseguir escalar recursos para mais ou para menos quando necessário ? A resposta curta é SIM, mas como tudo em desenvolvimento de software, depende. A ideia aqui é que algumas abordagens para se ter estabilidade estática talvez só vá valer o custo e complexidade para as suas aplicações mais críticas, você deve ponderar os prós e contras de se utilizar cada abordagem que estou sugerindo nesse post.

Para ficar mais claro, vamos ver um exemplo de uma aplicação com e sem alta disponibilidade e com e sem estabilidade estática.

## Sem alta disponibilidade e sem estabilidade estática

Na imagem abaixo podemos ver um exemplo de uma aplicação bem simples que é composta de uma instância EC2 que acessa um banco de dados RDS. Mas o que acontece se a instância EC2 ou o banco de dados se tornam indisponíveis por qualquer motivo que seja ? A aplicação se torna indisponível para os seus clientes.

![Imagem 4 do artigo](/blog/estabilidade-estatica/image-04.png)

Essa aplicação não possui alta disponibilidade, pois ela não consegue continuar operando se qualquer um dos seus componentes falhar.

## Alta disponibilidade e sem estabilidade estática

Agora vamos melhorar a arquitetura da nossa aplicação para que ela tenha alta disponibilidade. Vamos começar a executar nossa aplicação em mais de uma zona de disponibilidade, colocando instâncias EC2 em redundância e também o banco de dados. Além disso vamos adicionar um balanceador de carga para distribuir o tráfego entre as múltiplas zonas e vamos utilizar o Amazon EC2 Auto Scaling para garantir que temos instâncias executando em mais de uma AZ na quantidade que queremos, que nesse exemplo é duas instâncias.

![Imagem 5 do artigo](/blog/estabilidade-estatica/image-05.png)

Agora a nossa aplicação consegue continuar operando diante de uma série de falhas que podem acontecer, como por exemplo:

- Indisponibilidade de uma AZ
- Falha de uma instância da aplicação
- Falha de uma instância de banco de dados
- Falhas transientes de redes, onde o balanceador de carga pode direcionar o tráfego para outra AZ também
- Excesso de carga, onde o EC2 Auto Scaling pode provisionar mais instâncias se perceber que as instâncias atuais estão sobrecarregadas

Mas mesmo com alta disponibilidade essa aplicação **não tem estabilidade estática**. Para exemplificar, de repente a AZ1 se tornou indisponível, o que acontece com o comportamento da sua aplicação? Acontece o seguinte:

- O Load Balancer via health checking vai perceber que suas instâncias na AZ1 estão falhando e vai parar de direcionar tráfego para essa AZ1
- Se antes você tinha todo o tráfego sendo distribuído em 3 AZs e para 3 instâncias, agora está apenas em 2 instâncias.
- O Auto Scaling via health checking vai perceber que uma instância se tornou indisponível e vai provisionar uma nova instância em uma das outras duas AZs que ainda estão disponíveis.
- O serviço RDS vai perceber que a instância primária da AZ1 se tornou indisponível e vai fazer o failover para a instância da AZ2

A princípio, tudo funcionou bem aqui, na falha de uma AZ, sua aplicação continuou operando. Mas tem alguns detalhes importantes aqui que impedem essa abordagem de alcançar a estabilidade estática:

- **O estado da aplicação precisou ser alterado**, tanto o load balancer, quando o auto scaling tiveram que mudar suas configurações para evacuar o tráfego de uma AZ, quanto para provisionar uma nova instância.
- Essa **mudança de estado** **leva tempo**, de segundos a minutos. Será que suas aplicações podem suportar essa indisponibilidade ou sobrecarga por esse tempo ?
- Essa mudança de estado muitas vezes **requer o uso de APIs do control plane** da AWS, como por exemplo o Autoscaling que ao provisionar uma nova instância, usa as APIs de control plane do serviço EC2. Agora imagine se ao mesmo tempo que a AZ1 fica indisponível, o control plane do EC2 fica indisponível também, o que acontece ? Sua aplicação não vai ter uma nova instância provisionada para suprir a falta da AZ1.
- Se você está com instâncias provisionadas em três AZs para suportar 100% do seu tráfego, perde uma AZ significa perder 33% de capacidade e pode ser que isso sobrecarregue as demais instâncias antes mesmo do Auto Scaling conseguir responder e provisionar novas instâncias

Para aplicações de missão crítica, até mesmo segundos indisponíveis podem representar um grande impacto na experiência do cliente ou no negócio como um todo. Além disso, cada exemplo que dei acima se torna pior se você está trabalhando com um sistema que já opera em grande escala. Imagine uma aplicação que opera com 500 instâncias em cada AZ e esta AZ se torna indisponível. Agora há a necessidade de provisionar 500 instâncias o mais rápido possível, além de suportar o tráfego dessas 500 instâncias que se tornaram indisponíveis.

Agora vamos ver como podemos mitigar esses problemas adicionando estabilidade estática na nossa aplicação.

## Alta disponibilidade com estabilidade estática

Na imagem abaixo está a arquitetura necessária para se ter alta disponibilidade e também estabilidade estática. Vamos ver agora o que muda quando as mesmas falhas acontecem nessa arquitetura, como a falha de uma AZ, destacado em vermelho na imagem abaixo.

![Imagem 6 do artigo](/blog/estabilidade-estatica/image-06.png)

Primeiro vamos ver as alterações em nossa arquitetura para atingir a estabilidade estática. Agora temos um load balancer por AZ. Mas isso não precisa ser tão literal, pois o principal é eliminar o tráfego cross-az. Com AWS ELB pode-se [desabilitar o trafego cross-az](https://docs.aws.amazon.com/elasticloadbalancing/latest/application/disable-cross-zone.html) ou configurar três load balancers e configurar um para cada AZ, deixando assim cada um deles fora do mesmo domínio de falha. Com essa alteração reduzimos o tráfego cross-az e no caso de uma falha de uma AZ, via [Route53 Health Checking](https://docs.aws.amazon.com/Route53/latest/DeveloperGuide/dns-failover.html), podemos deixar de enviar tráfego para essa AZ, sem precisar alterar a configuração(o estado da mesma) no momento da falha.

Se antes para suportar 100% do tráfego, a aplicação tinha 3 instâncias EC2, uma em cada AZ, agora tem seis, duas em cada AZ. Dessa forma, mesmo se um AZ falhar, ainda se tem instâncias suficientes para suportar 100% do tráfego sem a necessidade de escalar a aplicação com a ajuda do EC2 Auto Scaling. Essa abordagem também protege em partes se a aplicação receber um pico de tráfego acima do normal, há margem para continuar operando normalmente sem a necessidade de escalar a aplicação.

Não há necessariamente a necessidade de provisionar 200% do seu trafego, é mais uma questão de calcular quanto de capacidade você precisa versus o numero de AZ que você está utilizando e o percentual máximo de tráfego necessário. Como no [exemplo do well-architected framework](https://docs.aws.amazon.com/wellarchitected/latest/reliability-pillar/rel_withstand_component_failures_static_stability.html):

![Imagem 7 do artigo](/blog/estabilidade-estatica/image-07.png)

Essa abordagem é válida quando o impacto financeiro e para a imagem do negócio é maior que o custo de pré-provisionar recursos, e é por isso que não é para todos as aplicações do negócio e sim para as mais críticas que valem essa abordagem.

## Evitar dependências circulares

Uma dependência circular ocorre quando dois ou mais módulos ou componentes de um software dependem um do outro, direta ou indiretamente, criando um ciclo de dependência. Isso pode ser problemático porque pode levar a uma série de complicações, como dificuldade de manutenção, problemas de compilação e execução, maior complexidade no gerenciamento do código e impedir estes sistemas dependentes de se recuperarem em caso de um deles falhar. A imagem abaixo mostra um exemplo simples de dependência circular.

![Imagem 8 do artigo](/blog/estabilidade-estatica/image-08.png)

Ao projetar uma aplicação resiliente, evitar dependências circulares é fundamental para alcançar a estabilidade estática. Quando os componentes são interdependentes, uma falha em um pode rapidamente propagar-se para outros, tornando todo o sistema vulnerável. Por outro lado, ao evitar essas dependências, garantimos que cada componente possa operar de forma independente, mesmo que outras partes do sistema estejam enfrentando problemas. Isso permite que a aplicação continue a funcionar corretamente, ou pelo menos degrade graciosamente, sem mudanças de estado ou necessidade de intervenções emergenciais.

Por exemplo, imagine um serviço de pagamento que depende de um serviço de autenticação para validar transações, enquanto o serviço de autenticação também depende do serviço de pagamento para cobrar taxas de autenticação. Se um desses serviços falhar, o outro também será afetado, criando um ponto único de falha. Ao desenhar a arquitetura para evitar essa interdependência, podemos garantir que o serviço de pagamento possa continuar processando transações, mesmo se o serviço de autenticação estiver indisponível, utilizando mecanismos alternativos ou modos de operação degradados.

Em resumo, ao evitar dependências circulares, aumentamos a capacidade da aplicação de resistir a falhas e melhorar sua resiliência geral. Isso é particularmente crucial em ambientes distribuídos e complexos, onde a estabilidade estática pode ser a diferença entre uma interrupção menor e uma falha catastrófica do sistema.

## Manter o estado atual da aplicação

Manter o estado atual da aplicação, também conhecido como **imutabilidade de estado**, é uma prática que envolve evitar mudanças no estado interno dos componentes ou serviços em resposta a eventos ou falhas externas. Essa abordagem contribui significativamente para a estabilidade estática, pois garante que a aplicação possa continuar operando em um estado conhecido e seguro, mesmo diante de interrupções ou falhas.

Quando uma aplicação mantém seu estado imutável, ela se torna mais previsível e fácil de gerenciar. Em situações onde uma dependência externa se torna indisponível, a aplicação pode optar por retornar resultados pré-calculados ou operados em um modo limitado, em vez de tentar alterar seu estado ou executar ações que possam falhar devido à indisponibilidade das dependências. Isso evita cenários onde mudanças inesperadas ou inconsistentes de estado possam levar a erros adicionais ou comportamentos imprevisíveis.

Além disso, ao manter o estado atual, a aplicação pode ser rapidamente restaurada a um estado funcional conhecido após uma falha. Isso é particularmente importante em sistemas distribuídos, onde a recuperação de estado pode ser complexa e sujeita a falhas adicionais se não for cuidadosamente gerenciada.

**Exemplos de abordagem**

- **Serviços de Cache:** Um exemplo clássico é o uso de caches para armazenar respostas de consultas a bancos de dados ou serviços externos. Se o serviço de backend estiver temporariamente indisponível, a aplicação pode retornar os dados em cache, mantendo o estado atual dos dados apresentados ao cliente, ao invés de apresentar uma falha ou tentar alterar o estado.
- **Feature Flags:** Outra abordagem é o uso de feature flags para controlar o comportamento da aplicação. Essas flags podem ser usadas para ativar ou desativar funcionalidades de forma segura, sem necessidade de modificar o estado interno da aplicação. Se uma nova funcionalidade estiver causando problemas, ela pode ser desativada instantaneamente sem impactar o estado geral da aplicação.
- **Configurações Imutáveis:** Manter configurações de aplicação em arquivos imutáveis, como arquivos de configuração em contêineres ou ambientes de cloud, assegura que mudanças não intencionais ou não controladas não afetem o comportamento da aplicação. Isso também permite uma fácil reversão para uma configuração anterior e conhecida, caso algo dê errado.
- **Instância de EC2:** Quando uma instância EC2 é reiniciada (seja manualmente ou automaticamente pelo sistema, por exemplo, após um evento de manutenção), ela pode recomeçar com o mesmo sistema operacional, configurações e dados presentes nos volumes EBS anexados. Isso permite que a instância mantenha o estado anterior, sem perda de dados ou configurações. Em caso de falhas de control plane, pode não ser possível provisionar novas instâncias, mas as atuais continuam funcionando. Mesmo que ocorra um erro fazendo a instância reiniciar, ela pode iniciar com o estado anterior.

## Priorizar a comunicação assíncrona

Priorizar a comunicação assíncrona em arquiteturas de software envolve a separação de componentes que se comunicam através de mensagens ou eventos, em vez de chamadas síncronas diretas. Nesse modelo, os sistemas não precisam esperar por uma resposta imediata para continuar suas operações. Em vez disso, eles podem enviar uma mensagem ou evento para outro serviço ou componente e prosseguir com suas próprias tarefas, enquanto a mensagem é processada de forma independente.

![Imagem 9 do artigo](/blog/estabilidade-estatica/image-09.png)

A comunicação assíncrona permite que os componentes sejam desacoplados, o que significa que a indisponibilidade ou o aumento de latência de um serviço não impacta diretamente outros serviços. Se um serviço está temporariamente indisponível, as mensagens podem ser armazenadas em uma fila ou buffer e processadas assim que o serviço estiver disponível novamente, sem que o sistema como um todo seja afetado.

Em um sistema assíncrono, se um serviço falha ao processar uma mensagem, o sistema pode implementar mecanismos de retentativa automática, como uma fila de mensagens com [exponential-backoff](https://aws.amazon.com/pt/builders-library/timeouts-retries-and-backoff-with-jitter/). Isso permite que o sistema continue tentando processar a mensagem até que a falha seja resolvida, aumentando a resiliência e garantindo que as operações sejam eventualmente concluídas.

A comunicação assíncrona facilita a escalabilidade horizontal, pois as mensagens podem ser processadas por várias instâncias de um serviço. Isso permite distribuir a carga de trabalho de forma eficiente e ajustar dinamicamente o número de instâncias conforme necessário para lidar com picos de demanda. Além disso, novas funcionalidades ou serviços podem ser adicionados ou atualizados de forma independente, sem interromper o funcionamento dos sistemas existentes.

Em sistemas síncronos, um serviço pode ficar bloqueado esperando por uma resposta de outro serviço, o que pode levar a problemas de desempenho e a gargalos. A comunicação assíncrona elimina esses bloqueios, permitindo que os serviços funcionem de forma independente e eficiente, melhorando a capacidade de resposta do sistema.

**Exemplos de abordagem**

- **Mensageria com Amazon SQS:** Um exemplo clássico é o uso do Amazon Simple Queue Service (SQS) para implementar filas de mensagens entre serviços. Por exemplo, um serviço de pedidos de e-commerce pode colocar uma mensagem em uma fila quando um pedido é criado, enquanto serviços de pagamento, estoque e envio consomem essas mensagens de forma assíncrona para processar o pedido. Isso permite que o serviço de pedidos continue operando mesmo que outros serviços estejam temporariamente indisponíveis.
- **Eventos com Amazon SNS:** O Amazon Simple Notification Service (SNS) pode ser utilizado para publicar eventos que outros serviços ou sistemas podem assinar e processar de forma assíncrona. Por exemplo, um sistema de monitoramento de saúde pode publicar eventos quando detecta um problema, e vários serviços podem reagir a esses eventos, como enviar notificações, iniciar procedimentos de mitigação ou registrar logs.
- **Arquiteturas orientadas a eventos em geral:** Em uma arquitetura orientada a eventos, os serviços reagem a eventos emitidos por outros serviços. Por exemplo, uma aplicação de rede social pode emitir eventos quando um novo post é criado, e outros serviços, como notificações, analytics ou feed de atividades, podem processar esses eventos de forma independente, sem depender de uma resposta síncrona.

Ao priorizar a comunicação assíncrona, os sistemas se tornam mais resilientes e estáveis, capazes de lidar com falhas e variações na carga de trabalho sem comprometer a disponibilidade e a funcionalidade. Essa abordagem é especialmente útil em ambientes distribuídos, onde a latência de rede e a disponibilidade dos serviços podem ser variáveis.

## Concluindo

A estabilidade estática é um conceito fundamental na arquitetura de sistemas resilientes. Refere-se à capacidade de uma aplicação de manter seu estado e continuar operando mesmo quando algumas de suas dependências falham ou estão indisponíveis. Essa característica é crucial para garantir a continuidade do serviço, minimizar interrupções e proporcionar uma experiência consistente ao cliente, especialmente em ambientes distribuídos e de alta disponibilidade.

## Principais pontos para alcançar a estabilidade estática:

- **Segregação de serviços:** Dividir a aplicação em componentes independentes, como separar o control plane do data plane. Isso ajuda a limitar o impacto de falhas a uma parte específica do sistema, permitindo que outros componentes continuem operando normalmente.
- **Pré-provisionamento de recursos:** Alocar recursos suficientes para suportar o tráfego esperado, mesmo em caso de falhas. Por exemplo, usar instâncias redundantes e evitar depender de escalabilidade automática em momentos críticos, para garantir que a aplicação possa lidar com picos de demanda sem problemas.
- **Evitar dependências circulares:** Projetar o sistema para que os componentes não dependam uns dos outros em um ciclo. Isso previne situações em que uma falha em um componente pode causar uma cascata de falhas em outros componentes, comprometendo a estabilidade do sistema.
- **Manter o estado atual da aplicação:** Garantir que o estado da aplicação não se altere inesperadamente em resposta a falhas. Isso permite que o sistema seja restaurado rapidamente para um estado conhecido e funcional.
- **Priorizar a comunicação assíncrona:** Adotar arquiteturas orientadas a eventos para desacoplar componentes. Isso permite que os serviços enviem e recebam mensagens sem precisar esperar respostas imediatas, tornando o sistema mais tolerante a falhas e mais flexível em termos de escalabilidade.

## Quando usar a estabilidade estática

A estabilidade estática deve ser considerada essencial em sistemas críticos e de alta disponibilidade, onde mesmo pequenos períodos de inatividade podem ter consequências significativas, como em sistemas financeiros, de saúde, comércio eletrônico ou infraestruturas de comunicação. É particularmente útil em ambientes distribuídos, onde a latência de rede e a disponibilidade dos serviços podem ser variáveis, e em cenários onde a recuperação rápida de falhas é crucial para manter a continuidade do serviço.

Além disso, a estabilidade estática é importante em situações onde o impacto financeiro ou para a imagem do negócio de uma falha é alto, justificando o investimento em práticas de design mais robustas. Em sistemas onde a experiência do cliente deve ser mantida consistente, mesmo diante de falhas internas, a estabilidade estática pode ser a diferença entre uma interrupção imperceptível e uma experiência negativa significativa para o cliente.
