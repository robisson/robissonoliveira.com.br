---
id: 1083
title: 'Como o JavaScript funciona: dentro da engine V8 + 5 dicas sobre como escrever código otimizado'
date: 2019-02-01T12:59:05+00:00
author: Robisson Oliveira
layout: post
guid: https://www.robissonoliveira.com.br/?p=1083
permalink: /desenvolvimento-de-software/como-o-javascript-funciona-dentro-da-engine-v8-5-dicas-sobre-como-escrever-codigo-otimizado
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
<p id="743c" class="graf graf--p graf-after--h3">
  Algumas semanas atrás nós começamos uma série com o objetivo de aprofundar como o Javascript atualmente funciona: pensamos que conhecendo melhor os blocos de construção do Javascript e como eles funcionam juntos, você vai estar habilitados a escrever melhores códigos e aplicativos.
</p>

<p id="d07c" class="graf graf--p graf-after--p">
  <a class="markup--anchor markup--p-anchor" href="https://medium.com/reactbrasil/como-o-javascript-funciona-uma-vis%C3%A3o-geral-da-engine-runtime-e-da-call-stack-471dd5e1aa30" target="_blank" data-href="https://medium.com/reactbrasil/como-o-javascript-funciona-uma-vis%C3%A3o-geral-da-engine-runtime-e-da-call-stack-471dd5e1aa30">O primeiro post dessa série</a> focou em fornecer uma visão geral da engine, o runtime e a call stack. Esse segundo post vai mergulhar nas partes internas da engine Javascript V8 do Google. Também vamos fornecer algumas dicas rápidas de como código Javascript melhor — melhores práticas que nosso time de desenvolvimento no <a class="markup--anchor markup--p-anchor" href="https://www.sessionstack.com/" target="_blank" rel="nofollow noopener" data-href="https://www.sessionstack.com/">SessionStack</a> segue quando está construindo o produto.
</p>

<p class="graf graf--p graf-after--p">
  <!--more-->
</p>

### <strong class="markup--strong markup--h3-strong">Visão geral</strong> {#749b.graf.graf--h3.graf-after--p}

<p id="c4f2" class="graf graf--p graf-after--h3">
  Uma <strong class="markup--strong markup--p-strong">engine Javascript </strong>é um programa ou um interpretador que executa código Javascript. Uma engine Javascript pode ser implementada como um interpretador padrão, ou apenas um compilador que na hora certa(Just-in-time) compila Javascript para bytecode de alguma forma.
</p>

<p id="aec9" class="graf graf--p graf-after--p">
  Essa é uma lista de projetos populares que estão implementando uma engine Javascript:
</p>

