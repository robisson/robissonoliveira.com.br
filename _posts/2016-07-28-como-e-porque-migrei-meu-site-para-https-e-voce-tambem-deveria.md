---
id: 905
title: Como e porque migrei meu site para HTTPS. E você também deveria!
date: 2016-07-28T23:53:53+00:00
author: Robisson Oliveira
excerpt: 'Eu migrei o meu site para HTTPS e nesse post vou explicar como e porque você que é  desenvolvedor ou do marketing deveria fazer mesmo para ter melhores resultados.'
layout: post
guid: https://www.robissonoliveira.com.br/?p=905
permalink: /infraestrutura-de-ti/como-e-porque-migrei-meu-site-para-https-e-voce-tambem-deveria
bluth_post_layout:
  - single_column
bluth_facebook_status:
  - 
bluth_twitter_status:
  - 
dsq_thread_id:
  - 5022898856
dsq_needs_sync:
  - 1
categories:
  - Infraestrutura de TI
---
<span style="font-weight: 400;">Eu migrei o meu site para HTTPS e nesse post vou explicar como e porque você que é  desenvolvedor ou do marketing deveria fazer mesmo para ter melhores resultados. Isso mesmo, se você é desenvolvedor já passa da hora de você desenvolver em um ambiente com SSL/HTTPS . E você que é do marketing, se deseja ter uma audiência mais qualificada e gerar mais impactos no negócio também. Então vamos explicar para cada uma das perspectivas as razões.</span>

&nbsp;

<!--more-->

## **<span style="color: #339966;">Mas o que ésse HTTPS ?</span>** 

<span style="font-weight: 400;">Primeiro vamos falar do HTTP ( Hyper text Transfer Protocol ) . Como o nome já diz, é um protocolo que define como sistemas de informação trocam dados entre dispositivos de redes. Uma vez que dois dispositivos estejam conectados e se comunicando sob este protocolo, eles passam a falar a mesma língua e a informação flui entre eles.</span>

<span style="font-weight: 400;">O problema desse protocolo é que a informação flui de forma aberta, ou seja, para quem possui conhecimento e também más intenções, é possível interceptar essas informações. Nesse ponto que falamos do </span>**HTTPS (** <span style="font-weight: 400;">Hyper text Transfer Protocol Secure ), que é outro protocolo seguindo os mesmos padrões do HTTP, porém adicionando uma camada a mais de segurança na transferências dos dados.</span>

<span style="font-weight: 400;">Os dados transferidos entre dispositivos sobre protocolo HTTPS são criptografados, de forma bem grosseira significa que somente as duas pontas que estão se comunicando entendem um a língua do outro.  Mas não sejamos radicais, nenhuma tecnologia é 100% segura, o fato é que o HTTPS troca informações criptografadas, que são mais seguras que o HTTP.</span>

<span style="font-weight: 400;">No nosso dia a dia de usuário de internet, podemos reconhecer um site que usa protocolo HTTPS pela barra de endereço do navegador, quando a mesma apresenta um cadeado com um fundo verde antes do endereço do site. Deixo um exemplo abaixo ( o/ ):</span>

&nbsp;

