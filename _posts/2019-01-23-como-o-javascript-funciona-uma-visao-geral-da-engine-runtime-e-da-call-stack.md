---
id: 1074
title: 'Como o Javascript funciona: uma visão geral da engine, runtime e da call stack'
date: 2019-01-23T20:15:02+00:00
author: Robisson Oliveira
layout: post
guid: https://www.robissonoliveira.com.br/?p=1074
permalink: /desenvolvimento-de-software/como-o-javascript-funciona-uma-visao-geral-da-engine-runtime-e-da-call-stack
bluth_post_layout:
  - single_column
bluth_facebook_status:
  - 
bluth_twitter_status:
  - 
dsq_needs_sync:
  - 1
categories:
  - Desenvolvimento de Software
  - frontend
---
<p id="b3bd" class="graf graf--p graf-after--h3">
  Como Javascript está ficando mais e mais popular, os times estão aproveitando o seu suporte em muitos níveis da sua stack — front-end, back-end, apps híbridas, dispositivos embarcados e muito mais.
</p>

<p id="3235" class="graf graf--p graf-after--p">
  Este post é destinado a ser o primeiro de uma série que visa aprofundar o JavaScript e como ele realmente funciona: pensamos que conhecendo os blocos construídos do Javascript e como eles funcionam juntos, você vai estar habilitado a escrever melhores códigos e apps. Nós também compartilhamos algumas regras de ouro que usamos quando construímos <a class="markup--anchor markup--p-anchor" href="https://www.sessionstack.com/?utm_source=medium&utm_medium=source&utm_content=javascript-series-post1-intro" target="_blank" rel="nofollow noopener" data-href="https://www.sessionstack.com/?utm_source=medium&utm_medium=source&utm_content=javascript-series-post1-intro">SessionStack</a>, uma aplicação Javascript leve que precisa ser robusta e de alto desempenho para se manter competitiva.<!--more-->
</p>

<p id="133d" class="graf graf--p graf-after--p">
  Como mostrado no <a class="markup--anchor markup--p-anchor" href="http://githut.info/" target="_blank" rel="nofollow noopener" data-href="http://githut.info/">GitHut stats</a>, Javascript está entre os top termos de repositórios ativos e total de pushes no GitHub. Ele não fica muito atrás nas outras categorias também.
</p><figure id="c847" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/1000/1*Zf4reZZJ9DCKsXf5CSXghg.png" alt="" data-image-id="1*Zf4reZZJ9DCKsXf5CSXghg.png" data-is-featured="true" />
</div></figure> 

<p id="bd35" class="graf graf--p graf-after--figure">
  <a class="markup--anchor markup--p-anchor" href="https://madnight.github.io/githut/" target="_blank" rel="nofollow noopener" data-href="https://madnight.github.io/githut/">(Confira as estatísticas atualizadas da linguagem no GitHub)</a>
</p>

<p id="88a5" class="graf graf--p graf-after--p">
  Se os projetos estão ficando cada mais dependentes de Javascript, isso significa que os desenvolvedores tem que estar utilizando todas as coisas que a linguagem e o ecossistema fornecem com uma profunda compreensão do funcionamento interno, a fim de construir um software incrível.
</p>

<p id="e2f3" class="graf graf--p graf-after--p">
  Como se constata, há um monte de desenvolvedores que estão usando o Javascript diariamente, mas não tem o conhecimento do que acontece debaixo do capô.
</p>



## <span style="color: #339966;"><strong class="markup--strong markup--h4-strong">Visão geral</strong></span> {#e11c.graf.graf--h4.graf-after--p}

<p id="6b41" class="graf graf--p graf-after--h4">
  Quase todo mundo tem ouvido falar da engine V8 como um conceito, e a maioria das pessoas sabem que o Javascript é single-threaded ou que está usando callback queue.
</p>

<p id="8926" class="graf graf--p graf-after--p">
  Neste post, nós vamos ir através destes conceitos em detalhes e explicar como o Javascript atualmente executa. Conhecendo estes detalhes, você vai estar habilitado a escrever aplicativos melhores e sem bloqueio que usam adequadamente as APIs fornecidas.
</p>

<p id="ba9d" class="graf graf--p graf-after--p">
  Você é relativamente novo em Javascript, esse post vai ajudar você a entender porque Javascript é tão “estranho” comparado com outras linguagens.
</p>