<ul class="postList">
  <li id="0cb9" class="graf graf--li graf-after--p">
    <a class="markup--anchor markup--li-anchor" href="https://en.wikipedia.org/wiki/V8_%28JavaScript_engine%29" target="_blank" rel="nofollow noopener" data-href="https://en.wikipedia.org/wiki/V8_%28JavaScript_engine%29">V8</a> — open source, desenvolvido pelo Google, escrito em C++
  </li>
  <li id="31cb" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="https://en.wikipedia.org/wiki/Rhino_%28JavaScript_engine%29" target="_blank" rel="nofollow noopener" data-href="https://en.wikipedia.org/wiki/Rhino_%28JavaScript_engine%29">Rhin</a>o — gerenciado pela Mozilla Foundation, open source, desenvolvido inteiramente em Java
  </li>
  <li id="973e" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="https://en.wikipedia.org/wiki/SpiderMonkey_%28JavaScript_engine%29" target="_blank" rel="nofollow noopener" data-href="https://en.wikipedia.org/wiki/SpiderMonkey_%28JavaScript_engine%29">SpiderMonkey</a> — a primeira engine Javascript, que um dia empoderou o Netscape Navigator, e hoje empodera o Firefox
  </li>
  <li id="ab25" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="https://en.wikipedia.org/wiki/JavaScriptCore" target="_blank" rel="nofollow noopener" data-href="https://en.wikipedia.org/wiki/JavaScriptCore">JavaScriptCore</a> — open source, comercializado como Nitro desenvolvido pela Apple para o Safari
  </li>
  <li id="e479" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="https://en.wikipedia.org/wiki/KJS_%28KDE%29" target="_blank" rel="nofollow noopener" data-href="https://en.wikipedia.org/wiki/KJS_%28KDE%29">KJS</a> — KDE’s engine originalmente desenvolvido por Harri Porten para o projeto KDE Konqueror web browser
  </li>
  <li id="6ae3" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="https://en.wikipedia.org/wiki/Chakra_%28JScript_engine%29" target="_blank" rel="nofollow noopener" data-href="https://en.wikipedia.org/wiki/Chakra_%28JScript_engine%29">Chakra (JScript9)</a> — Internet Explorer
  </li>
  <li id="8920" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="https://en.wikipedia.org/wiki/Chakra_%28JavaScript_engine%29" target="_blank" rel="nofollow noopener" data-href="https://en.wikipedia.org/wiki/Chakra_%28JavaScript_engine%29">Chakra (JavaScript)</a> — Microsoft Edge
  </li>
  <li id="86e8" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="https://en.wikipedia.org/wiki/Nashorn_%28JavaScript_engine%29" target="_blank" rel="nofollow noopener" data-href="https://en.wikipedia.org/wiki/Nashorn_%28JavaScript_engine%29">Nashorn</a>, open source como parte do OpenJDK, escrito pela Oracle Java Languages e Tool Group
  </li>
  <li id="0616" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="https://en.wikipedia.org/wiki/JerryScript" target="_blank" rel="nofollow noopener" data-href="https://en.wikipedia.org/wiki/JerryScript">JerryScript</a> — é uma engine leve para a internet das coisas(IOT).
  </li>
</ul>

&nbsp;

### <strong class="markup--strong markup--h3-strong">Porque a engine V8 foi criada ?</strong> {#b998.graf.graf--h3.graf-after--li}

<p id="5b48" class="graf graf--p graf-after--h3">
  A engine V8 que é construída pelo Google é open source e escrito em C++. Essa engine é usada dentro do Google Chrome. Ao contrário do resto das engines, no entanto, a V8 é também usado pelo popular runtime do Node.js.
</p><figure id="9cdf" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/800/1*AKKvE3QmN_ZQmEzSj16oXg.png" alt="" data-image-id="1*AKKvE3QmN_ZQmEzSj16oXg.png" data-is-featured="true" />
</div></figure> 

<p id="5617" class="graf graf--p graf-after--figure">
  V8 foi primeiro projetado para aumentar a performance de execução do Javascript dentro de navegadores web. A fim de obter velocidade. Ele compila código Javascript em código de máquina ao invés de usar um interpretador. Ele compila o código Javascript em código de máquina na execução implementando um compilador JIT(Just-in-time) como várias engines Javascript modernas fazem, como SpiderMonkey ou Rhino(Mozila). A principal diferença aqui é que V8 não produz bytecode ou qualquer código intermediário.
</p>

### <strong class="markup--strong markup--h3-strong">V8 costumava ter dois compiladores</strong> {#d9d2.graf.graf--h3.graf-after--p}

<p id="e5e8" class="graf graf--p graf-after--h3">
  Antes da versão da 5.9 da V8 ser sair(foi lançada em 2017), a engine costumava ter dois compiladores:
</p>

<ul class="postList">
  <li id="fa3e" class="graf graf--li graf-after--p">
    Full-codepen — um compilador simples e muito rápido que produzia código de máquina simples e relativamente lento.
  </li>
  <li id="cc55" class="graf graf--li graf-after--li">
    CrankShaft — um compilador mais complexo(Just-in-time) que produzia um código altamente otimizado.
  </li>
</ul>

<p id="0fbc" class="graf graf--p graf-after--li">
  A engine V8 também usa váriAs segmentos(threads) internamente:
</p>

