---
id: 298
title: Como instalar e configurar Amazon EC2 e opinião após alguns meses
date: 2013-11-11T10:57:27+00:00
author: Robisson Oliveira
layout: post
guid: http://www.robissonoliveira.com.br/?p=298
permalink: /infraestrutura-de-ti/como-instalar-e-configurar-amazon-ec2-e-opiniao-apos-alguns-meses
bluth_post_layout:
  - single_column
bluth_facebook_status:
  - 
bluth_twitter_status:
  - 
WSS_DATA_GOOGLE:
  - 21
WSS_DATA_FACEBOOK:
  - 12
WSS_DATA_STUMBLEUPON:
  - 0
WSS_DATA_TWITTER:
  - 0
WSS_DATA_LINKEDIN:
  - 17
WSS_DATA_PINTEREST:
  - 0
WSS_DATA_TOTAL:
  - 50
WSS_DATA:
  - 's:142:"a:7:{s:6:"google";i:21;s:8:"facebook";i:12;s:11:"stumbleupon";i:0;s:7:"twitter";i:0;s:8:"linkedin";i:17;s:9:"pinterest";i:0;s:5:"total";i:50;}";'
dsq_thread_id:
  - 4911109683
categories:
  - Infraestrutura de TI
format: image
---
Neste post vou explicar como se cadastrar uma conta no Amazon ec2, instalar e configurar um ambiente com PHP e mysql . Antes de mais nada o <a title="Amazon EC2" href="http://aws.amazon.com/pt/ec2/" target="_blank">Amazon EC2</a>  é resumindo um serviço de hospedagem de servidores na nuvem, na minha opinião o melhor do mundo. Ele ainda possui para versões de servidores básicos que possui um ano de hospedagem gratuita e é esse serviço que vou mostrar como configurar. Para você que procura um ambiente de alto nível, saiba que ele pode ser obtido gratuitamente por 1 ano e mesmo após esse período o preço não é absurdo . Ainda vou descrever como é minha convivência após alguns meses de Amazon na empresa aonde trabalho.<!--more-->

&nbsp;

## O cenário crítico

Eu trabalho na <a title="Guarida Imóveis" href="http://www.guarida.com.br" target="_blank">Guarida Imóveis</a> , umas da maiores imobiliárias do Sul do Pàis . A Guarida tem milhares de acessos todos os dias em seus sites e sistemas web. Até janeiro deste ano toda a nossa infraestrutura web estava nos clouds da Locaweb, isso inclui o site da empresa, e alguns sistemas internos. O problema é que chegou um momento em que nossos servidores começaram sofrer grandes sobrecargas de processamento e memória sem que nossa aplicação fosse alterada. Sofremos por um período razoável e o suporte da Locaweb embora dedicado não achava a solução e passava a responsabilidade para a nossa infra de TI.

Optamos por migrar para outro servidor, eu já havia lido sobre os serviços da Amazon, sobre suas infraestruturas altamente escaláveis, disponibilidade e simplicidade de operação ( neste momento o preço não era objeto de discussão, até porque gastávamos algum milhares de reais na Locaweb também) . Como a  infraestrutura da Guarida era composta diversos servidores e a Amazon lhe da diversas opções de ambientes, fiquei com receio de fazer toda a migração com minha equipe apenas, então busquei empresas partner da Amazon e cheguei até a <a title="Puntox" href="http://www.puntox.com.br/" target="_blank">Puntox</a> . Desde já recomendo para quem tem uma infraestrutura web de grande porte que use uma empresa parceira da Amazon caso queira usá-la, pois há inúmeras maneiras de se configurar um ambiente na Amazon e estão sempre inovando, por isso acho mais adequado.

&nbsp;

## A migração para o Amazon EC2

O pessoal da Puntox foi realmente exemplar em seu trabalho desde a instalação e configuração do ambiente até a sua migração, seu suporte também é ágil e extremamente proativo ( quando falamos em disponibilidade de recursos de TI, proatividade em momentos críticos é algo de muito valor). Enfim migramos, os problemas acabaram e agora vou dizer o que realmente nos faz continuar na Amazon.