<p id="11df" class="graf graf--p graf-after--p">
  E se você é um experiente desenvolvedor Javascript, esperamos que ele dê a você algumas ideias de como o tempo de execução do Javascript que você está utilizando todo o dia atualmente funciona.
</p>

### <strong class="markup--strong markup--h4-strong">A engine Javascript</strong>

<p id="f756" class="graf graf--p graf-after--h4">
  Um popular exemplo da engine Javascript é a engine V8 do Google. A engine V8 é usada dentro do Google Chrome e do Node.js por exemplo. Aqui está um muito simplificada visão do que ela parece:
</p><figure id="0745" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/1000/1*OnH_DlbNAPvB9KLxUCyMsA.png" alt="" data-image-id="1*OnH_DlbNAPvB9KLxUCyMsA.png" />
</div></figure> 

<p id="1c6d" class="graf graf--p graf-after--figure">
  A engine consiste de dois principais componentes:
</p>

<ul class="postList">
  <li id="a54d" class="graf graf--li graf-after--p">
    Memory Heap — é onde a alocação de memória acontece.
  </li>
  <li id="69a9" class="graf graf--li graf-after--li">
    Call Stack — onde seus stack frames(quadros de pilha) estão enquanto seu código.
  </li>
</ul>

###  {.graf.graf--h4.graf-after--li}

### O Runtime( tempo de execução ) {#6958.graf.graf--h4.graf-after--li}

<p id="bc95" class="graf graf--p graf-after--h4">
  Há APIs no browser que têm sido utilizadas por quase todo o desenvolvedor Javascript(exemplo: “setTimeout”). Estas APIs, no entanto, não são fornecidas pela Engine.
</p>

<p id="8cc5" class="graf graf--p graf-after--p">
  Então, de onde elas vem?
</p>

<p id="49e7" class="graf graf--p graf-after--p">
  Acontece que a realidade é um pouco mais complicada.
</p><figure id="6673" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/1000/1*4lHHyfEhVB0LnQ3HlhSs8g.png" alt="" data-image-id="1*4lHHyfEhVB0LnQ3HlhSs8g.png" />
</div></figure> 

<p id="3f94" class="graf graf--p graf-after--figure">
  Então, temos a engine, mas na verdade tem muito mais. Temos essas coisas das quais chamamos Web APIs que são fornecidas pelos browsers, como o DOM, AJAX, setTimeout e muito mais.
</p>

<p id="4c76" class="graf graf--p graf-after--p">
  E então, temos o tão popular <strong class="markup--strong markup--p-strong">event loop </strong>e o <strong class="markup--strong markup--p-strong">callback queue.</strong>
</p>

### <strong class="markup--strong markup--h4-strong">A Call Stack (Pilha de chamadas)</strong> {#bbe6.graf.graf--h4.graf-after--p}

<p id="7a09" class="graf graf--p graf-after--h4">
  Javascript é uma linguagem de programação single-thread, o que significa que ela tem um única Call Stack. Portanto ela só pode fazer uma coisa de cada vez.
</p>

<p id="ed78" class="graf graf--p graf-after--p">
  A <strong class="markup--strong markup--p-strong">Call Stack</strong> é uma estrutura de dados que armazena basicamente onde no programa nós estamos. Se entrarmos em uma função, nós colocamos ela sobre o topo da Stack. Se retornamos de uma função, saímos do topo da stack. Isso é tudo que a stack pode fazer.
</p>

<p id="d00d" class="graf graf--p graf-after--p">
  Vamos ver um exemplo. Veja o seguinte código:
</p>

<pre id="8ad9" class="graf graf--pre graf-after--p">function multiply(x, y) {
    return x * y;
}</pre>

<pre id="3648" class="graf graf--pre graf-after--pre">function printSquare(x) {
    var s = multiply(x, x);
    console.log(s);
}</pre>

<pre id="c017" class="graf graf--pre graf-after--pre">printSquare(5);</pre>

<p id="d3e5" class="graf graf--p graf-after--pre">
  Quando a engine começa a executar esse código, a Call Stack vai estar vazia. Depois, os passos serão os seguintes:
</p><figure id="0a5e" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/1000/1*Yp1KOt_UJ47HChmS9y7KXw.png" alt="" data-image-id="1*Yp1KOt_UJ47HChmS9y7KXw.png" />
</div></figure> 

<p id="e5c7" class="graf graf--p graf-after--figure">
  Cada entrada na Call Stack é chamada de <strong class="markup--strong markup--p-strong">Stack frame.</strong>
</p>