<ul class="postList">
  <li id="9c7d" class="graf graf--li graf-after--p">
    A principal threads faz o que espera: busca seu código, compila; e então executa.
  </li>
  <li id="2da0" class="graf graf--li graf-after--li">
    Também há uma thread separada para compilar, de modo que a thread principal pode se manter executando enquanto o código está sendo otimizado.
  </li>
  <li id="00eb" class="graf graf--li graf-after--li">
    Um Profiler thread que vai contar ao runtime quais métodos nós gastamos mais tempo para que o Crankshaft possa otimizá-lo.
  </li>
  <li id="4976" class="graf graf--li graf-after--li">
    Algumas thread para lidar com varreduras de garbage collection(coleta de lixo)
  </li>
</ul>

<p id="668a" class="graf graf--p graf-after--li">
  Ao executar pela primeira vez o código Javascript, V8 aproveita o <strong class="markup--strong markup--p-strong">full-codegen </strong>que traduz diretamente o Javascript parseado em código de máquina sem qualquer transformação. Isso permite que ele comece a executar o código de máquina muito rápido. Note que a V8 não usa representação bytecode intermediário, dessa maneira removendo a necessidade por um interpretador.
</p>

<p id="be80" class="graf graf--p graf-after--p">
  Quando o seu código está executando por algum tempo, o profile thread reuni dados o suficiente para contar que método deveria ser otimizado.
</p>

<p id="67e2" class="graf graf--p graf-after--p">
  Próximo, a otimização do <strong class="markup--strong markup--p-strong">CrankShaft </strong>começa em outra thread. Ele traduz a abstract syntax tree(árvore de sintaxe abstrata) Javascript para uma representação de atribuição estática única de alto nível(SSA em inglês) chamada <strong class="markup--strong markup--p-strong">Hydrogen </strong>e tenta otimizar esse gráfico Hydrogen. A maioria das otimizações são feitas nesse nível
</p>

### <strong class="markup--strong markup--h3-strong">Inlining</strong> {#0299.graf.graf--h3.graf-after--p}

<p id="1d6e" class="graf graf--p graf-after--h3">
  A primeira otimização é inserir o maior número possível de código com antecedência. Inlining é o processo de substituir uma call site(a linha de código onde a função é chamada) com o corpo da função chamada. Esse simples passo permite as otimizações a seguir serem mais significativas.
</p><figure id="f906" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/800/0*RRgTDdRfLGEhuR7U.png" alt="" data-image-id="0*RRgTDdRfLGEhuR7U.png" />
</div>

<div class="aspectRatioPlaceholder">
</div></figure> 

### <strong class="markup--strong markup--h3-strong">Classe oculta (Hidden class)</strong> {#3d5c.graf.graf--h3.graf-after--figure}

<p id="4a60" class="graf graf--p graf-after--h3">
  Javascript é uma linguagem baseada em protótipo(prototype-based language): <strong class="markup--strong markup--p-strong">não há classes</strong> e objetos são criados usando um processo de clonagem. Javascript é também um linguagem de programação dinâmica, o que significa que propriedades podem ser facilmente adicionadas ou removidas de um objeto depois de sua instanciação.
</p>

<p id="2325" class="graf graf--p graf-after--p">
  A maioria dos interpretadores Javascript usam dicionários como estruturas para armazenar a localização de valores de propriedades de objetos em memória. Essa estrutura faz a recuperação do valor de uma propriedade mais cara computacionalmente do que isso seria em linguagens de programação não dinâmicas como Java ou C#. Em Java, todos as propriedades de objetos são determinadas por um layout de objeto fio antes da compilação e não pode ser adicionado ou removido em tempo de execução (bem, C# tem o dynamic type que é outro tópico). Como um resultado, os valores de propriedades (ou ponteiros para essas propriedades) podem ser armazenados como um buffer contínuo na memória com um deslocamento fixo entre cada um deles. O tamanho de um deslocamento pode ser facilmente determinado baseado no tipo de propriedade, enquanto que isso não é possível em Javascript onde um tipo de propriedade pode mudar durante o tempo de execução.
</p>