[<img class="size-full wp-image-907 aligncenter" src="https://www.robissonoliveira.com.br/wp-content/uploads/2016/07/barra-https.jpg" alt="barra-https" width="691" height="36" />](https://www.robissonoliveira.com.br/wp-content/uploads/2016/07/barra-https.jpg){.lightbox}

&nbsp;

## <span style="color: #339966;"><b>Porque o marketing deveria estar preocupado com o  HTTPS ?</b></span>

<span style="font-weight: 400;">Eu trabalho essencialmente no mercado imobiliário a 7 anos, mas já dei consultoria de SEO para algumas empresas do mercado de varejo em nichos como e-commerce de cosméticos e roupas. Em todos eles a maior parte da audiência do site sempre vinha de trafego orgânico, ou seja do Google. Isso mesmo, as pessoas que pesquisam no Google por alguma informação e chegam até o site das referidas empresas. Raras foram as empresas que o tráfego orgânico não era superior a 50% do total. Então estar bem posicionado no Google é vender mais.</span>

<span style="font-weight: 400;">Não é de hoje que o Google anunciou que o protocolo HTTPS é um fator de ranqueamento para os seus resultados de busca. Foi anunciado em agosto de 2014 no post </span>[<span style="font-weight: 400;">HTTPS as a ranking signal</span>](https://webmasters.googleblog.com/2014/08/https-as-ranking-signal.html) <span style="font-weight: 400;">no blog do </span>[<span style="font-weight: 400;">Google Webmaster central</span>](https://webmasters.googleblog.com/2014/08/https-as-ranking-signal.html) <span style="font-weight: 400;">. O Google que é centrado no que é melhor para o usuário, passou a entender que sites que usam HTTPS possuem um condição de oferecer uma experiência mais segura.</span>

&nbsp;

<h3 style="text-align: center;">
  <span style="font-weight: 400; color: #808080;">“Em agosto de 2014 o Google anunciou que o HTTPS é um dos fatores de ranqueamento para o seus resultados de busca”</span>
</h3>

&nbsp;

<span style="font-weight: 400;">Então basicamente é isso, sites que usam HTTPS tem uma chance de se posicionar melhor nos resultados do Google. Mas nem tudo é tão simples, uma url com HTTPS para o Google é diferente de uma que usa http, há um certo risco se não for tomada as devidas precauções de você migrar seu site para HTTPS e perder tráfego orgânico ao invés de ganhar. Mas isso eu vou explicar mais a frente como se prevenir.</span>

&nbsp;

## <span style="color: #339966;"><b>Porque os desenvolvedores deveriam estar olhando para o HTTPS ?</b></span>

<span style="font-weight: 400;">Em minha jornada de desenvolvedor, antes eu pensava que apenas sites que manipulavam informações extremamente importantes precisam ter seus ambientes protegidos com SSL/HTTPS , como bancos e e-commerce, hoje me apavoro em como eu pensava isso. Outro fator que minha ignorância desprezava é que eu pensava que usar HTTPS era equivalente a ter que investir muito em infraestrutura de TI. E por último, mas não menos importante eu também achava que usar o protocolo HTTPS deixaria a minha aplicação mais lenta por ele ter uma camada a mais de controle entre cliente/servidor.</span>

<span style="font-weight: 400;">Enfim, tudo isso que citei é mito. Toda a informação que trafega na internet deveria ser criptografada, indiferente da origem ou objetivo, e dito isso os usuários agradecem, ainda mais hoje com tantas invasões e interceptações que se tem notícias. O outro ponto é que obter um certificado SSL/HTTPS não é caro nem para a pessoa física, quanto muito para uma empresa.</span>

<span style="font-weight: 400;">Mas o que quero focar é que se você é desenvolvedor front-end ou backend, não importa. As tecnologias que estão evoluindo atualmente estão valorizando as aplicações que usam HTTPS, e de uma forma que você não consegue utilizar certas features se não estiver em um ambiente que use HTTPS. Logo, as aplicações que você desenvolve com o passar do tempo não conseguiram evoluir ou vão disparar alarmes de inseguras para os clientes que fazem uso do que você desenvolve.</span>

<span style="font-weight: 400;">Me chamou atenção esse ano no Google IO e também no Google Dev Summit que o Google vai permitir em futuro bem próximo as APIs do Chrome como Geolocalization, Service Workers, App Cache e Local storage apenas para aplicações que estejam rodando sob HTTPS. E você vai ficar sem usar esses recursos ? Em mundo onde performance e mobilidade é só o que se fala e praticamente tudo está virando uma API.</span>

&nbsp;

<h3 style="text-align: center;">
  <span style="font-weight: 400; color: #808080;">“O Google anunciou que em breve APIs do Chrome como Geolocalization, Service Workers, AppCache e LocalStorage só estarão disponíveis para aplicações que usam HTTPS”</span>
</h3>

&nbsp;

## <span style="color: #339966;"><b>Como eu migrei meu site WordPress para HTTPS</b></span>

<span style="font-weight: 400;">Para começar, eu precisei comprar um certificado SSL. Há alguns tipos de certificado que você pode comprar.  Eu apliquei no meu site o certificado SSL de validação de domínio. Não vou entrar em detalhes de qual tipo aplicar, mas você pode ter uma base mais detalhada no link </span>[<span style="font-weight: 400;">SSL: Tipos de certificados para proteger seu site</span>](https://cryptoid.com.br/ssl/tipos-de-certificados-ssl-para-proteger-seu-site/)**.** 

<span style="font-weight: 400;">Vale ressaltar que eu paguei R$13,95 na </span>[<span style="font-weight: 400;">br.godaddy.com/SSL</span>](http://br.godaddy.com/SSL) <span style="font-weight: 400;">, e nada mais, válido por um ano. Esse é outro detalhe, os certificados SSL tem validade, então indiferente do tipo que você escolha ele tem um período de vigência. </span>

<span style="font-weight: 400;">Vou explicar o processo da Godaddy, mas outros sites que vendem certificados SSL não vão escapar muito dessa regra. No momento em que estou me cadastrando, preciso informar ao certificador ( a Godaddy ) a minha chave </span>**CSR ( Certificate Signing Request ).**

<span style="font-weight: 400;">O meu site é hospedado na </span>[<span style="font-weight: 400;">Digital Ocean</span>](https://www.digitalocean.com/) <span style="font-weight: 400;">e todo o ambiente foi configurado por mim, apenas porque eu gosto de mexer para não perder o jeito e também para algumas experiências loucas que faço de tempo em tempo. Meu ambiente é um Sistema operacional  Linux com Debian 8 e um Servidor Apache 2.4. Os passos a seguir que vou demonstrar são para esse ambiente, mas em versões CentoOS também vai funcionar.</span>

&nbsp;

## <span style="color: #339966;"><b>Gerando o CSR ( Certificate Signing Request )</b></span>

<span style="font-weight: 400;">O </span>**CSR** <span style="font-weight: 400;">é um arquivo de texto criptografado, gerado pelo servidor web do seu site, contendo as informações para a solicitação do seu Certificado Digital. O </span>**CSR**<span style="font-weight: 400;">contém as informações da sua empresa (nome, departamento, cidade, estado, país) e a URL onde o certificado SSL será utilizado.</span>

<span style="font-weight: 400;">O CSR é gerado no servidor, então pode variar de um sistema operacional para outro, o método que utilizei é para servidores Linux. conheço. Para gear o CSR basta digitar no terminal:</span>

&nbsp;

<pre class="bash">openssl req -new -newkey rsa:2048 -nodes -keyout seudominio.key -out seudominio .csr</pre>

&nbsp;

<span style="font-weight: 400;">Após digitar esse comando alguns dados seus ou da sua empresa serão requisitados, basta preencher os dados e confirmar para que as chaves sejam geradas. Duas chaves serão geradas nesse processo conforme a imagem abaixo:</span>

&nbsp;

[<img class="aligncenter size-full wp-image-910" src="https://www.robissonoliveira.com.br/wp-content/uploads/2016/07/chave-csr.jpg" alt="chave-csr" width="570" height="89" />](https://www.robissonoliveira.com.br/wp-content/uploads/2016/07/chave-csr.jpg){.lightbox}

&nbsp;

<span style="font-weight: 400;">Caso seu servidor não tenha o openssl instalado basta digitar um </span>_<span style="font-weight: 400;">sudo apt-get install ssl  </span>_<span style="font-weight: 400;">. Caso você não esteja usando o apache como eu, deixo esse para </span>[<span style="font-weight: 400;">gerar o CSR para os servidores mais populares</span>](https://br.godaddy.com/help/como-gerar-uma-solicitacao-de-assinatura-de-certificado-5343)<span style="font-weight: 400;">.</span>

&nbsp;

## <span style="color: #339966;"><b>Fazendo o download e instalando o  certificado</b></span>

<span style="font-weight: 400;">Passada as etapas de pagamento do certificado, para que o certificador possa gerar o seu certificado SSL ele precisa do seu CSR, no caso Godaddy abriu um campo de texto para que eu pudesse colocar o conteúdo e informar o meu servidor, que no caso é o Apache. E só isso, após inserir o CSR ele confirmar qual é o seu domínio desejado e o certificado é criado. Já podemos fazer o download do certificado. O certificado que você deve instalar no seu servidor é baseado em 2 arquivos com a extensão </span>**crt**<span style="font-weight: 400;">.</span>

<span style="font-weight: 400;">O jeito mais simples de configurar o SSL no Apache é dentro do próprio arquivo de virutalhosts . Por padrão o suporte a SSL no apache vem desabilitado, então para habilitar o ssl digite o comando abaixo:</span>

&nbsp;

<pre class="bash"><i><span style="font-weight: 400;">a2enmod ssl</span></i></pre>

&nbsp;

<span style="font-weight: 400;">Feito isso vá até o arquivo do seu virtualhost, no meu caso fica em </span>_<span style="font-weight: 400;">/etc/apache2/sites-available/default-ssl.conf </span>_<span style="font-weight: 400;">e adicione ou altere caso já exista as seguintes linhas:</span>

&nbsp;

  * **SSLCertificateFile** &#8211; Arquivo crt que você fez o download
  * **SSLCertificateKeyFile** &#8211; Este é o arquivo que você gerou com o nome do seu domínio e extensão _.key_
  * **SSLCertificateChainFile** &#8211; Arquivo crt que você fez o download
  * **SSLCACertificateFile** &#8211; Arquivo crt de bundle que você fez o download.

&nbsp;

<p style="text-align: left;">
  <span style="font-weight: 400;">Coloque os aquivos que você fez o download em lugar apropriado e seguro dentro do servidor. Certifique-se também que seu virtual está configurado para a porta 443 conforme imagem abaixo:</span>
</p>

&nbsp;

<p style="text-align: left;">
  <a class="lightbox" href="https://www.robissonoliveira.com.br/wp-content/uploads/2016/07/virtual-hosts-apache.jpg"><img class="size-full wp-image-915 aligncenter" src="https://www.robissonoliveira.com.br/wp-content/uploads/2016/07/virtual-hosts-apache.jpg" alt="virtual-hosts-apache" width="409" height="92" /></a>
</p>

&nbsp;

<p style="text-align: left;">
  <span style="font-weight: 400;">Feito essas etapas basta dar um reload no apache com o comando abaixo:</span>
</p>

&nbsp;

<p style="text-align: left;">
  <pre class="bash">service apache2 reload</pre>
</p>

&nbsp;

<p style="text-align: left;">
  <span style="font-weight: 400;">Pronto, seu servidor já está configurado para servir páginas com protocolo HTTPS. Mas isso acaba gerando um problema de SEO para você , porque as páginas sob HTTP também estão funcionando, ou seja, no momento para os motores de busca podemos dizer que você tem 2 sites com o mesmo conteúdo, é isso é ruim.</span>
</p>

&nbsp;

<h2 style="text-align: left;">
  <span style="color: #339966;"><b>Redirecionando todo o conteúdo HTTP do site para HTTPS</b></span>
</h2>

<p style="text-align: left;">
  <span style="font-weight: 400;">Essa é uma das tarefas mais importante ao meu ver atualmente quando se migra um site para HTTPS, que é a tarefa de mapear as suas URL que eram acessadas via HTTP para que sejam redirecionadas para HTTPS. Isso porque como descrito na </span><a href="https://support.google.com/webmasters/answer/6073543?hl=pt-BR"><span style="font-weight: 400;">ajuda do Google Search Console</span></a><span style="font-weight: 400;">, o Google trata a mudança para HTTPS como se vocês estivesse colocando um novo site no ar.</span>
</p>

<p style="text-align: left;">
  <span style="font-weight: 400;">Como já mencionei, meu site é um WordPress rodando sob um servidor Apache e que roda em um servidor Linux Debian 8. Dito isso a primeira coisa que fiz foi alterar na raíz do meu site o arquivo </span><i><span style="font-weight: 400;">.htaccess</span></i><span style="font-weight: 400;"> para redirecionar todas as requisições que não forem feitas por HTTPS  para o mesmo destino em HTTPS.</span>
</p>

&nbsp;

<p style="text-align: left;">
  <pre class="bash"></p>


<p style="text-align: left;">
  <i><span style="font-weight: 400;">RewriteCond %{HTTPS} !=on</span></i>
</p>


<p style="text-align: left;">
  <i><span style="font-weight: 400;">RewriteRule ^ HTTPS://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]</span></i>
</p>


<p style="text-align: left;">
  </pre>
  
</p>


<p>
  &nbsp;
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">Esse código está testando se a requisição é feita em HTTPS e caso contrário está redirecionando com um código de 301, que é o </span><b>movido permanentemente.</b>
</p>


<p>
  &nbsp;
</p>


<h2 style="text-align: left;">
  <span style="color: #339966;"><b>Mapeando as principais URL do seu site</b></span>
</h2>


<p style="text-align: left;">
  <span style="font-weight: 400;">Para que você não perca trafego em uma migração para HTTPS é fundamental que você identifique quais são as principais páginas de destino do seu site, isso é aquelas que recebem o maior volume de tráfego com origem de tráfego orgânico e teste se elas estão redirecionando com código 301 para a nova URL em HTTPS.</span>
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">Uma maneira de você validar se suas urls HTTP estão sendo redirecionadas para HTTPS com código 301 é usando um </span><a href="http://www.agenciamestre.com/ferramentas-seo/http-header-checker/"><span style="font-weight: 400;">http header checker</span></a><span style="font-weight: 400;"> , já fica de dica esse da Agência Mestre.</span>
</p>


<p>
  &nbsp;
</p>


<p style="text-align: left;">
  <a class="lightbox" href="https://www.robissonoliveira.com.br/wp-content/uploads/2016/07/http-header-check.jpg"><img class="aligncenter size-medium wp-image-917" src="https://www.robissonoliveira.com.br/wp-content/uploads/2016/07/http-header-check-300x162.jpg" alt="http-header-check" width="300" height="162" /></a>
</p>


<p>
  &nbsp;
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">Como podem ver eu teste a url antiga do meu site e a ferramenta indica que ele está sendo redirecionada para o HTTPS.</span>
</p>


<p>
  &nbsp;
</p>


<h2 style="text-align: left;">
  <span style="color: #339966;"><b>Cuidados com o WordPress na migração para HTTPS</b></span>
</h2>


<p style="text-align: left;">
  <span style="font-weight: 400;">O WordPress tem configurado em banco de dados a url padrão do site, então você deve alterar em </span><b>Configurações -> Geral </b><span style="font-weight: 400;">a url do seu site colocando HTTPS conforme imagem abaixo:</span>
</p>


<p>
  &nbsp;
</p>


<p style="text-align: left;">
  <a class="lightbox" href="https://www.robissonoliveira.com.br/wp-content/uploads/2016/07/configuracoes-url-wordpress.jpg"><img class="aligncenter size-medium wp-image-918" src="https://www.robissonoliveira.com.br/wp-content/uploads/2016/07/configuracoes-url-wordpress-300x77.jpg" alt="configuracoes-url-wordpress" width="300" height="77" /></a>
</p>


<p>
  &nbsp;
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">Outro problema que eu tive, mas não sei dizer se todos terão é com o gerenciador de media do WordPress. Todas as imagens estavam setadas com o caminho absoluto e com HTTP , e isso desqualifica a validação do HTTPS. Então para não ter que alterar um a um manualmente eu executei a query abaixo, substituindo todas as entradas HTTP por //, que faz o navegador enviar as requisições pelo protocolo padrão que a página página  está.</span>
</p>


<p>
  &nbsp;
</p>


<p style="text-align: left;">
  <pre class="sql"></p>


<p style="text-align: left;">
  <span style="font-weight: 400;">UPDATE wp_posts </span>
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">SET    post_content = ( Replace (post_content, 'src="http://', 'src="//') )</span>
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">WHERE  Instr(post_content, 'jpeg') > 0 </span>
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">        OR Instr(post_content, 'jpg') > 0 </span>
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">        OR Instr(post_content, 'gif') > 0 </span>
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">        OR Instr(post_content, 'png') > 0;</span>
</p>


<p style="text-align: left;">
  </pre>
  
</p>


<p>
  &nbsp;
</p>


<h2 style="text-align: left;">
  <span style="color: #339966;"><b>Resolução de problemas</b></span>
</h2>


<p style="text-align: left;">
  <span style="font-weight: 400;">Meu conselho é que depois que você instalar o certificado, teste seu site com um simples </span><i><span style="font-weight: 400;">index.html </span></i><span style="font-weight: 400;">em branco, assim vai saber se a instalação funcionou. Isso porque qualquer arquivo incluído no seu código que não seja de uma origem HTTPS vai invalidar seu certificado.</span>
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">Tenha sempre um ambiente de homologação para validar tudo antes de migrar, não tente fazer tudo em produção, o risco é muito grande.</span>
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">Acompanhe diariamente o número de páginas indexadas pelo Google, senão vai cair e também as páginas 404 pelo Google Search Console. Acompanhe também o tráfego geral do seu site no Google Analytics ou outra ferramenta que de mensuração que você utilize.</span>
</p>


<p style="text-align: left;">
  <span style="font-weight: 400;">Isso é tudo que eu tinha para dizer sobre a migração do meu site para HTTPS, porque eu migrei e porque você independente do mercado de atuação deveria estar fazendo o mesmo.</span>
</p>


<p>
  &nbsp;
</p>


<h2 style="text-align: center;">
  <span style="color: #339966;">E você o que falta para migrar seu ambiente para SSL/HTTPS ?</span>
</h2>


<p>
  &nbsp;
</p>