<p id="99ae" class="graf graf--p graf-after--p">
  E isso é exatamente como stack traces estão sendo construídos quando uma exceção é send lançada — isso é basicamente o estado da Call Stack quando a exceção acontece. Dê uma olhada no seguinte código:
</p>

<pre id="4da2" class="graf graf--pre graf-after--p">function foo() {
    throw new Error('SessionStack will help you resolve crashes :)');
}</pre>

<pre id="6272" class="graf graf--pre graf-after--pre">function bar() {
    foo();
}</pre>

<pre id="a1a7" class="graf graf--pre graf-after--pre">function start() {
    bar();
}</pre>

<pre id="f386" class="graf graf--pre graf-after--pre">start();</pre>

<p id="ecd5" class="graf graf--p graf-after--pre">
  Se isso é executado no Chrome (assumindo que esse código está um arquivo chamado foo.js), o seguinte stack trace vai ser produzido:
</p><figure id="bee1" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/1000/1*T-W_ihvl-9rG4dn18kP3Qw.png" alt="" data-image-id="1*T-W_ihvl-9rG4dn18kP3Qw.png" />
</div></figure> 

<p id="2152" class="graf graf--p graf--startsWithDoubleQuote graf-after--figure">
  <strong class="markup--strong markup--p-strong">“Explodir a stack” — </strong>isso acontece quando você chega no tamanho máximo da Call Stack. E o que poderia acontecer muito facilmente, especialmente se você está utilizando recursão sem testar seu código muito extensivamente. Dê uma olhada neste exemplo de código:
</p>

<pre id="fdb4" class="graf graf--pre graf-after--p">function foo() {
    foo();
}</pre>

<pre id="8b4f" class="graf graf--pre graf-after--pre">foo();</pre>

<p id="c7e3" class="graf graf--p graf-after--pre">
  Quando a engine começa a executar esse código, ela começa chamando a função “foo”. Essa função, no entanto, é recursiva e começa a chamar ela mesmo sem qualquer condição para terminar. Então em cada ponto da execução, a mesma função é adicionada para a Call Stack de novo e de novo. Isso parece alguma coisa isso:
</p><figure id="9125" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/1000/1*AycFMDy9tlDmNoc5LXd9-g.png" alt="" data-image-id="1*AycFMDy9tlDmNoc5LXd9-g.png" />
</div></figure> 

<p id="54bb" class="graf graf--p graf-after--figure">
  Em algum ponto, no entanto, o número de chamadas de função na Call Stack excede o tamanho atual da Call Stack, e o browser decide tomar uma ação, lançando um erro, que pode parecer alguma coisa como isso:
</p><figure id="83f0" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/1000/1*e0nEd59RPKz9coyY8FX-uw.png" alt="" data-image-id="1*e0nEd59RPKz9coyY8FX-uw.png" />
</div></figure> 

<p id="ca08" class="graf graf--p graf-after--figure">
  Executando código sobre um single-thread pode ser muito fácil desde que você não tenha que lidar com cenários complicados que são criados em ambientes multi-threaded — por exemplo, deadlocks(pontos sem saída).
</p>

<p id="bc0c" class="graf graf--p graf-after--p">
  Mas executando sobre single-thread é muito limitante também. Já que o Javascript tem uma única Call Stack,<strong class="markup--strong markup--p-strong"> o que acontece quando as coisas estão lentas ?</strong>
</p>

## <span style="color: #339966;"><strong class="markup--strong markup--h4-strong">Concorrência e o Event Loop</strong></span> {#bdd9.graf.graf--h4.graf-after--p}

<p id="77d2" class="graf graf--p graf-after--h4">
  O que acontece quando você tem chamadas de função na Call Stack que tomam uma enorme quantidade de tempo a fim de ser processada? Por exemplo, imagine que você quer fazer alguma transformação complexa de imagem com Javascript no browser.
</p>

<p id="b48f" class="graf graf--p graf-after--p">
  Você pode perguntar — Por que isso é um problema ? O problema é que enquanto a Call Stack tem funções para executar, o browser não pode atualmente fazer qualquer coisa mais — ele está bloqueado. Isso significa que o browser não pode renderizar, ele não pode executar qualquer outro código, está preso. E isso cria problemas se você quer UIs fluídas em seu código.
</p>

<p id="8a1b" class="graf graf--p graf-after--p">
  E esse não é o único problema. Uma vez que o browser começa processar muitas tarefas na Call Stack, ele pode parar de responder por um longo tempo. E a maioria dos browsers tomam uma ação criando um erro, perguntando se você quer encerrar a página web.