<p id="5c19" class="graf graf--p graf-after--p">
  Desde que usando dicionários para encontrar a localização de propriedades de objetos na memória é muito ineficiente, V8 ao invés disso, usa um método diferente: <strong class="markup--strong markup--p-strong">Classe oculta</strong> <strong class="markup--strong markup--p-strong">(Hidden class). </strong>Classes ocultas funcionam similarmente a layouts de objetos (classes) fixos usados em linguagens como Java, exceto que eles são criados em tempo de execução. Agora vamos ver como eles realmente funcionam.
</p>

<pre id="19b9" class="graf graf--pre graf-after--p">function Point(x, y) {
    this.x = x;
    this.y = y;
}</pre>

<pre id="283c" class="graf graf--pre graf-after--pre">var p1 = new Point(1, 2);</pre>

<p id="b098" class="graf graf--p graf-after--pre">
  Uma vez que a invocação do “new Point(1,2)” acontece, V8 vai criar uma classe oculta chamada “C0”.
</p><figure id="5d10" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/800/1*pVnIrMZiB9iAz5sW28AixA.png" alt="" data-image-id="1*pVnIrMZiB9iAz5sW28AixA.png" />
</div></figure> 

<p id="ec5b" class="graf graf--p graf-after--figure">
  Nenhuma propriedade foi definida ainda para o Point, então “Co”está vazia.
</p>

<p id="c065" class="graf graf--p graf-after--p">
  Uma vez que a primeira declaração “this.x = x” é executada (dentro da função “Point”), V8 vai criar uma segunda classe oculta chamada “C1” que é baseada sobre “C0”. “C1” descreve a localização na memória(relativa ao objeto point) onde a propriedade X pode ser encontrada. Neste caso, “x” é armazenado no deslocamento 0, o que significa que ao visualizar objeto point na memória como um buffer contínuo, o primeiro deslocamento vai corresponder para a propriedade “x”. V8 vai também atualizar “C0” com uma “classe de transição” que declara se uma propriedade “x’ é adicionada para o objeto point, a classe oculta deveria trocar de “C0”para “C1”. A classe oculta para o objeto pint abaixo é agora “c1”.
</p><figure id="9e3b" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/800/1*QsVUE3snZD9abYXccg6Sgw.png" alt="" data-image-id="1*QsVUE3snZD9abYXccg6Sgw.png" />
</div><figcaption class="imageCaption">

<em class="markup--em markup--figure-em">Cada vez que uma nova propriedade é adicionada para um objeto, a antiga classe oculta é atualizada com um caminho de transição para a nova classe oculta. Transições de classe o oculta são importantes porque eles permitem que as classes ocultas sejam compartilhadas entre os objetos que são criados da mesma maneira. Se dois objetos compartilham a mesma classe oculta e a mesma propriedade é adicionada para ambos, transições vão assegurar que ambos os objetos recebem a mesma nova classe oculta e todo o código otimizado que vem junto com eles.</em></figcaption> </figure> 

<p id="6266" class="graf graf--p graf-after--figure">
  Esse processo é repetido quando a declaração “this.y = y”é executado (de novo, dentro da função Point depois da declaração “this.x = x”).
</p>

<p id="3a7e" class="graf graf--p graf-after--p">
  Uma nova classe oculta chamada “C2” é criada, uma classe de transição é adicionada para “C1” declarando que se uma propriedade ÿ” é adicionada para o objeto Point (que já contém a propriedade “x”) então a classe oculta deveria mudar para “C2”, e as classes ocultas dos objetos Point são atualizadas para “C2”.
</p><figure id="4bda" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/800/1*spJ8v7GWivxZZzTAzqVPtA.png" alt="" data-image-id="1*spJ8v7GWivxZZzTAzqVPtA.png" />
</div></figure> 

<p id="da80" class="graf graf--p graf-after--figure">
  Transições de classe oculta são dependentes da ordem nas quais as propriedades são adicionadas para um objeto. Dê uma olhada no trecho de código abaixo:
</p>

<pre class="html|php|js|css">function Point(x, y) {
this.x = x;
this.y = y;
}
var p1 = new Point(1, 2);
p1.a = 5;
p1.b = 6;
var p2 = new Point(3, 4);
p2.b = 7;
p2.a = 8;
</pre>