A primeira coisa que quero ressaltar e que realmente me admirou é que em nosso antigo fornecedor tínhamos praticamente o dobro de servidores alocados do que temos hoje, e nossos acessos diários hoje são maiores (consideravelmente maiores). Ou seja, o pessoal da Puntox com um número menor de servidores conseguiu atender nossa demanda sem sofrermos com paradas críticas ou qualquer problema para nossos usuários, clientes e colaboradores, enquanto que o antigo fornecedor nos oferecia apenas como solução aumentar o número de servidores.

&nbsp;

## Minha experiência usando Amazon EC2

  * Diminuímos o número de servidores em relação ao antigo fornecedores e conseguimos atender a uma demanda 20% maior . Economizamos custos, e reduzimos a complexidade de nossa infraestrutura. Méritos aqui a equipe de suporte que soube configurar um ambiente mais otimizado para nossa necessidade.

  * Minha equipe interna não teve mais que se preocupar com disponibilidade dos serviços. A Amazon possui um sistema de monitoramento extremamente flexível, é possível configurar alarmes para que você possa ser avisado sobre qualquer falha ou alteração de comportamento que sua infraestrutura  venha a sofrer ( e digo mais, realmente funciona).  Só para dar um exemplo, hoje se meu ambiente hospedado ficar mais de 5 minutos acima de 85% de processamento minha equipe de suporte já avisada pelo sistema para que alguma ação seja tomada.

  * Todas as métricas que você pode imaginar de monitoramento de rede ou hardware a Amazon registra e coloca a sua disposição. Você pode acompanhar tudo por um outro serviço da Amazon chamado <a title="Cloud Watch" href="http://aws.amazon.com/pt/cloudwatch/" target="_blank">Cloud Watch</a>. Ainda tem um aplicativo mobile para você acompanhar todo o status de sua infraestrutura.

  * Hoje se nosso ambiente estiver sobrecarregado de alguma forma, automaticamente um servidor adicional é posto em produção, aumentando nossa capacidade, esse recurso é chamado de <a title="Auto Scaling" href="http://aws.amazon.com/pt/autoscaling/" target="_blank">Auto Scaling</a> .

  * Outro ponto que me deixa confortável é que temos acesso total aos servidores, por incrível que pareça, há diversos fornecedores com planos de hospedagem corporativa que limitam apenas a equipe da própria empresa alterar qualquer coisa nos servidores. Na minha experiência, esse sempre foi um aspecto negativo. Hoje apesar de deixar tudo para a empresa que nos atende minha equipe tem liberdade de fazer o que quiser com o ambiente.

  * Além do do Auto Scaling e Cloud Watch, colocar um novo servidor no ar é uma questão de minutos literalmente.

&nbsp;

## OK , Amazon EC2 é bom, mas e o preço ?!

Em geral hoje nós pagamos o mesmo valor que pagávamos na Locaweb(também temos mais serviços agregados que no antigo fornecedor), porém temos um serviço muito mais robusto, um suporte que funciona e que é proativo em situações de crise ( pois elas sempre acontecem na vida real) . A Amazon cobra de uma forma muito singular, ela cobra por hora que seus servidores ficam ligados, estejam eles a 100% de uso ou não, e cobra por tráfego de rede, detalhes sobre os preços podem ser encontrados <a title="Preços do Amazon EC2" href="http://aws.amazon.com/pt/ec2/pricing/" target="_blank">aqui</a> ( não esqueça de consultar os preços para o Brasil).

&nbsp;

##  Colocando o seu site na Amazon de graça por 1 ano