</p><figure id="8be1" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/1000/1*WlMXK3rs_scqKTRV41au7g.jpeg" alt="" data-image-id="1*WlMXK3rs_scqKTRV41au7g.jpeg" />
</div></figure> 

<p id="2f53" class="graf graf--p graf-after--figure">
  Agora, essa não é a melhor experiência do usuário não é?
</p>

<p id="fce0" class="graf graf--p graf-after--p">
  Então, como podemos executar códigos pesados ​​sem bloquear a interface do usuário e deixar o navegador sem resposta? Bem, a solução é <strong class="markup--strong markup--p-strong">callbacks assíncronos</strong>.
</p>

<p id="5c58" class="graf graf--p graf-after--p">
  Isso vai ser explicado em maiores detalhes na <strong class="markup--strong markup--p-strong">parte 2 </strong>de<strong class="markup--strong markup--p-strong"> </strong><a class="markup--anchor markup--p-anchor" href="https://blog.sessionstack.com/how-javascript-works-inside-the-v8-engine-5-tips-on-how-to-write-optimized-code-ac089e62b12e" target="_blank" rel="nofollow noopener" data-href="https://blog.sessionstack.com/how-javascript-works-inside-the-v8-engine-5-tips-on-how-to-write-optimized-code-ac089e62b12e">“Como Javascript atualmente funciona: Dentro da engine V8 + 5 dicas de como escrever código otimizado”</a> (post ainda não traduzido, o link é do post original)
</p>

<p id="51b3" class="graf graf--p graf-after--p">
  Enquanto isso, se você estiver com dificuldades para reproduzir e entender os problemas em seus aplicativos JavaScript, dê uma olhada no <a class="markup--anchor markup--p-anchor" href="https://www.sessionstack.com/?utm_source=medium&utm_medium=blog&utm_content=Post-1-overview-outro" target="_blank" rel="nofollow noopener" data-href="https://www.sessionstack.com/?utm_source=medium&utm_medium=blog&utm_content=Post-1-overview-outro">SessionStack</a>. SessionStack registra todas as coisas que acontecem em suas web apps: todas as mudanças do DOM, interações do usuário, exceções Javascript, stack traces, requisições de redes que falharam e mensagens de debug.
</p>

<p id="6add" class="graf graf--p graf-after--p">
  Com SessionStack, você pode dar reproduzir problemas em seus web apps como se fossem vídeos e tudo o que está acontecendo para seu usuário.
</p>

<p id="50b4" class="graf graf--p graf-after--p">
  Há um plano grátis, não precisa de cartão de crédito. <a class="markup--anchor markup--p-anchor" href="https://www.sessionstack.com/solutions/developers/?utm_source=medium&utm_medium=blog&utm_content=Post-1-overview-getStarted" target="_blank" rel="nofollow noopener" data-href="https://www.sessionstack.com/solutions/developers/?utm_source=medium&utm_medium=blog&utm_content=Post-1-overview-getStarted">Comece agora.</a>
</p><figure id="eb9a" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/1000/1*kEQmoMuNBDfZKNSBh0tvRA.png" alt="" data-image-id="1*kEQmoMuNBDfZKNSBh0tvRA.png" />
</div></figure> 

<p id="a597" class="graf graf--p graf-after--figure">
  <em class="markup--em markup--p-em">Este é um artigo traduzido com a autorização do autor. O artigo original pode ser lido em </em><a class="markup--anchor markup--p-anchor" href="https://blog.sessionstack.com/how-does-javascript-actually-work-part-1-b0bacc073cf" target="_blank" rel="nofollow noopener" data-href="https://blog.sessionstack.com/how-does-javascript-actually-work-part-1-b0bacc073cf"><em class="markup--em markup--p-em">https://blog.sessionstack.com/how-does-javascript-actually-work-part-1-b0bacc073cf</em></a>
</p>

<p id="8655" class="graf graf--p graf-after--p graf--trailing">
  <strong class="markup--strong markup--p-strong">Autor do post original — </strong>Alexander Zlatkov<strong class="markup--strong markup--p-strong"> </strong>— Co-founder & CEO <a class="markup--anchor markup--p-anchor" href="http://twitter.com/SessionStack" target="_blank" rel="nofollow noopener" data-href="http://twitter.com/SessionStack">@SessionStack</a>
</p>