<p id="500b" class="graf graf--p graf-after--pre">
  Agora vamos assumir que para ambos p1 e p2 aa mesmas classes oculta e de transição seriam utilizadas. Bem, na verdade não. Para “p1”, primeiro a propriedade ä”vai ser adicionada e então a propriedade “b”. Para “p2”, no entanto, primeiro “b”está sendo atribuído, seguido por “a”. Portanto, “p1”e “p2”terminam com diferentes classes ocultas como um resultado de diferentes caminhos de transições. Em tais casos, é muito melhor inicializar propriedades dinâmicas na mesma ordem para que então as classes ocultas possam ser reutilizadas.
</p>

### <strong class="markup--strong markup--h3-strong">Inline Caching</strong> {#c23b.graf.graf--h3.graf-after--p}

<p id="c3b8" class="graf graf--p graf-after--h3">
  V8 toma vantagem de outra técnica para otimizar dinamicamente linguagens tipadas chamada <strong class="markup--strong markup--p-strong">inline caching. </strong>Inline caching depende de que repetidas chamadas para o mesmo método tendem a ocorrer para o mesmo tipo de objeto. Uma explicação em profundidade de Inline Caching pode ser encontrada <a class="markup--anchor markup--p-anchor" href="https://github.com/sq/JSIL/wiki/Optimizing-dynamic-JavaScript-with-inline-caches" target="_blank" rel="nofollow noopener" data-href="https://github.com/sq/JSIL/wiki/Optimizing-dynamic-JavaScript-with-inline-caches">aqui</a>.
</p>

<p id="1809" class="graf graf--p graf-after--p">
  Nós vamos abordar de forma geral o conceito inline cache (neste caso você não ter tempo de ir para a explicação aprofundada).
</p>

<p id="dc7d" class="graf graf--p graf-after--p">
  Então como isso funciona ? V8 mantém um cache do tipo de objetos que são passados como parâmetros em chamadas recentes de métodos e usa essa informação para fazer uma suposição sobre o tipo de objeto que vai ser passada como parâmetro no futuro. Se V8 está habilitado a fazer uma boa suposição sobre o tipo de objeto que vai ser passado para um método, ele pode ignorar o processo de como acessar as propriedades do objeto , e ao invés disso, usar a informação armazenada de pesquisas anteriores para a classe oculta do objeto.
</p>

<p id="e365" class="graf graf--p graf-after--p">
  Então como os conceitos de classe oculta e inline cache estão relacionados? Qualquer método que é chamado sobre um objeto específico, a engine V8 tem que realizar uma pesquisa para a classe oculta desse objeto a fim de determinar o deslocamento para acessar uma propriedade específica. Depois de chamadas bem sucedidas do mesmo método para a mesma classe oculta, V8 omite a pesquisa de classe oculta e simplesmente adiciona o deslocamento da propriedade do próprio objeto Point. Para todas as futuras chamadas desse método, a engine V8 supões que a classe oculta não mudou, e pula diretamente para o endereço de memória para uma específica propriedade usando o deslocamento armazenado das pesquisas anteriores. Isso aumenta muito a velocidade de execução.
</p>

<p id="f350" class="graf graf--p graf-after--p">
  Inline caching é também a razão porque é tão importante que objetos do mesmo tipo compartilhem classes ocultas. Se você cria dois objetos do mesmo tipo com diferentes classes ocultas (como nós vimos no exemplo anterior), V8 não vai estar habilitado a usar o inline caching porque mesmo os objetos sendo do mesmo tipo, suas correspondentes classes ocultas atribuem deslocamentos diferentes para suas propriedades.
</p><figure id="c8ab" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/800/1*iHfI6MQ-YKQvWvo51J-P0w.png" alt="" data-image-id="1*iHfI6MQ-YKQvWvo51J-P0w.png" />
</div><figcaption class="imageCaption">

<em class="markup--em markup--figure-em">Os dois objetos são basicamente os mesmos, mas as propriedades “a” e “b” são criadas em diferentes ordem.</em></figcaption> </figure> 

&nbsp;

### <strong class="markup--strong markup--h3-strong">Compilação para o código de máquina</strong> {#3557.graf.graf--h3.graf-after--figure}

