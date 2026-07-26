---
title: "Entendendo o que é Availability Zone Independence(AZI) para aumentar a resiliência das aplicações"
description: "Um guia abrangente sobre Availability Zone Independence, design, deploy, observabilidade e resposta a falhas em aplicações distribuídas."
pubDate: 2024-08-12
tags: ["AWS", "Resilience", "High Availability", "Microservices"]
series: "Cloud Resilience"
language: "pt-BR"
---

**TLDR;** A ideia desse post é explorar de forma bem abrangente(assim espero) um conceito que falamos pouco no contexto de desenvolvimento de sistemas distribuídos, mas que faz uma grande diferença em aplicações que precisam de elevado nível de resiliência. Vamos ver sobre o design, deploy, observability e a resposta a falhas de uma aplicação que tem a característica de ser Availability Zone Independence(AZI).

No último post [Como a estabilidade estática aumenta a resiliência da sua aplicação](/blog/2024-08-05-como-a-estabilidade-estatica-aumenta-a-resiliencia-da-sua-aplicacao/) falamos como uma aplicação pode continuar operando sem mudar o seu estado, mesmo quando as dependências falham total ou parcialmente. Muitos exemplos que dei foram relacionados a falha de uma zona de disponibilidade.

Usei no título desse post o termo Availability Zone Independence(AZI), mas você pode encontrar artigos relacionados ao mesmo assunto por Availability Zone Affinity. Vou usar o primeiro, porque acho que reflete melhor o resultado que espero quando aplico esse conceito no design de aplicações, tornar a aplicação mais resiliente quando falhas acontecem, ou ainda, falhar de forma independente quando falamos de zonas de disponibilidade.

Antes de entrarmos fundo no assunto, vamos definir alguns conceitos. O que vou descrever aqui nesse artigo é válido para qualquer cloud provider ou mesmo qualquer software que expõe o contexto de zona de disponibilidade para seus clientes, mas vou focar no uso da nuvem da AWS.

## O que é uma Região na AWS?