Isso mesmo se você tem um site,e  ele não precisa de uma grande infraestrutura, saiba que a Amazon disponibiliza um servidor básico (seja Windows ou Linux) para você usar durante 1 ano de graça. Claro que tem algumas cotas que se forem extrapoladas vão ser cobradas, detalhes sobre os o que você tem gratuitamente estão [aqui](http://aws.amazon.com/pt/free/). Realmente os limites gratuitos são suficientes para um site ou blog básico , eles incluem um servidor Linux ou Windows Server, você ainda pode colocar outro servidor apenas para banco de dados (chamado de serviço RDS), 15GB de tráfego mensal ( não é pouco) , até um servidor CDN com 5GB de entrega você pode configurar e tem a disposição 20GB de backup, entre diversas outras coisas.

Eu coloquei meu próprio site no Amazon EC2 e agora vou mostrar como vocês podem configurar um ambiente PHP e Mysql para hospedar seu site.

&nbsp;

## <span style="color: #339966;">Criando a conta na Amazon</span>

A primeira coisa que você precisa fazer é criar sua conta na Amazon AWS o link para cadastro é este <a title="Endereço para cadastro do Amazon EC2" href="http://aws.amazon.com/pt/free/" target="_blank">aqui</a> . Clicando em **Comece a usar gratuitamente **você tem alguns passos a seguir :

Preencha o campo email, marque a opção **I am a new user** e clique no botão para prosseguir.

[<img class="aligncenter wp-image-320 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-0-300x176.png" alt="ec2-passo-0" width="300" height="176" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-0-300x176.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-0.png 841w" sizes="(max-width: 300px) 100vw, 300px" />](https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-0.png){.lightbox}

&nbsp;

&nbsp;

&nbsp;

O próximo passo é preencher seu nome, confirmar seu email e escolher uma senha.

&nbsp;

<p style="text-align: center;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-1.png"><img class="aligncenter wp-image-325 size-medium" title="Clique para a ampliar" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-1-300x159.png" alt="ec2-passo-1" width="300" height="159" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-1-300x159.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-1.png 713w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

&nbsp;

A seguir seus dados de contato conforme mostra a imagem abaixo.

&nbsp;

<p style="text-align: center;">
  <a class="lightbox" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-2.png"><img class="alignnone wp-image-327 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-2-300x258.png" alt="ec2-passo-2" width="300" height="258" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-2-300x258.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-2.png 786w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

&nbsp;

Embora o serviço seja gratuito por 1 ano, a Amazon lhe pede informações de cartão de crédito, pois há a possibilidade de você extrapolar alguma cota, lembrando que na Amazon você só paga pelo que usa, se por acaso um dia do mês você extrapolar alguma conta, só vai pagar pelas horas que extrapolam.

&nbsp;

[<img class="aligncenter wp-image-392 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-31-300x252.png" alt="ec2-passo-3" width="300" height="252" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-31-300x252.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-31.png 734w" sizes="(max-width: 300px) 100vw, 300px" />](https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-31.png){.lightbox}

&nbsp;

&nbsp;

Na próxima tela o sistema pede uma confirmação por telefone, você coloca seu número de telefone e clica em confirmar, um número PIN aparece. Seu telefone toca e você tem que digitar esse PIN com o teclado . Uma coisa curiosa aqui é que comigo só deu certo quando coloquei o PIN através de um telefone fixo, acho que o sistema da Amazon não reconheceu os dígitos quando digitados de um teclado Android, vai entender.

&nbsp;

<p style="text-align: center;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-4.png"><img class="aligncenter wp-image-331 size-medium" title="Clique para a ampliar" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-4-300x231.png" alt="ec2-passo-4" width="300" height="231" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-4-300x231.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-4-194x150.png 194w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-4.png 714w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

&nbsp;

Após digitar o PIN é só aguardar e tela abaixo  e clicar em **Continuar.**

&nbsp;

<p style="text-align: center;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-5.png"><img class="aligncenter wp-image-333 size-medium" title="Clique para a ampliar" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-5-300x179.png" alt="ec2-passo-5" width="300" height="179" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-5-300x179.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-5.png 721w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

&nbsp;

O próximo passo é escolher que tipo de suporte você deseja, claro que eu escolhi o gratuito.

&nbsp;

<p style="text-align: center;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-6.png"><img class="aligncenter wp-image-335 size-medium" title="Clique para a ampliar" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-6-300x221.png" alt="ec2-passo-6" width="300" height="221" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-6-300x221.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-6.png 757w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

&nbsp;

Após isso seu cadastro foi realizado com sucesso , basta voltar <a title="Console AWS Amazon" href="https://console.aws.amazon.com/" target="_blank">aqui </a>e colocar seu email e senha.

&nbsp;

## <span style="color: #339966;">Criando o servidor para o seu site</span>

Uma vez logado no console da Amazon, você tem todos os serviços da mesma a disposição, no nosso caso o que nos importa é o Amazon EC2, opção para criar servidores na nuvem. Essa opção fica a esquerda da tela conforme imagem abaixo:

&nbsp;

<p style="text-align: center;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-8.png"><img class="aligncenter wp-image-341 size-medium" title="Clique para a ampliar" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-8-300x129.png" alt="ec2-passo-8" width="300" height="129" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-8-300x129.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-8-1024x441.png 1024w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-8-700x300.png 700w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-8.png 1115w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

&nbsp;

A Amazon possui algumas regiões com datacenters no mundo, quando você configura algum serviço, está condicionado a uma região, geralmente no primeiro acesso e pelo menos para mim a região estava em Oregon e tive que mudá-la para São Paulo. Em qualquer parte do sistema para mudar a região basta clicar sobre o nome da cidade localizada na parte superior direita da tela conforme imagem abaixo. É importante frisar que para sites com o público-alvo brasileiro é melhor escolher a região de configuração de São Paulo pois a latência é menor.

&nbsp;

<p style="text-align: center;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-9.png"><img class="aligncenter wp-image-344 size-medium" title="Clique para a ampliar" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-9-300x234.png" alt="ec2-passo-9" width="300" height="234" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-9-300x234.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-9-194x150.png 194w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-9.png 499w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

&nbsp;

&nbsp;

Agora esta na hora de criar nosso servidor, no serviço da Amazon EC2, cada máquina virtual recebe o nome de instância, sendo assim nós vamos criar nossa primeira instância. Há várias tipos de instâncias que podem ser criadas, o que diferencia elas são a capacidade de hardware , memória e processamento. Como estamos na versão gratuita nós temos direito a uma micro instância (t1.micro como é denominada no sistema) com um processador Intel(R) Xeon(R) CPU E5-2650 0 @ 2.00GHz e 650MB de ram .

Para criar a instância basta clicar em **instances **no menu esquerdo Instances e na próxima tela clicar no botão azul **Launch Instace **e você será enviado para a seguinte tela:

&nbsp;

<p style="text-align: center;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-10.png"><img class="aligncenter wp-image-347 size-medium" title="Clique para a ampliar" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-10-300x136.png" alt="ec2-passo-10" width="300" height="136" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-10-300x136.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-10-1024x465.png 1024w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-10.png 1599w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

<p style="text-align: left;">
  Criar uma instância (diga-se também máquina virtual) no Ec2 é muito simples, você tem que apenas escolher o sistema que deseja e pronto, já existe inclusive diversas versões de máquinas virtuais prontas para plataformas windows e Linux e ainda clicando em Community AMIs ( AMI de Amazon Machine Image) você tem acesso a versões de sistemas mantidas pela comunidade voltada para diversos ambientes de desenvolvimento ou objetivos diversos e ainda tem algumas AMIs para comprar na aba AWS marketplace . Eu já fui analista de infraestrutura Linux então optei por instalar uma imagem sem nada do Linux Centos e é essa que vou mostrar como instalar o ambiente PHP e Mysql.  Sendo assim na aba Quick Start escolham a primeira opção  Amazon Linux AMI, é um Centos sem nada instalado porém com diversos repositórios a disposição. Clique na opção conforme imagem abaixo :
</p>

<p style="text-align: left;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-11.png"><img class="aligncenter wp-image-352 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-11-300x45.png" alt="ec2-passo-11" width="300" height="45" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-11-300x45.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-11-1024x154.png 1024w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-11.png 1539w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

<p style="text-align: left;">
  A próxima tela é apenas para mostrar os detalhes da instância escolhida, como estamos na versão gratuita ele nos mostra e alerta que é uma máquina de baixo desempenho, basta clicar em <strong>Review and Launch </strong> conforme imagem abaixo.
</p>

<p style="text-align: left;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-12.png"><img class="aligncenter wp-image-353 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-12-300x136.png" alt="ec2-passo-12" width="300" height="136" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-12-300x136.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-12-1024x464.png 1024w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-12.png 1599w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

<p style="text-align: left;">
  Agora temos a tela com a visão geral da instância que acabamos de configurar, não se preocupe com o alerta em amarelo, ele apenas está dizendo que nosso servidor está disponível para ser acessado pelo mundo todo via internet na porta 22(serviço SSH). Ao clicar em <strong>Launch </strong>para confirmar toda a configuração o sistema vai nos solicitar para criar uma <strong>key pair</strong>, que é na verdade uma chave pública de acesso, o acesso primário as instâncias criadas pela Amazon é via SSH e para criar a instância ele exige que seja criado uma chave de acesso logo de início.
</p>

<p style="text-align: left;">
  Quando clicarmos em <strong>Launch </strong>vai abrir uma janela pedindo a seleção da key pair, como ainda não temos nenhuma criada escolha a opção <strong>create a new key pair</strong>, escolha um nome que você desejar e clique em <strong>download key pair . </strong> Após isso mude novamente o select para <strong>choose and existing key pair </strong>e escolha a kay pair que você acabou de criar, marque o checkbox  dizendo que você reconhece que tem acesso ao arquivo.pem e sem ele não podera acessar a instância. Depois clique em <strong>Launch Instance </strong>conforme imagem abaixo:
</p>

<p style="text-align: left;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-13.png"><img class="aligncenter wp-image-355 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-13-300x130.png" alt="ec2-passo-13" width="300" height="130" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-13-300x130.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-13-1024x446.png 1024w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-13-700x300.png 700w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-13.png 1593w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

<p style="text-align: left;">
  E pronto acabamos de criar nossa primeira instância
</p>

<p style="text-align: left;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-14.png"><img class="aligncenter wp-image-356 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-14-300x49.png" alt="ec2-passo-14" width="300" height="49" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-14-300x49.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-14-1024x170.png 1024w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-14.png 1583w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

<p style="text-align: left;">
  Agora podemos voltar a painel inicial, neste momento já temos uma máquia linux rodando na nuvem, porém o que queremos é um servidor web acessível de qualquer lugar do mundo, no momento só podemos acessar nossa máquina via SSH (porta 22) , então vamos liberar a porta 80 (http) para que um browser possa acessar este servidor. No menu esquerdo clique <strong>security Groups ,</strong> depois na tabela que vai aparecer clique sobre a linha launch-wizard-1, você vai ver que nas TCP ports apenas a porta 22 está liberada. Na opção <strong>create a new rule</strong>, escolha http e clique em  <strong>add rule. </strong>Após isso a tabela de portas vai ficar como a imagem abaixo :
</p>

<p style="text-align: left;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-15.png"><img class="aligncenter wp-image-358 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-15-300x137.png" alt="ec2-passo-15" width="300" height="137" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-15-300x137.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-15-1024x469.png 1024w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-15-870x400.png 870w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-15.png 1381w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

<p style="text-align: left;">
  Certo agora nosso servidor está com acesso a porta 80 para o serviço web e porta 22 para nossa administração, mas ainda falta um detalhe, precisamos ter um IP público de acesso associado a nossa instância, sendo assim vamos clicar em <strong>Elastic IPs  </strong>e em seguida <strong>Allocate new address </strong>e confirmar. Nosso IP já está alocado para nossa conta, agora vamos associá-lo a interface de nossa instância. Clique em <strong>Network Interfaces </strong>, marque a interface da sua instância e no botão <strong>Actions </strong>escolha a opção Associate Address conforme imagem abaixo:
</p>

<p style="text-align: left;">
  <a class="lightbox" title="Clique para ampliar" href="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-16.png"><img class="aligncenter wp-image-359 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-16-300x121.png" alt="ec2-passo-16" width="300" height="121" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-16-300x121.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-16-1024x415.png 1024w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-16.png 1589w" sizes="(max-width: 300px) 100vw, 300px" /></a>
</p>

<p style="text-align: left;">
  Selecione a interface que deseja associar o IP, marque a opção <strong>Allow associate </strong> e pronto, já temos nosso servidor todo configurado e acessível via internet. Agora vamos acessá-lo via SSH e configurar o apache, php e mysql.
</p>

<h2 style="text-align: left;">
  <span style="color: #339966;">Acessando e configurando o servidor</span>
</h2>

Nós vamos acessar nosso servidor via SSH, quando criamos a key pair fizemos o download da chave pública, agora vamos converter essa chave pública em privada. Para converter a chave e depois acessar nosso servidor vou usar o <a title="putty.org" href="http://www.putty.org/" target="_blank">putty</a> (você pode usar outros programas se quiser). Você deve fazer o download do **PuttyGen **e o **Putty . **

Execute o PuttyGen que vai converter nossa chave, com ele aberto clique em **conversions **e **import key**, selecione a chave que você fez download e confirme, conforme imagem abaixo:

&nbsp;

[<img class="aligncenter wp-image-363 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-17-300x86.png" alt="ec2-passo-17" width="300" height="86" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-17-300x86.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-17.png 493w" sizes="(max-width: 300px) 100vw, 300px" />](https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-17.png "Clique para ampliar"){.lightbox}

&nbsp;

&nbsp;

Depois do programa ler a chave apenas clique em **Save private Key,** você deve colocar o mesmo nome da key pair que criou. Feito isso o PuttyGen vai criar um arquivo de extensão .ppk. Uma coisa curiosa que aconteceu comigo é que fiz o download a primeira vez da chave de um notebook, e converti a mesma para privada de outro computador, por algum motivo que não entendi, só consegui acessar com a chave privada depois que fiz o download e converti do mesmo computador, após isso consegui acessar de qualquer computador mas o processo e download e conversão teve que ser do mesmo, apenas para constar minha situação ( pois me tomou alguns minutos de stress isso).

Agora vamos acessar nosso servidor usando o Putty se você nunca usou o putty com uma chave privada ai vai como fazer. Abra o Putty e clique no menu lateral em **SSH** e em seguida **Auth**, selecione a chave privada e confirme. conforme imagem abaixo:

&nbsp;

[<img class="aligncenter wp-image-365 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-18-300x290.png" alt="ec2-passo-18" width="300" height="290" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-18-300x290.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-18.png 464w" sizes="(max-width: 300px) 100vw, 300px" />](https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-18.png "Clique para ampliar"){.lightbox}

&nbsp;

Volte para o menu **session **e em hostname coloque endereço da seguinte forma [user]@[elastic\_ip] . A amazon sempre tem o mesmo usuário de acesso, uma vez que o que determina a autenticação é a chave primária. você deve usar sempre dessa forma : ec2-user@seu\_elastic_ip . Após acessar você verá a seguinte tela :

&nbsp;

[<img class="aligncenter wp-image-366 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-19-300x190.png" alt="ec2-passo-19" width="300" height="190" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-19-300x190.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-19.png 674w" sizes="(max-width: 300px) 100vw, 300px" />](https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-19.png "Clique para ampliar"){.lightbox}

&nbsp;

Agora já podemos começar a instalar os programas necessários, quando você acessa a primeira vez, sempre entra com um usuário comum. Para ter o acesso de root digite **sudo su .** A primeira coisa que vamos instalar é o apache, como estamos em um Linux Centos, vamos fazer tudo pelo gerenciador de pacotes **yum **, no console digite o seguinte comando :

<pre class="html|php|js|css">1- yum install httpd php php-mysql php-gd php-pdo php-cli php-dev php-pear mysql mysql-server;

2 -chkconfig --levels 235 httpd on;

3 -chkconfig --levels 235 mysqld on;

</pre>

&nbsp;

O comando **yum install **, como já fica evidente instalar os programas citados, para o meu caso além de apache( que no Centos se chama httpd), php e mysql só precisei de mais algumas bibliotecas adicionais do php, caso precise de mais é só fazer o mesmo processo. O comando **chkconfig **configura os tempos de execução do apache e mysql, para não complicar, estamos dizendo para ambos iniciarem automaticamente junto com SO.

Agora é só iniciar os serviços mysql e apache com o comando:

<pre class="html|php|js|css">1 - service httpd start;

2- service mysqld start

</pre>

Se acessarmos agora nosso enderço de IP público pelo browser já teremos a seguinte resposta:

&nbsp;

[<img class="aligncenter wp-image-372 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-20-300x146.png" alt="ec2-passo-20" width="300" height="146" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-20-300x146.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-20-1024x499.png 1024w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-20.png 1592w" sizes="(max-width: 300px) 100vw, 300px" />](https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-20.png "Clique para ampliar"){.lightbox}

&nbsp;

Por padrão no Centos os arquivos devem ficar em **/var/www/html . **Agora já temos nosso servidor dentro da Amazon e com um ambiente PHP e mysql rodando.

&nbsp;

## <span style="color: #339966;">Vinculando um domínio ao seu servidor na Amazon</span>

Agora que já temos nosso servidor PHP/Mysql rodando falta vincularmos ele a um domínio, vamos o meu exemplo como eu faço para quem digitar robissonoliveira.com.br ser redireiconado para o nosso IP da Amazon. Para isso existe outro serviço da Amazon, o **Route 53**, no menu **Services **localize este serviço e clique nele, está localizado na parte superior da tela a direita.

&nbsp;

[<img class="aligncenter wp-image-376 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-21-300x115.png" alt="ec2-passo-21" width="300" height="115" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-21-300x115.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-21-1024x394.png 1024w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-21.png 1100w" sizes="(max-width: 300px) 100vw, 300px" />](https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-21.png "Clique para ampliar"){.lightbox}

&nbsp;

Em seguida clique em **created Hosted Zone **

&nbsp;

[<img class="aligncenter wp-image-377 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-22-300x53.png" alt="ec2-passo-22" width="300" height="53" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-22-300x53.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-22.png 596w" sizes="(max-width: 300px) 100vw, 300px" />](https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-22.png "Clique para ampliar"){.lightbox}

&nbsp;

Agora digite seu domínio sem o WWW e clique em **create hosted zone.**

&nbsp;

[<img class="aligncenter wp-image-379 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-23-224x300.png" alt="ec2-passo-23" width="224" height="300" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-23-224x300.png 224w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-23.png 467w" sizes="(max-width: 224px) 100vw, 224px" />](https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-23.png "Clique para ampliar"){.lightbox}

&nbsp;

&nbsp;

Feito isso você vai ver a seguinte entrada confirmando a sua criação de zona de DNS.

&nbsp;

[<img class="aligncenter wp-image-382 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-24-300x46.png" alt="ec2-passo-24" width="300" height="46" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-24-300x46.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-24.png 941w" sizes="(max-width: 300px) 100vw, 300px" />](https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-24.png "Clique para ampliar"){.lightbox}

&nbsp;

&nbsp;

Agora você já criou a Zona de DNS padrão da Amazon, mas falta adicionar as entradas para o seu domínio com e sem o WWW e a entrada de MX que é responsável por redirecionar suas requisições de email. Para preencher essas entradas selecione a linha que você acabou de criar e clique em **Go to Record Sets.** Nesta tela você deve inserir entradas do tipo A  , CNAME e MX . É bem simples a operação, então vou colocar como exemplo minhas entradas para terem como base:

&nbsp;

[<img class="aligncenter wp-image-383 size-medium" src="//www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-25-300x92.png" alt="ec2-passo-25" width="300" height="92" srcset="https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-25-300x92.png 300w, https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-25.png 987w" sizes="(max-width: 300px) 100vw, 300px" />](https://www.robissonoliveira.com.br/wp-content/uploads/2013/11/ec2-passo-25.png "Clique para ampliar"){.lightbox}

&nbsp;

&nbsp;

## <span style="color: #339966;">Concluindo</span>

Este post ficou meio grande mas quis enfatizar os pontos positivos do serviço da Amazon e como agregou valor para a minha empresa e para meu site pessoal. Como já dito, é gratuito por um ano,  no entanto há alguns serviços que são cobrados desde o primeiro mês, que no caso é o DNS, mesmo assim os valores são mínimos. Para aqueles que tiverem a preocupação de quanto vão pagar daqui a um ano, a amazon disponibiliza uma calculadora para estimar seus custos baseados na quantidade utilizada de recursos, o link para a calculadora é <a title="Calculadora de custos Amazon AWS" href="http://calculator.s3.amazonaws.com/calc5.html?lng=pt_BR" target="_blank">este</a> .

Para dar um exemplo, para o meu site o custo ficou em U$$18,00 , cerca de 50 reais . Para mim é um custo aceitável em troca da estabilidade, disponibilidade e liberdade que tenho em gerenciar o serviço. Para terem uma ideia neste mesmo servidor instalei um serviço de Node.js para testar algumas aplicações que desenvolvo. Para aqueles que monetizam seu site acho que pode ter um ROI melhor ainda. Espero ter contribuído para aqueles que já conhecem a Amazon e tinham algumas dúvida como eu tinha e para quem acabou de conhecer.