<p id="b32a" class="graf graf--p graf-after--h3">
  Uma vez que o gráfico Hyfrogen está otimizado, Crankshaft baixa ele para uma representação baixo nível chamada Lithium. A maior parte da implementação do Lithium é arquitetura específica. Alocação de registros acontece nesse nível.
</p>

<p id="0c10" class="graf graf--p graf-after--p">
  No final, Lithium é compilado em código de máquina. Então alguma coisa mais acontece chamada OSR: on-stack replacement. Antes de começarmos a compilar e otimizar um método obviamente de longa duração, nós provavelmente estávamos executando ele. V8 não vai esquecer o que ele executou lentamente para começar de novo com a versão otimizada. Ao invés disso, ele vai transformar todo o contexto que nós temos (stack, registers) para que possamos trocar para a versão otimizada no meio da execução. Essa é uma tarefa muito complexa, tendo em mente que entre outras otimizações, V8 tem embutido o código inicialmente. V8 não é a única engine capaz de fazer isso.
</p>

<p id="797b" class="graf graf--p graf-after--p">
  Há garantias chamadas deoptimization para fazer a transformação oposta e reverter para o código não otimizado caso uma suposição da engine não seja mais verdadeira.
</p>

### <strong class="markup--strong markup--h3-strong">Garbage collection (Coleta de lixo)</strong> {#6db6.graf.graf--h3.graf-after--p}

<p id="529d" class="graf graf--p graf-after--h3">
  Para a garbage collection, V8 usa uma abordagem geracional tradicional de marca e varredura para limpar a velha geração. A fase de marcação deve interromper a execução do Javascript. A fim de controlar os custos de GC (Garbage Collection) e fazer a execução mais estável, V8 usa marcação incremental: ao invés de percorrer todo o heap, tentando marcar cada possível objeto, ele apenas marca parte do heap, e então volta para a execução normal. A próxima parada do GC ai continuar de onde o passo anterior do Heap parou. isso permite muitas pausas curtas durante a execução normal. Como mencionado antes, a fase de varredura é tratada por threads separadas.
</p>

### <strong class="markup--strong markup--h3-strong">Ignition e TurboFan</strong> {#94ba.graf.graf--h3.graf-after--p}

<p id="9645" class="graf graf--p graf-after--h3">
  Com a release da V8 5.9 no início de 2017, um novo pipeline de execução foi introduzido. Esse novo pipeline consegue até mais melhorias de performance e significativas economias de memória no <strong class="markup--strong markup--p-strong">mundo real</strong> das aplicações Javascript.
</p>

<p id="aaac" class="graf graf--p graf-after--p">
  O novo pipeline de execução é construído sobre o <a class="markup--anchor markup--p-anchor" href="https://github.com/v8/v8/wiki/Interpreter" target="_blank" rel="nofollow noopener" data-href="https://github.com/v8/v8/wiki/Interpreter">Ignition</a>, um interpretador da V8, e <a class="markup--anchor markup--p-anchor" href="https://github.com/v8/v8/wiki/TurboFan" target="_blank" rel="nofollow noopener" data-href="https://github.com/v8/v8/wiki/TurboFan">TurboFan</a>, o mais novo compilador de otimização da V8.
</p>

<p id="af1e" class="graf graf--p graf-after--p">
  Você pode verificar o post do time da V8 sobre esse tópico <a class="markup--anchor markup--p-anchor" href="https://v8project.blogspot.bg/2017/05/launching-ignition-and-turbofan.html" target="_blank" rel="nofollow noopener" data-href="https://v8project.blogspot.bg/2017/05/launching-ignition-and-turbofan.html">aqui</a>.
</p>

<p id="6526" class="graf graf--p graf-after--p">
  Desde que a versão 5.9 da V8 saiu, full-codegen e Crankshaft ( as tecnologias que tem servido V8 desde 2010) não tem sido mais utilizadas por V8 para a execução do Javascript pois o time da V8 tem lutado para acompanhar as novas funcionalidades da linguagem Javascript e as otimizações necessárias dessas funcionalidades.