Pegando a [referência direta](https://aws.amazon.com/pt/about-aws/global-infrastructure/regions_az/?p=ngi&loc=2) do site da AWS. Uma região é um local físico em alguma parte do mundo onde é agrupado data centers. Cada grupo de datacenters lógicos é chamado de zona de disponibilidade(AZ). Cada região da AWS consiste no mínimo em três AZs isoladas e separadas fisicamente em uma área geográfica. Diferentemente de outros provedores de nuvem, que geralmente definem uma região como um único datacenter, o design de múltiplas AZs de cada região da AWS oferece vantagens para os clientes. Cada AZ tem energia, refrigeração e segurança física independentes e está conectada por meio de redes redundantes de latência ultrabaixa. Os clientes da AWS, focados em alta disponibilidade, podem projetar seus aplicativos para serem executados em várias AZs, a fim de obter tolerância a falhas ainda maior. As regiões de infraestrutura da AWS atendem aos mais altos níveis de segurança, conformidade e proteção de dados.

![Imagem 1 do artigo](/blog/availability-zone-independence-azi/image-01.png)

## O que é uma Zona de Disponibilidade (AZ)

Pegando a [referência direta](https://aws.amazon.com/pt/about-aws/global-infrastructure/regions_az/?p=ngi&loc=2) do site da AWS. Uma zona de disponibilidade (AZ) é um ou mais datacenters distintos com energia, rede e conectividade redundantes em uma região da AWS. As AZs proporcionam aos clientes a capacidade de operar aplicativos e bancos de dados de produção com alta disponibilidade, tolerância a falhas e escalabilidade em níveis superiores aos que um único datacenter pode oferecer. Todas as AZs em uma região da AWS estão interconectadas por redes de alta largura de banda e baixa latência, usando fibra metropolitana dedicada e totalmente redundante para proporcionar redes de alto throughput e baixa latência entre AZs. Todo o tráfego entre as AZs é criptografado. O desempenho da rede é suficiente para realizar a replicação síncrona entre as AZs. As AZs particionam aplicativos para facilitar a alta disponibilidade. Se um aplicativo for particionado em várias AZs, as empresas estarão melhor isoladas e protegidas contra problemas como quedas de energia, raios, tornados e terremotos, entre outros. As AZs são fisicamente separadas por uma distância significativa (vários quilômetros) das outras AZs, embora todas estejam em um raio de até 100 km entre si.

![Imagem 2 do artigo](/blog/availability-zone-independence-azi/image-02.png)

## O que é Availability Zone Independence(AZI)?

A AWS possui serviços regionais e zonais(também possui [globais](https://docs.aws.amazon.com/whitepapers/latest/aws-fault-isolation-boundaries/global-services.html), mas não é o foco desse post). **Serviços regionais** são serviços que a AWS construiu sobre várias Zonas de Disponibilidade para que os clientes não precisem descobrir como fazer o melhor uso dos serviços zonais. **Um serviço zonal** é aquele que fornece a capacidade de especificar em qual Zona de Disponibilidade os recursos são implantados. Esses serviços operam independentemente em cada Zona de Disponibilidade dentro de uma Região e, mais importante, falham independentemente em cada Zona de Disponibilidade também.

Availability Zone Independence(AZI) significa traduzindo de forma literal **independência de zona de disponibilidade**, mas no restante desse post vou usar apenas AZI. Significa fazer o design de aplicações de forma que eles possam continuar operando eficazmente mesmo se uma ou mais AZs estiverem indisponíveis, e principalmente que os erros que aconteçam em uma AZ fiquem contidos na AZ da falha, ou seja, não impactem as demais AZ. Esse conceito se aplica somente para serviços zonais.

O primeiro passo **para implementar AZI** em suas aplicações **é reduzir o tráfego cross-az**(entre zonas de disponibilidade) o máximo possível. Isso tem um impacto na resiliência e no custo das aplicações, uma vez que o tráfego cross-az é cobrado, reduzi-lo significa gastar menos. Para entender melhor isso vamos ver uma arquitetura convencional, mas de uma forma que geralmente não pensamos.

![Imagem 3 do artigo](/blog/availability-zone-independence-azi/image-03.png)

A imagem acima mostra uma aplicação tradicional, multi-az, que recebe tráfego em um load balancer e redireciona o tráfego para um cluster ECS que então acessa uma base de dados RDS. mas esse diagrama não reflete bem como as coisas são na realidade, ou pelo menos no comportamento padrão de um AWS Application Load Balancer(ALB), ou mesmo da sua aplicação sem a devida ciência disso que estamos tratando nesse post.

![Imagem 4 do artigo](/blog/availability-zone-independence-azi/image-04.png)

A imagem acima agora já mostra um cenário mais realista do que realmente é executado. Embora o AWS ALB seja um serviço regional, a região é apenas um agrupamento lógico de AZs. Todo e qualquer serviço sempre roda em alguma AZ, com um endereço IP de uma VPC em uma AZ. Para ficar mais claro, eu criei um AWS ALB de teste habilitado em 3 AZs e então fiz uma pesquisa pelo DNS:

![Imagem 5 do artigo](/blog/availability-zone-independence-azi/image-05.png)

Depois desabilitei uma AZ, deixando apenas 2 AZs habilitadas e refiz a mesma pesquisa:

![Imagem 6 do artigo](/blog/availability-zone-independence-azi/image-06.png)

Perceba que agora retornou apenas 2 endereços IPs, ao invés de 3, isso porque quando você cria um ALB, o control plane coloca um ALB node em cada AZ habilitada. O Route53 distribui o tráfego de maneira balanceada e os ALB nodes fazem o mesmo por todas as instâncias registradas como target, como na imagem abaixo.

![Imagem 7 do artigo](/blog/availability-zone-independence-azi/image-07.png)

Você pode ler mais a respeito aqui [nesse link](https://docs.aws.amazon.com/pt_br/elasticloadbalancing/latest/userguide/how-elastic-load-balancing-works.html). Mas assim sendo o ALB recebe uma requisição em uma zona específica e como sua função é distribuir as requisições e balancear o tráfego de forma igualmente balanceada(num cenário ideal) entre as AZs, e vamos pensar em balanceada aqui onde cada AZ receberia sempre ⅓ do tráfego, assim como cada instância que o ALB tem como target também recebe proporcionalmente. O cenário da imagem acima acontece por padrão e pode ser que sua requisição entre pela AZ1, o ALB devido ao seu algoritmo envia tráfego para a AZ3 e sua aplicação acesse o banco de dados pela instância que está na AZ2. Comportamento super comum, que na minha experiência raramente é raramente discutido e questionado.

Levando em consideração o design da Cloud(AWS no caso desse post) e focando em aumentar a resiliência uma abordagem é garantir que falhas que aconteçam em uma AZ não impactem os componentes de sua aplicação que estão rodando em outra AZ ou que sua aplicação não seja afetada como um todo só porque uma AZ ficou indisponível. Afinal de contas é por isso que fazemos o design das aplicações para utilizar múltiplas AZs.

![Imagem 8 do artigo](/blog/availability-zone-independence-azi/image-08.png)

A imagem acima mostra o nosso cenário desejado e por isso reduzir o tráfego cross-az ajuda. Mas não é o suficiente, é necessário pensar no design da aplicação para ser AZI, no deploy, na observabilidade e como respondemos a falhas que acontecem em uma específica AZ para que sua aplicação realmente continue operando, que significa evacuar o tráfego da AZ indisponível. É isso que vamos ver a partir de agora.

## Fazendo o design da aplicação para ser AZI

O conceito de AZI se aplica apenas para serviços que têm o escopo de zona para ser configurado. Fazer o design de sua aplicação para ser AZI é fazer o design de uma forma que o impacto em uma zona de disponibilidade afete apenas os componentes dessa zona e não as demais como já dito. A abordagem para esse resultado vai depender de cada serviço do qual sua aplicação é composta.

Nesse post vou pegar como exemplo a arquitetura que estamos discutindo composta por ALB, container ECS e um banco de dados RDS. No melhor cenário vamos ter esse comportamento da imagem abaixo:

![Imagem 9 do artigo](/blog/availability-zone-independence-azi/image-09.png)

Agora se uma AZ falhar ou componentes da sua aplicação falharem em apenas uma AZ, você pode parar de enviar tráfego para essa AZ e continuar operando normalmente com as demais. Vamos ver ainda como pode ser feito esse “parar de enviar tráfego para uma AZ com indisponibilidade”.

![Imagem 10 do artigo](/blog/availability-zone-independence-azi/image-10.png)

Na imagem abaixo temos o tráfego da AZ que está em falha já evacuado, e somente as demais AZs que estão funcionando normalmente passam a receber tráfego. Claro que nessa arquitetura em específico, ainda temos algumas perdas. Como estamos usando RDS, depende um pouco se você está usando [multi-az db instance ou multi-az db cluster](https://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/Concepts.MultiAZ.html), uma das vantagens é termos instâncias de escrita, escrita(com read réplicas) e standby instances que podem ser configuradas para se tornarem instâncias primárias. Esse processo pode levar alguns segundos e nesse caso, AZI não vai resolver tudo, mas vai facilitar bastante.Quando uma AZ que está com a instância de escrita estiver com problema, evacuar o tráfego dessa AZ é um dos passos, o outro passo é fazer o failover da instância do RDS.

![Imagem 11 do artigo](/blog/availability-zone-independence-azi/image-11.png)

O primeiro passo na nossa arquitetura para reduzir o tráfego cross-az é justamente garantir que o ponto de entrada do nosso tráfego não faça tráfego cross-az, neste caso o ALB. Você pode fazer isso [desabilitando o trafego cross-az para os target groups](https://docs.aws.amazon.com/elasticloadbalancing/latest/application/disable-cross-zone.html). O mesmo também pode ser feito com [Network Load Balancer](https://docs.aws.amazon.com/elasticloadbalancing/latest/network/target-group-cross-zone.html) ou [Classic Load Balancer](https://docs.aws.amazon.com/elasticloadbalancing/latest/classic/enable-disable-crosszone-lb.html#disable-cross-zone). Com isso, sempre que uma request chegar em um node do ALB, ele vai redirecionar para as instâncias que estiverem na mesma zona dele. Nessa simples arquitetura, que é muito praticada, esse ajuste de configuração já causa o efeito da imagem abaixo.

![Imagem 12 do artigo](/blog/availability-zone-independence-azi/image-12.png)

Mas desabilitar o tráfego cross-az do ALB tem seus prós e contras. Os prós são:

- Facilita a identificação de [gray failures](https://docs.aws.amazon.com/whitepapers/latest/advanced-multi-az-resilience-patterns/gray-failures.html#:~:text=Gray%20failures%20are%20defined%20by,entities%20observe%20the%20failure%20differently.), falhas intermitentes ou que podem ser identificadas somente de algumas perspectivas e outras não. Como um componente em falha quando um cliente tenta usar a aplicação, mas que o sistema de monitoramento da aplicação não consegue identificar porque ocorre num percentual ou situação muito específica.
- Facilita a evacuação de tráfego no caso de falhas em uma AZ específica ou gray failures, citada acima.
- Reduz o custo da aplicação, uma vez que na AWS o tráfego cross-az é cobrado.

O principal ponto contra é que com o tráfego cross-az desabilitado você não tem o tráfego igualmente balanceado por todas as instâncias da sua aplicação(saiba mais [aqui](https://docs.aws.amazon.com/elasticloadbalancing/latest/userguide/how-elastic-load-balancing-works.html)). Se você estiver utilizando duas AZs, o tráfego vai continuar chegando no ALB de maneira balanceada, mas a distribuição para seus target e instâncias, não necessariamente, como na imagem abaixo:

![Imagem 13 do artigo](/blog/availability-zone-independence-azi/image-13.png)

Nesse caso há uma atenção redobrada para que todas as suas AZ tenham o mesmo número de instâncias, algo que o [AWS Auto Scaling](https://aws.amazon.com/pt/autoscaling/) ajuda muito.

Um dos desafios de se implementar AZI é como sua instância sabe em que zona está operando ? E como ela sabe como invocar um endpoint que está na mesma zona que ela está operando ? Novamente, depende do serviço, mas duas abordagens que funciona amplamente são:

- Usar Availability Zone ID para sua aplicação identificar em que zona está operando.
- Criar endpoints DNS específicos por AZ

### Usar Availability Zone ID(AZ ID) para sua aplicação identificar em que zona está operando

O AZ ID é um identificador exclusivo e consistente de uma Zona de Disponibilidade em todas as contas da Contas da AWS, e não é necessariamente o identificador us-east-1 que você vê no console. De acordo com a [documentação](https://docs.aws.amazon.com/pt_br/ram/latest/userguide/working-with-az-ids.html), a AWS mapeia as zonas de disponibilidade físicas aleatoriamente com os nomes das zonas de disponibilidade de cada conta da AWS. Essa abordagem ajuda a distribuir recursos pelas Zonas de Disponibilidade em uma região, em vez de os recursos provavelmente estarem concentrados na zona de disponibilidade “a” de cada região. Como resultado, a Zona de Disponibilidade us-east-1a da sua conta A pode não representar a mesma localização física us-east-1a de uma AWS conta diferente. A imagem abaixo ilustra isso um pouco:

![Imagem 14 do artigo](/blog/availability-zone-independence-azi/image-14.png)

Por causa desse comportamento a melhor forma de uma instância da sua aplicação saber em que zona está operando é usar o AZ ID através do EC2 Instance Metadata Service (IMDS). Que é uma interface que permite que instâncias do Amazon EC2 recuperem metadados sobre si mesmas. Essa informação inclui detalhes como o ID da instância, o tipo de instância, a AMI utilizada, e a região ou zona de disponibilidade onde a instância está operando. IMDS é acessível somente a partir da própria instância, usando um endpoint especial que está disponível na rede local da instância.

Para acessar o IMDS, você pode fazer uma requisição HTTP para o endpoint [http://169.254.169.254/.](http://169.254.169.254/) Para obter o ID da Zona de Disponibilidade (AZ) em que a instância está operando, você pode fazer uma requisição específica para o seguinte endpoint:

```bash
curl http://169.254.169.254/latest/meta-data/placement/availability-zone
```

Esse comando retorna o ID da AZ, que será algo como us-east-1a, us-west-2b, etc. Essa abordagem funciona para EC2, ECS, EKS, Elastic Beanstalk, EMR e outros que posso ter esquecido, só quis enfatizar o quanto a abordagem é ampla.

### Criar endpoints DNS específicos por AZ

Nem sempre o serviço que você está invocando vai estar atrás de um load balancer. Um exemplo disso é o próprio Amazon RDS onde você tem primary instance, standby instance e read replicas.

Com o Amazon RDS, você pode criar endpoints DNS específicos por AZ para acessar réplicas de leitura em diferentes zonas de disponibilidade. Isso permite que sua aplicação leia dados de réplicas que estão localizadas na mesma AZ, o que melhora a performance e oferece resiliência.

Suponha que você tenha um banco de dados RDS com réplicas em us-east-1a, us-east-1b, e us-east-1c. Você pode configurar endpoints DNS como:

- db-read-us-east-1a.example.com
- db-read-us-east-1b.example.com
- db-read-us-east-1c.example.com

![Imagem 15 do artigo](/blog/availability-zone-independence-azi/image-15.png)

Os mesmos princípios se aplicam para o caso de serviços que sua aplicação possua rodando em múltiplas zonas e você quer priorizar invocar o serviço dentro da mesma zona. Esse processo de discovery pode ser feito via AWS CloudMap, DynamoDB ou qualquer serviço de service discovery como Consul por exemplo. Novamente isso vai variar de serviço para serviço.

![Imagem 16 do artigo](/blog/availability-zone-independence-azi/image-16.png)

## Fazendo o deploy da aplicação para ser AZI

A forma que é feito o deploy da sua aplicação influencia diretamente na implementação de AZI, pois o que queremos é que as falhas de uma AZ fiquem contidas nesta AZ. O que acontece se durante o deploy de uma V2 de um serviço A, você faz o deploy em 2 AZs ao mesmo e essa versão possui um bug ? Um grande problema de escopo de impacto na sua aplicação e experiência do cliente.

Tem bastante conteúdo na internet falando sobre boas práticas deploy e geralmente segue a lógica abaixo, onde se tem múltiplos ambientes e a versão vai progredindo através de cada ambiente até chegar em produção, onde idealmente você não coloca 100% do seu trafego de uma única vez, mas faz gradualmente, preferencialmente em ondas e com certo intervalo de tempo entre elas, para observar se algum erro ou alarme vai disparar. Caso aconteça, você faz o rollback da versão(preferencialmente automaticamente).

![Imagem 17 do artigo](/blog/availability-zone-independence-azi/image-17.png)

Se os nomes acima parecem um pouco estranhos para você sugiro que leia esse excelente artigo da Amazon Builders library: [Automating safe, hands-off deployments](https://aws.amazon.com/builders-library/automating-safe-hands-off-deployments/?nc1=h_ls).

Se descermos um pouco mais no contexto dentro de cada evento de deployment é comum também vermos abordagens como [blue/green deployment](https://docs.aws.amazon.com/whitepapers/latest/overview-deployment-options/bluegreen-deployments.html), onde você tem 2 ambientes/cópias iguais(blue e green) e há sempre um ativo(pode ser o blue inicialmente) recebendo tráfego de seus clientes. Quando você precisa iniciar uma nova versão você faz no ambiente que não está sendo utilizado(nesse caso o green). Outra abordagem bem conhecida é o [canary deployment](https://docs.aws.amazon.com/whitepapers/latest/overview-deployment-options/canary-deployments.html), onde você tem também 2 ambientes, mas a cada nova versão disponibilizada é feita em um ambiente paralelo, mas gradualmente você vai fazendo a mudança de tráfego de seus clientes. Você pode selecionar mover 10% do seu tráfego para a versão nova, depois 50% e então 100%.

![Imagem 18 do artigo](/blog/availability-zone-independence-azi/image-18.png)

Como estamos falando de AZI nesse post, precisamos representar nesses diagramas as AZs e é isso que vejo pouco quando estamos definindo estratégias de deploy, pois o comportamento da imagem abaixo pode acontecer, onde tenho várias AZs sendo impactadas ao mesmo independente da abordagem selecionada.

![Imagem 19 do artigo](/blog/availability-zone-independence-azi/image-19.png)

O ponto chave aqui é que independente da forma que você faz progresso de uma versão entre ambientes ou do quão gradual é essa mudança de tráfego, também deve ser inserido nessa equação fazer uma AZ de cada vez. É essa dinâmica que vai lhe proporcionar evacuar o tráfego de uma AZ no caso de um deployment que possui alguma falha. Mesmo que seu pipeline implemente rollback automático em caso de falha, ter a possibilidade de evacuar o tráfego vai ser uma estratégia mais rápida e com isso gera menor impacto.

![Imagem 20 do artigo](/blog/availability-zone-independence-azi/image-20.png)

Fazer isso não é tão trivial e vai variar de acordo com os serviços de compões a sua aplicação, seja EC2, ECS ou Kubernetes por exemplo.

## Fazendo a Observabilidade da aplicação para ser AZI

Não adianta fazer o design da aplicação e o deploy AZI se quando uma falha acontece em uma AZ não somos capazes de identificar. Observabilidade é um fator crítico, basicamente quando estamos implementando AZI tudo precisa ser “AZ aware” como geralmente chamamos, ou seja, todos os sinais e dados que a sua aplicação produz como logs, métricas e tracing precisa conter o AZ ID. Já foi mencionado nesse post com você pode ter o AZ ID dentro da sua aplicação

Depois de ter sua aplicação instrumentada para gerar sinais por AZ podemos montar alarmes por AZ e é aí que a mágica da observabilidade para AZI acontece. Cada métrica da sua aplicação precisa ser por AZ e com um alarme correspondente. O Amazon Cloudwatch tem uma feature muito legal que se chama [Composite Alarm](https://docs.aws.amazon.com/AmazonCloudWatch/latest/monitoring/Create_Composite_Alarm.html)(alarmes compostos) onde você pode combinar alarmes com expressões AND e OR.

Com essa funcionalidade do CloudWatch você poderia ter por exemplo um alarme de latência por AZ e um alarme de disponibilidade e combiná-los para ter uma visão de AZ completa por serviço e pela dimensão que desejar.

![Imagem 21 do artigo](/blog/availability-zone-independence-azi/image-21.png)

O mesmo processo da imagem acima pode ser feito para latência e assim ter uma visão de todas as métricas dos seus serviços por AZ, assim em caso de falha de uma AZ por parte da AWS ou mesmo por um deployment com erro, agora você é capaz de observar os erros que podem acontecer na sua aplicação dentro do contexto de cada AZ e escolher que ação tomar, pode ser um rollback, evacuar o tráfego de uma AZ ou ambos.

![Imagem 22 do artigo](/blog/availability-zone-independence-azi/image-22.png)

Dei exemplos aqui com CloudWatch, mas o principal é ter tudo que fiz a respeito da observabilidade por AZ, independente da stack que você estiver utilizando. Escrevi pouco sobre observabilidade aqui porque existe esse excelente whitepaper da AWS falando sobre [Advanced Multi-AZ resilience Patterns](https://docs.aws.amazon.com/whitepapers/latest/advanced-multi-az-resilience-patterns/multi-az-observability.html) que fala muito sobre e com exemplos muito bons.

## Respondendo para falhas de Zonas de disponibilidade

Chegamos a parte final do nosso post, você já sabe o que é AZI, sabe algumas coisas que é importante considerar quando for fazer o design, o deploy e a observabilidade da sua aplicação. Agora falhas que acontecem em uma AZ, ficam contidas somente nessa sem impactar as demais AZs. Mas o fato é que falhar ainda é uma experiência ruim, mesmo que minimizada, como podemos responder para essa falha o mais rápido possível para não degradar a experiência dos clientes da aplicação?

![Imagem 23 do artigo](/blog/availability-zone-independence-azi/image-23.png)

A resposta curta é **evacuar o tráfego da AZ que está em falha**, seja por causa de um serviço AWS ou por um serviço da sua aplicação. Essa evacuação de tráfego pode ser feita via [control plane ou via data plane](https://docs.aws.amazon.com/whitepapers/latest/aws-fault-isolation-boundaries/control-planes-and-data-planes.html). O mais recomendado é [fazer via data plane](https://docs.aws.amazon.com/wellarchitected/latest/framework/rel_withstand_component_failures_avoid_control_plane.html), uma vez que o data plane tende a falhar menos que o control plane, e pode acontecer da sua aplicação começar a falhar em uma AZ e o control plane da AWS também estar indisponível. Nesse caso você vai estar incapacitado de se recuperar dessa falha.

A evacuação via control plane seria a seguinte:

1. Uma falha de uma AZ é identificada através de um alarme
2. Na configuração do ALB a subnet da AZ que está com problema é removida
3. Na configuração do ECS a subnet da AZ que está com problema é removida
4. Fazer o failover do RDS para a instância que está em standby.

Todas as ações acima utilizam ações de control plane e podem ser automatizadas para responder a um alarme, tendo um fluxo mais ou menos assim:

![Imagem 24 do artigo](/blog/availability-zone-independence-azi/image-24.png)

Uma vez que os alarmes estejam em estado normal novamente o mesmo processo pode ser disparado, para reverter a evacuação de AZ:

![Imagem 25 do artigo](/blog/availability-zone-independence-azi/image-25.png)

Esse processo pode ser disparado automaticamente ou manualmente por um operador. Dessa forma, para a nossa arquitetura de exemplo é claro, o tráfego de um AZ pode ser evacuado, usando as APIs de control plane da AWS.

Uma abordagem mais robusta seria utilizar somente APIs e recursos do data plane para fazer a evacuação de zona e a restauração da mesma. Essa abordagem se utiliza bastante da funcionalidade de [Health Checks do Route53](https://docs.aws.amazon.com/Route53/latest/DeveloperGuide/dns-failover.html). Pegando referência da documentação, Health checks do Amazon Route 53 monitoram a integridade e o desempenho das aplicações. Cada Health check que você cria pode monitorar um dos seguintes:

- A integridade de um recurso especificado, como um servidor da Web ou uma API.
- O status de outros Health Checks.
- O status de um alarme do Amazon CloudWatch.

Basicamente os Health Checks são requisições feitas periodicamente pelo Route 53 para avaliar a saúde do seu serviço, se ele falhar dentro de um intervalo, um número X de vezes, o serviço é classificado como unhealthy(não saudável) e nesse caso o Route 53 para de resolver DNS para esse destino. O lado desse comportamento é que ele utiliza 100% de APIs do data plane e o próprio data plane do Route 53 tem um SLA de 100%. Entendido os Health Checks, temos algumas abordagens possíveis que vamos ver.

![Imagem 26 do artigo](/blog/availability-zone-independence-azi/image-26.png)

Na imagem acima temos o seguinte cenário do nosso post, porém configuramos 3 health checks, um para cada endpoint de AZ(prática já recomendada mais acima). A diferença aqui é que vamos apontar o destino desses health checks para um bucket S3 e para um nome de um arquivo que corresponda a nossa AZ. Assim se uma requisição dessas falha, nosso health check pode falhar e evacuar a AZ em questão. O passo 1 poderia ser disparado por um alarme ou rotina similar a que explicamos na abordagem de control plane. O que vale a pena prestar atenção aqui é o método de inserir um arquivo no bucket e pela presença do arquivo o health check falhar.

Isso acontece porque o Route 53 tem uma funcionalidade chamada **inverted health check**, onde você pode dizer que se a requisição de health check retornar com sucesso ele deve falhar, logo o resultado inverso no normal.

Outra forma de se ter o mesmo resultado é salvando essa informação das AZs e seu estado em uma base de dados e via API oferecer isso como destino para o Route 53 Health Checks, como no desenho simplificado abaixo:

![Imagem 27 do artigo](/blog/availability-zone-independence-azi/image-27.png)

Agora, se você alterar o registro que tem no DynamoDB, você pode deixar a AZ health ou unhealthy e evacuar ou recuperar a AZ em questão. Você ainda pode desenvolver um serviço que avalie N verificações que você achar necessário para considerar uma AZ indisponível e então evacuar o tráfego.

Por último, quero falar de uma abordagem mais gerenciada para fazer a evacuação de tráfego de uma AZ, que é utilizando o serviço do [Route 53 Application Recovery Controller](https://docs.aws.amazon.com/r53recovery/latest/dg/what-is-route53-recovery.html) chamada [zonal shift](https://docs.aws.amazon.com/r53recovery/latest/dg/arc-zonal-shift.html). Da documentação da AWS, O Amazon Route 53 Application Recovery Controller (ARC53) ajuda você a se preparar e concluir uma recuperação mais rápida para aplicativos em execução AWS. O Route 53 ARC fornece dois conjuntos de recursos: recuperação de zona de disponibilidade múltipla (AZ), que inclui mudança zonal e mudança automática zonal, e recuperação multirregional, que inclui controle de roteamento e verificação de prontidão.

![Imagem 28 do artigo](/blog/availability-zone-independence-azi/image-28.png)

Aqui tem 2 possibilidades, o Zonal Shift e o Zonal autoshift. O zonal shift atua no nível de load balancer da sua aplicação, pode ser ALB ou NLB. O que ele faz é tornar unhealthy o health check do ALB, assim sendo o Route 53 para de enviar tráfego para ele. O zonal shift é um recurso manual, feito através de APIs de data plane do ARC53, mas pode ser automatizado com a mesma abordagem que montamos acima. E temos também o Zonal autoshift, onde você pode configurar um alarme que vai então disparar o zonal shift, ou seja, a evacuação de trafego da AZ em questão.

## Conclusão

Availability Zone Independence (AZI) é uma prática fundamental para aumentar a resiliência das aplicações que operam na cloud. Ao adotar o conceito de AZI, as aplicações são projetadas para isolar falhas em uma Zona de Disponibilidade (AZ) específica, evitando que problemas em uma AZ afetem a operação em outras zonas. Isso resulta em uma arquitetura mais robusta e preparada para lidar com falhas sem comprometer a experiência do cliente.

Pontos-chaves para implementar AZI:

- Entenda o escopo dos serviços que você utiliza, se é zonal ou regional
- Reduza o tráfego cross-az o máximo possível
- Crie endpoints específicos por AZ
- Instrumente a sua aplicação para produzir logs, métricas e tracing por AZ
- Faça o design do seu pipeline de deploy para atuar em uma AZ de cada vez
- Faça dashboards e alarmes de cada métrica e por AZ
- Crie mecanismos para evacuar o tráfego de uma AZ utilizando APIs de control e data plane(preferencialmente) do serviços

O objetivo desse post foi focar nos fundamentos de AZI e abordagens que podem ajudar em todas as etapas, com uma visão mais alto nível entre problema e solução. Pretendo escrever um post mais hands-on, focado principalmente em como fazer AZI com Kubernetes(EKS), que é uma stack que vejo que gera bastante confusão na hora de implementar AZI, por ser muito flexível.

## Referências

- [https://aws.amazon.com/pt/blogs/containers/choosing-container-logging-options-to-avoid-backpressure/](https://aws.amazon.com/pt/blogs/containers/choosing-container-logging-options-to-avoid-backpressure/)
- [https://docs.aws.amazon.com/whitepapers/latest/advanced-multi-az-resilience-patterns/availability-zone-independence.html](https://docs.aws.amazon.com/whitepapers/latest/advanced-multi-az-resilience-patterns/availability-zone-independence.html)
- [https://docs.aws.amazon.com/whitepapers/latest/advanced-multi-az-resilience-patterns/appendix-a-getting-the-availability-zone-id.html](https://docs.aws.amazon.com/whitepapers/latest/advanced-multi-az-resilience-patterns/appendix-a-getting-the-availability-zone-id.html)
- [https://docs.aws.amazon.com/vpc/latest/privatelink/create-interface-endpoint.html#access-service-though-endpoint](https://docs.aws.amazon.com/vpc/latest/privatelink/create-interface-endpoint.html#access-service-though-endpoint)
- [https://docs.aws.amazon.com/whitepapers/latest/introduction-devops-aws/deployment-strategies.html](https://docs.aws.amazon.com/whitepapers/latest/introduction-devops-aws/deployment-strategies.html)
- [https://docs.aws.amazon.com/AmazonCloudWatch/latest/monitoring/Create_Composite_Alarm.html](https://docs.aws.amazon.com/AmazonCloudWatch/latest/monitoring/Create_Composite_Alarm.html)
- [https://docs.aws.amazon.com/whitepapers/latest/aws-fault-isolation-boundaries/control-planes-and-data-planes.html](https://docs.aws.amazon.com/whitepapers/latest/aws-fault-isolation-boundaries/control-planes-and-data-planes.html)
- [https://docs.aws.amazon.com/Route53/latest/DeveloperGuide/dns-failover.html](https://docs.aws.amazon.com/Route53/latest/DeveloperGuide/dns-failover.html)
- [https://docs.aws.amazon.com/r53recovery/latest/dg/what-is-route53-recovery.html](https://docs.aws.amazon.com/r53recovery/latest/dg/what-is-route53-recovery.html)
- [https://docs.aws.amazon.com/r53recovery/latest/dg/arc-zonal-shift.html](https://docs.aws.amazon.com/r53recovery/latest/dg/arc-zonal-shift.html)