</p>

<p id="d730" class="graf graf--p graf-after--p">
  Isso significa que de forma geral, a V8 vai ter uma arquitetura muito mais simples e de fácil manutenção no futuro.
</p><figure id="4aa6" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/800/0*pohqKvj9psTPRlOv.png" alt="" data-image-id="0*pohqKvj9psTPRlOv.png" />
</div></figure> 

<p id="4442" class="graf graf--p graf-after--figure">
  Essas melhorias são apenas o começo. Os novos pipelines ignition e TuboFan abrem o caminho para futuras otimizações que vão impulsionar a performance do Javascript e reduzirão o impacto do V8 no Chrome e no Node.js nos próximos anos.
</p>

<p id="aa08" class="graf graf--p graf-after--p">
  Finalmente, aqui estão algumas dicas de como escrever código bem otimizado, Javascript melhor. Você pode facilmente derivar elas do conteúdo acima, no entanto, aqui está um resumo para sua conveniência:
</p>

### <strong class="markup--strong markup--h3-strong">Como escrever Javascript otimizado</strong> {#8d64.graf.graf--h3.graf-after--p}

<ol class="postList">
  <li id="6950" class="graf graf--li graf-after--h3">
    <strong class="markup--strong markup--li-strong">Ordem das propriedades de objetos:</strong> sempre instancie suas propriedades de objetos na mesma ordem para que as classes ocultas e o subsequente código otimizado possa ser compartilhado.
  </li>
  <li id="ea41" class="graf graf--li graf-after--li">
    <strong class="markup--strong markup--li-strong">Propriedades dinâmicas:</strong> adicionando propriedades para um objeto depois da instanciação vai forçar uma classe oculta a mudar e desacelerar qualquer método que estava otimizado pela classe oculta anterior. Ao invés disso, atribua todas as propriedades de um objeto no seu construtor.
  </li>
  <li id="a46c" class="graf graf--li graf-after--li">
    <strong class="markup--strong markup--li-strong">Métodos:</strong> código que executa o mesmo método várias vezes vai executar mais rápido do que código que executa muitos métodos diferentes uma única vez (devido ao inline caching).
  </li>
  <li id="ba04" class="graf graf--li graf-after--li">
    <strong class="markup--strong markup--li-strong">Arrays:</strong> evite array esparsas onde as chaves não são números incrementais. Arrays esparsas que não possuem todos os elementos dentro delas são um hash.
  </li>
  <li id="29ad" class="graf graf--li graf-after--li">
    <strong class="markup--strong markup--li-strong">Valores marcados: </strong>V8 representa objetos e números com 32 bits. Ele usa um bit para saber se é um objeto (flag = 1) ou um inteiro (flag=0) chamado SMI (Small Integer) por causa de seus 31 bits. Então se valor numérico é maior que 31 bits, V8 vai marcar o número, transformando ele em um double e criando um novo objeto para colocar o número dentro. Tente usar números assinados de 31 bit sempre que possível para evitar marcações de alto custo dentro de um objeto JS.
  </li>
</ol>

<p id="7b89" class="graf graf--p graf-after--li">
  Nós na SessionStack tentamos seguir essas melhores práticas escrevendo código Javascript altamente otimizado. A razão é que uma vez que você integra SessionStack em sua web app de produção, ele começa a registrar todas as coisas: todas as mudanças no DOM, interações do usuário, exceptions Javascript, stack traces, requisições de rede que falharam e mensagens de debug.
</p>

<p id="b8cb" class="graf graf--p graf-after--p">
  Com SessionStack, você pode reproduzir problemas em suas web apps como videos e ver tudo que acontece para seu usuário. E tudo isso acontece sem nenhum impacto de performance para sua web app.
</p>

<p id="4a9e" class="graf graf--p graf-after--p">
  Há um plano grátis que permite que você comece <a class="markup--anchor markup--p-anchor" href="https://www.sessionstack.com/solutions/developers/?utm_source=medium&utm_medium=blog&utm_content=the-v8-engine" target="_blank" rel="nofollow noopener" data-href="https://www.sessionstack.com/solutions/developers/?utm_source=medium&utm_medium=blog&utm_content=the-v8-engine"><strong class="markup--strong markup--p-strong">de graça</strong></a><strong class="markup--strong markup--p-strong">.</strong>
</p><figure id="b5cb" class="graf graf--figure graf-after--p"> 

<div class="aspectRatioPlaceholder">
  <img class="graf-image aligncenter" src="https://cdn-images-1.medium.com/max/800/1*kEQmoMuNBDfZKNSBh0tvRA.png" alt="" data-image-id="1*kEQmoMuNBDfZKNSBh0tvRA.png" />
</div></figure> 

#### Referências {#d7e2.graf.graf--h4.graf-after--figure}

<ul class="postList">
  <li id="0b98" class="graf graf--li graf-after--h4">
    <a class="markup--anchor markup--li-anchor" href="https://docs.google.com/document/u/1/d/1hOaE7vbwdLLXWj3C8hTnnkpE0qSa2P--dtDvwXXEeD0/pub" target="_blank" rel="nofollow noopener" data-href="https://docs.google.com/document/u/1/d/1hOaE7vbwdLLXWj3C8hTnnkpE0qSa2P--dtDvwXXEeD0/pub">https://docs.google.com/document/u/1/d/1hOaE7vbwdLLXWj3C8hTnnkpE0qSa2P&#8211;dtDvwXXEeD0/pub</a>
  </li>
  <li id="236f" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="https://github.com/thlorenz/v8-perf" target="_blank" rel="nofollow noopener" data-href="https://github.com/thlorenz/v8-perf">https://github.com/thlorenz/v8-perf</a>
  </li>
  <li id="2a1e" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="http://code.google.com/p/v8/wiki/UsingGit" target="_blank" rel="nofollow noopener" data-href="http://code.google.com/p/v8/wiki/UsingGit">http://code.google.com/p/v8/wiki/UsingGit</a>
  </li>
  <li id="f106" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="http://mrale.ph/v8/resources.html" target="_blank" rel="nofollow noopener" data-href="http://mrale.ph/v8/resources.html">http://mrale.ph/v8/resources.html</a>
  </li>
  <li id="b6e9" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="https://www.youtube.com/watch?v=UJPdhx5zTaw" target="_blank" rel="nofollow noopener" data-href="https://www.youtube.com/watch?v=UJPdhx5zTaw">https://www.youtube.com/watch?v=UJPdhx5zTaw</a>
  </li>
  <li id="e839" class="graf graf--li graf-after--li">
    <a class="markup--anchor markup--li-anchor" href="https://www.youtube.com/watch?v=hWhMKalEicY" target="_blank" rel="nofollow noopener" data-href="https://www.youtube.com/watch?v=hWhMKalEicY">https://www.youtube.com/watch?v=hWhMKalEicY</a>
  </li>
</ul>

<p id="9968" class="graf graf--p graf-after--li">
  <em class="markup--em markup--p-em">Este é um artigo traduzido com a autorização do autor. O artigo original pode ser lido em </em><a class="markup--anchor markup--p-anchor" href="https://blog.sessionstack.com/how-javascript-works-inside-the-v8-engine-5-tips-on-how-to-write-optimized-code-ac089e62b12e" target="_blank" rel="nofollow noopener" data-href="https://blog.sessionstack.com/how-javascript-works-inside-the-v8-engine-5-tips-on-how-to-write-optimized-code-ac089e62b12e"><em class="markup--em markup--p-em">https://blog.sessionstack.com/how-javascript-works-inside-the-v8-engine-5-tips-on-how-to-write-optimized-code-ac089e62b12e</em></a>
</p>

<p id="6f1a" class="graf graf--p graf-after--p graf--trailing">
  <strong class="markup--strong markup--p-strong">Autor do post original — </strong>Alexander Zlatkov<strong class="markup--strong markup--p-strong"> </strong>— Co-founder & CEO <a class="markup--anchor markup--p-anchor" href="http://twitter.com/SessionStack" target="_blank" rel="nofollow noopener" data-href="http://twitter.com/SessionStack">@SessionStack</a>
</p>