---
id: 1088
title: Você ou o seu time não vão aprender ReactJs em 7 dias ou em um Hello World!
date: 2019-02-19T15:23:01+00:00
author: Robisson Oliveira
layout: post
guid: https://www.robissonoliveira.com.br/?p=1088
permalink: /desenvolvimento-de-software/voce-ou-o-seu-time-nao-vao-aprender-reactjs-em-7-dias-ou-em-um-hello-world
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
<section class="section section--body section--first"> 

<div class="section-content">
  <div class="section-inner sectionLayout--insetColumn">
    <p id="ae12" class="graf graf--p graf-after--figure">
      O objetivo desse artigo é posicionar o leitor de que desenvolver com ReactJs uma aplicação para rodar em produção de forma estável e evoluindo, pode não ser tão simples como alguns artigos podem dar a impressão.
    </p>
    
    <p id="fc80" class="graf graf--p graf-after--p">
      Com toda a popularidade atual do ReactJs, Vue.js e Angular também vem um mar de informações, artigos e video aulas simplificando( na minha opinião) a sua curva de aprendizado. Esse artigo é baseado na minha experiência ao aprender ReactJs. Comecei a utilizar em setembro de 2017, bem no lançamento do <strong class="markup--strong markup--p-strong">React 15.6 para 16.0</strong> e algumas polêmicas de troca de licença de MIT para BSD, que depois de um barulho, voltou para MIT.
    </p>
    
    <h3 id="a5be" class="graf graf--h3 graf-after--p">
      Porque comecei a usar ReactJs ?
    </h3>
    
    <p id="77c3" class="graf graf--p graf-after--h3">
      Procurando um software para fazer gestão Lean com OKR e Kanban em 2017 não achei nenhuma solução que resolvesse o problema que tínhamos na empresa, então resolvi criar uma solução. Cheguei a conclusão que a melhor abordagem seria criar uma PWA, e quis fazer o backend e o front-end com Javascript.
    </p>
    
    <p id="7c68" class="graf graf--p graf-after--p">
      Parei na dúvida que acredito que todos ainda tem. <strong class="markup--strong markup--p-strong">ReactJs, Vue.js </strong>ou<strong class="markup--strong markup--p-strong">Angular</strong> ? Eu já conhecia o Angular 1.6 e o 2.0. Vue.js e ReactJS eu nunca havia testado, nem para brincar. O Angular 2.0 eu achei complexa a arquitetura e pouca documentação oficial, e havia uma certa tensão se o Google iria continuar com ela ou não, dado o imenso breaking change da versão 1.6 para 2.0, fiquei inseguro com isso. Então comecei a comparar Vue.js e ReactJs.
    </p>
    
    <p id="5b02" class="graf graf--p graf-after--p">
      Acabei optando pelo ReacJs por 2 motivos principais: <strong class="markup--strong markup--p-strong">comunidade </strong>e a <strong class="markup--strong markup--p-strong">arquitetura.</strong>
    </p>
    
    <h4 class="graf graf--h4 graf-after--p">
    </h4>
    
    <h4 id="2a43" class="graf graf--h4 graf-after--p">
      Comunidade
    </h4>
    
    <p id="6e79" class="graf graf--p graf-after--h4">
      Espero não o ofender a comunidade do Vue.js que é tão grande quando a de ReactJs, mas na minha opinião é menos ativa na inserção de código para o repositório. Se olhar ambos os projetos no Github, tive em 2017 e continuo tendo agora as seguintes percepções:
    </p>
    
    <ul class="postList">
      <li id="acba" class="graf graf--li graf-after--p">
        Ambos os projetos tem mais <strong class="markup--strong markup--li-strong">120 mil starts, Vue.js </strong>tem até mais que o ReactJs atualmente(em 2017 ainda não tinha), mas isso eu vejo apenas como admiradores do projeto. Eles consomem(talvez), mas não contribuem necessariamente.
      </li>
      <li id="db5b" class="graf graf--li graf-after--li">
        O ReactJs tem quase <strong class="markup--strong markup--li-strong">5X mais contribuidores</strong> que o Vue.js. Penso que isso faça uma diferença na longevidade da plataforma, mais do que o número de starts(admiradores)
      </li>
      <li id="1846" class="graf graf--li graf-after--li">
        Claro que contribuidores não significa também atividade significativa, mas se olharmos no <strong class="markup--strong markup--li-strong">Insights </strong>de cada projeto e compararmos os itens <strong class="markup--strong markup--li-strong">Pulse, Contributors, Commits </strong>e <strong class="markup--strong markup--li-strong">Code Frequence, </strong>os números do ReactJs são mais elevados. Logo entendo que de fato tem mais gente melhorando e mantendo o ReactJs do que o Vue.js.
      </li>
      <li id="5e8c" class="graf graf--li graf-after--li">
        Pelo tamanho do Facebook, que é quem criou e principal mantenedor do ReactJs vejo maior estabilidade no longo prazo. O ReactJs é utilizado amplamente dentro do Facebook, então eles tem muito a perder no caso da biblioteca deixar de existir, não que isso não possa acontecer, obviamente, mas eu pensei assim.
      </li>
      <li id="1566" class="graf graf--li graf-after--li">
        Se você pesquisar no <a class="markup--anchor markup--li-anchor" href="https://trends.google.com/trends/explore?date=today%205-y&q=%2Fm%2F012l1vxv,%2Fm%2F0j45p7w,%2Fg%2F11c0vmgx5d" target="_blank" rel="nofollow noopener" data-href="https://trends.google.com/trends/explore?date=today%205-y&q=%2Fm%2F012l1vxv,%2Fm%2F0j45p7w,%2Fg%2F11c0vmgx5d">Google Trends</a> também se percebe que ReactJs está em maior ênfase.
      </li>
      <li id="408b" class="graf graf--li graf-after--li">
        Gosto de ver também o <a class="markup--anchor markup--li-anchor" href="https://insights.stackoverflow.com/survey/2018/#technology" target="_blank" rel="nofollow noopener" data-href="https://insights.stackoverflow.com/survey/2018/#technology">Stack Overflow Survey</a>
      </li>
    </ul>
    
    <p id="24da" class="graf graf--p graf-after--li">
      <strong class="markup--strong markup--p-strong">Arquitetura</strong>
    </p>
    
    <p id="1769" class="graf graf--p graf-after--p">
      Basicamente achei muito interessante o JSX, Virtual DOM e a liberdade ao se desenvolver com ReactJs. Sei que essa liberdade tem um preço, explico isso mais adiante.
    </p>
    
    <p id="1616" class="graf graf--p graf-after--p">
      Eu acredito que qualquer problema que se resolve com ReactJs, pode ser também resolvido com a mesma eficiência com o Vue.js ou com o Angular, só depende dos desenvolvedores dominarem a ferramenta e o problema que precisam resolver. Dito isso, também tem um lado emocional na minha escolha, apesar dos pontos acima citadas, eu <strong class="markup--strong markup--p-strong">também </strong>gostei mais de codificar com ReactJs e ponto final.
    </p>
    
    <p id="a1c6" class="graf graf--p graf-after--p graf--trailing">
      Mesmo assim recomendo ficar sempre atendo as melhorias do Vue.js e do Angular, pois se pode aprender bastante com elas.
    </p>
  </div>
</div></section> 

<!--more--><section class="section section--body section--last"> 

<div class="section-divider">
  <hr class="section-divider" />
</div>

<div class="section-content">
  <div class="section-inner sectionLayout--insetColumn">
    <h3 class="graf graf--h3 graf--leading">
    </h3>
    
    <h3 id="018a" class="graf graf--h3 graf--leading">
      Você realmente precisa de ReactJS? Que problema ele resolve?
    </h3>
    
    <p id="ea97" class="graf graf--p graf-after--h3">
      Como a própria descrição do ReactJs diz no GitHub ou no site oficial, ele é:
    </p>
    
    <blockquote id="b93c" class="graf graf--blockquote graf-after--p">
      <p>
        Uma biblioteca JavaScript declarativa, eficiente e flexível para criar interfaces com o usuário.
      </p>
    </blockquote>
    
    <p id="fae9" class="graf graf--p graf-after--blockquote">
      E nada mais! Sim, e nada mais.
    </p>
    
    <p id="001f" class="graf graf--p graf-after--p">
      Se <span class="markup--quote markup--p-quote is-other" data-creator-ids="306bf8b2286">você</span> tem um problema que pode se beneficiar do uso de SPA e desenvolvimento orientado a componentes e essa aplicação vai crescer no futuro, sua complexidade aumentar, ReactJs é um bom candidato para gerar e orquestrar esses componentes no meu entendimento.
    </p>
    
    <p id="4676" class="graf graf--p graf-after--p">
      Em outras palavras, ReactJs serve para criar interfaces visuais, você não vai criar uma aplicação completa com ele.
    </p>
    
    <p id="1704" class="graf graf--p graf-after--p">
      Aqui começa a minha insatisfação com o grande número de artigos que vejo do tipo: Aprenda ReactJs em 7 dias, em 5 minutos ou alguns que leio como aprender ReactJs nunca foi tão fácil. Depois de 14 meses desenvolvendo com ReactJs, esses artigos parecem subestimar a curva de aprendizado para se colocar uma aplicação decente para rodar em produção com um nível de qualidade aceitável.
    </p>
    
    <p id="d62a" class="graf graf--p graf-after--p">
      <span class="markup--quote markup--p-quote is-other" data-creator-ids="26663559fbc2">Se você tem um problema que pode ser resolvido pelos Hello Worlds ou rápidos tutoriais, talvez você não precise de ReactJs, uma solução mais simples já resolva inicialmente.</span>
    </p>
    
    <p id="6ad6" class="graf graf--p graf-after--p">
      Eu passei pelos Hello Worlds, pelos 5 minutos e pelos 7 dias. E depois disso ainda me senti na estaca zero. Então fico pensando, será que sou só eu ? Ou realmente não é tão simples assim ?
    </p>
    
    <p id="b6fe" class="graf graf--p graf-after--p">
      Eu trabalho com desenvolvimento de software a 10 anos. Logo quando vou construir uma aplicação me vem questões relacionadas ao <strong class="markup--strong markup--p-strong">o que, como e por que</strong> da implementação. Penso em como reusar melhor o código, penso em testes, em arquitetura de código, padrões de desenvolvimento, penso em como resolver o problema do presente, mas deixar opções abertas para a evolução.
    </p>
    
    <p id="4e86" class="graf graf--p graf-after--p">
      Para todas essas questões eu tive dificuldade de encontrar documentação ou ajuda, e acredito que pode ter outras pessoas que tem também. Dificuldade pode não ser a palavra certa, mas dúvidas do tipo: Será que estou fazendo certo isso ? Faltou alguma coisa ?
    </p>
    
    <h2 id="74f2" class="graf graf--h3 graf-after--p">
      <strong class="markup--strong markup--h3-strong">Curva de aprend</strong>izado
    </h2>
    
    <p id="ae73" class="graf graf--p graf-after--h3">
      Abaixo vou listar os conceitos que acho importantes na hora de aprender ReactJS. Todos foram pontos de inflexão para mim, depois que compreendi, tudo fez mais sentido e meu código melhorou.
    </p>
    
    <h3 class="graf graf--h4 graf-after--p">
    </h3>
    
    <h3 id="9e68" class="graf graf--h4 graf-after--p">
      <strong class="markup--strong markup--h4-strong">Javascript moderno — ES6/ES7/ES8/ES9 e por ai vai…</strong>
    </h3>
    
    <p id="0eb5" class="graf graf--p graf-after--h4">
      Você pode desenvolver com ReactJs usando ES5, mas não sei porque alguém faria isso atualmente. Logo conhecimento sobre as últimas implementações do JS é muito importante. Imagine você ver o código abaixo, quando não é o seu dia a dia:
    </p><figure id="c515" class="graf graf--figure graf-after--p"> 
    
    <div class="aspectRatioPlaceholder is-locked">
      <div class="aspectRatioPlaceholder-fill">
      </div>
      
      <div class="progressiveMedia js-progressiveMedia graf-image is-canvasLoaded is-imageLoaded" data-image-id="1*OZZfO8mr4kptAqP2u4BWrg.png" data-width="560" data-height="192" data-scroll="native">
        <canvas class="progressiveMedia-canvas js-progressiveMedia-canvas" width="75" height="25"></canvas><img class="progressiveMedia-image js-progressiveMedia-image" src="https://cdn-images-1.medium.com/max/800/1*OZZfO8mr4kptAqP2u4BWrg.png" alt="" data-src="https://cdn-images-1.medium.com/max/800/1*OZZfO8mr4kptAqP2u4BWrg.png" />
      </div>
    </div></figure> 
    
    <p id="e817" class="graf graf--p graf-after--figure">
      Essa sintaxe é do Javascript ? É do ReactJs ? Ela está errada ? Que coisa é essa ?
    </p>
    
    <p id="7e01" class="graf graf--p graf-after--p">
      <span class="markup--quote markup--p-quote is-other" data-creator-ids="4f0bde3b006f">Baseado nessas questões é importante conhecer o Javascript moderno, ele vai estar por toda a parte no ReactJs, nos tutoriais, na documentação</span>. Eles já partem do princípio que você sabe isso. E no mínimo, penso eu, você deve saber o que é a linguagem e o que é a biblioteca que você está usando.
    </p>
    
    <h3 id="55a9" class="graf graf--h3 graf-after--p">
      JSX — Syntax extension to JavaScript
    </h3>
    
    <p id="edef" class="graf graf--p graf-after--h3">
      De forma bem resumida o JSX é uma sintaxe introduzida no ReactJs para representar como o componente deve parecer quando foi renderizado.
    </p>
    
    <p id="787c" class="graf graf--p graf-after--p">
      Veja o código abaixo:
    </p>
    
    <pre id="9b95" class="graf graf--pre graf-after--p">function todoList(){
    return &lt;div&gt;Task 1&lt;/div&gt;
}</pre>
    
    <p id="baeb" class="graf graf--p graf-after--pre">
      Esse código vai renderizar(tem mais código envolvido no processo obviamente) no navegador uma div com um texto &#8220;Task 1&#8221;.
    </p>
    
    <p id="eb1e" class="graf graf--p graf-after--p">
      O JSX já foi motivo de muita crítica por &#8220;parecer&#8221;que está misturando HTML com Javascript. Mas aprofundando você entende o benefício e o porque de usar. <a class="markup--anchor markup--p-anchor" href="https://reactjs.org/docs/introducing-jsx.html" target="_blank" rel="nofollow noopener" data-href="https://reactjs.org/docs/introducing-jsx.html">Aqui</a> tem a documentação oficial
    </p>
    
    <h4 id="28f6" class="graf graf--h4 graf-after--p">
      Virtual DOM
    </h4>
    
    <p id="1bd6" class="graf graf--p graf-after--h4">
      O virtual DOM é uma representação em memória do DOM que o ReactJs mantém. Quando há alguma mudança no DOM, o ReactJs utiliza um processo chamado <a class="markup--anchor markup--p-anchor" href="https://reactjs.org/docs/reconciliation.html" target="_blank" rel="nofollow noopener" data-href="https://reactjs.org/docs/reconciliation.html">Reconciliação</a> para identificar o que mudou no DOM para fazer renderização incremental. Reduzindo os custos de render do navegador. Uma explicação completa pode ser vista <a class="markup--anchor markup--p-anchor" href="https://reactjs.org/docs/faq-internals.html" target="_blank" rel="nofollow noopener" data-href="https://reactjs.org/docs/faq-internals.html">n</a>a <a class="markup--anchor markup--p-anchor" href="https://reactjs.org/docs/faq-internals.html" target="_blank" rel="nofollow noopener" data-href="https://reactjs.org/docs/faq-internals.html">documentação oficial</a> e nesse <a class="markup--anchor markup--p-anchor" href="https://www.youtube.com/watch?v=ZCuYPiUIONs" target="_blank" rel="nofollow noopener" data-href="https://www.youtube.com/watch?v=ZCuYPiUIONs">video</a>.
    </p>
    
    <p id="b6b4" class="graf graf--p graf-after--p">
      Se você pretende ir mais fundo na compreensão do ReactJs eu aconselho mesmo você baixar o código fonte e entender o que ela está fazendo. Pois são algorítimos e conceitos bem sofisticados que fazem o ReactJs ser como é.
    </p>
    
    <h4 id="8910" class="graf graf--h4 graf-after--p">
      <strong class="markup--strong markup--h4-strong">Ciclo de vida dos componentes</strong>
    </h4>
    
    <p id="27be" class="graf graf--p graf-after--h4">
      Esse é um assunto que sempre converso com o time, se você está utilizando qualquer biblioteca ou framework, procure entender o ciclo de vida dele. Sempre há um.
    </p>
    
    <p id="9cd1" class="graf graf--p graf-after--p">
      Entender o ciclo de vida de um componente ReactJs vai lhe permitir criar código mais otimizado e no mínimo vai evitar você fazer renderizações desnecessárias, melhorando significativamente a performance.
    </p>
    
    <h4 id="a944" class="graf graf--h4 graf-after--p">
      Programação funcional
    </h4>
    
    <p id="4084" class="graf graf--p graf-after--h4">
      Tive certa dificuldade no início quando via alguns <span class="markup--quote markup--p-quote is-other" data-creator-ids="4f0bde3b006f">conceitos de programação funcional</span>, por isso tive que aprofundar no assunto. Você não precisa ir fundo na programação funcional para aprender ReactJs, mas para desenvolver de forma mais elegante e simplificar seu código acredito que no mínimo deve estar a par de conceitos como <strong class="markup--strong markup--p-strong">funções puras </strong>e as funções básicas para lidar com arrays como <strong class="markup--strong markup--p-strong">map, reduce </strong>e <strong class="markup--strong markup--p-strong">filter</strong>. Entender <strong class="markup--strong markup--p-strong">High-Order-Function </strong>e<strong class="markup--strong markup--p-strong">imutabilidade</strong> também são importante.
    </p>
    
    <h3 id="c99c" class="graf graf--h4 graf-after--p">
      Gerenciamento de estado
    </h3>
    
    <p id="cdde" class="graf graf--p graf-after--h4">
      Para mim, o que mais acrescentou como programador ao aprender a usar o ReactJs sem dúvida foi o conceito de gerenciamento do estado da aplicação. Estado é um termo e bem amplo quando falamos de aplicações WEB. Aplicações ReactJs tem componentes com estado local. O estado de forma simplista é basicamente um objeto atômico ou um agregado de objetos com a informação de tudo o que precisa viver e ser modificado no navegador.
    </p>
    
    <p id="b723" class="graf graf--p graf-after--p">
      Imagine um objeto JSON que representação todo o estado visual da sua aplicação. Por exemplo: se tem uma modal, ela pode estar sendo exibido ou não, isso seria um estado de um componente ou da aplicação. Como eu disse antes gerenciamento de estado é um termo amplo.
    </p>
    
    <p id="4277" class="graf graf--p graf-after--p">
      Há bibliotecas de gerenciamento de estado quase tão famosas quando o ReactJs como o caso do Redux e o MOBX, que podem ser utilizados mesmo sem o ReactJs. Essas bibliotecas nos ajudam a fazer do estado local um estado global. Essa é uma explicação bem pobre, mas o ponto de atenção é, estude <strong class="markup--strong markup--p-strong">Gerenciamento de estado.</strong>
    </p>
    
    <p id="d7b8" class="graf graf--p graf-after--p">
      Agora mesmo estamos na minha visão em um divisor de águas com a nova proposta do Facebook para gerenciar o estado e ciclos de vida dos componentes com <a class="markup--anchor markup--p-anchor" href="https://reactjs.org/docs/hooks-intro.html" target="_blank" rel="nofollow noopener" data-href="https://reactjs.org/docs/hooks-intro.html"><strong class="markup--strong markup--p-strong">Hooks</strong></a><strong class="markup--strong markup--p-strong">.</strong>
    </p>
    
    <h3 id="dcb5" class="graf graf--h4 graf-after--p">
      SPA — Single Page Application
    </h3>
    
    <p id="c9ce" class="graf graf--p graf-after--h4">
      Acredito que a essa altura você deve saber o que é uma SPA, mas vale ressaltar que ReactJs é voltado ao desenvolvimento de SPAs. Logo você deve ter ciência disso na hora de desenvolver. Sua aplicação vai ter, ou deveria pelo menos carregar &#8220;toda &#8220;em uma única página, ou pelo menos a parte crítica da sua aplicação, todo resto seria carregado assincronamente.
    </p>
    
    <p id="abf1" class="graf graf--p graf-after--p">
      Deixo a descrição da <a class="markup--anchor markup--p-anchor" href="https://en.wikipedia.org/wiki/Single-page_application" target="_blank" rel="nofollow noopener" data-href="https://en.wikipedia.org/wiki/Single-page_application">Wikipedia</a>
    </p>
    
    <h3 id="38cc" class="graf graf--p graf-after--p">
      <strong class="markup--strong markup--p-strong">PWA — Progressive Web apps</strong>
    </h3>
    
    <p id="8d38" class="graf graf--p graf-after--p">
      No meu caso quis fazer uma PWA com ReactJs. Para quem já desenvolveu uma PWA que atenda a todos os princípios, sabe que não é tão fácil assim também. Com ReactJs + PWA vem algumas considerações, Em que momento vou inicializar meus Service Workers? Como vou tratar os componentes de uma forma para atender ao Offline-first? Como vou estilizar para atender ao Critical Render Path e Instant Loading?
    </p>
    
    <ul class="postList">
      <li id="d6b3" class="graf graf--li graf-after--p">
        Esse <a class="markup--anchor markup--li-anchor" href="https://medium.com/@addyosmani/progressive-web-apps-with-react-js-part-i-introduction-50679aef2b12" target="_blank" data-href="https://medium.com/@addyosmani/progressive-web-apps-with-react-js-part-i-introduction-50679aef2b12">artigo do Addy Osmani</a> traz algumas ideias.
      </li>
    </ul>
    
    <p id="f1f0" class="graf graf--p graf-after--li">
      Também li esse livro:
    </p>
    
    <ul class="postList">
      <li id="2329" class="graf graf--li graf-after--p">
        <a class="markup--anchor markup--li-anchor" href="https://www.amazon.com/Progressive-Web-Apps-React-lightning-ebook/dp/B076SZY9P9" target="_blank" rel="nofollow noopener" data-href="https://www.amazon.com/Progressive-Web-Apps-React-lightning-ebook/dp/B076SZY9P9">Progressive Web Apps with ReactJs</a>
      </li>
    </ul>
    
    <h3 id="cda2" class="graf graf--h4 graf-after--li">
      Desenvolvimento orientado a componentes
    </h3>
    
    <p id="885e" class="graf graf--p graf-after--h4">
      ReactJs permite que você divida a UI em pedaços reutilizáveis que podem ser combinados para formar UIs mais complexas. Em outras palavras, permite que você crie componentes e combine-os para formar outros componentes.
    </p>
    
    <p id="2bf0" class="graf graf--p graf-after--p">
      O que é um componente ? Quão grande deve ser um componente até que eu precise dividi-lo? Ou quão pequeno deve ser?
    </p>
    
    <p id="d282" class="graf graf--p graf-after--p">
      Na <a class="markup--anchor markup--p-anchor" href="https://reactjs.org/docs/thinking-in-react.html" target="_blank" rel="nofollow noopener" data-href="https://reactjs.org/docs/thinking-in-react.html">documentação oficial</a> tem uma explicação básica do assunto. Mas o importante é você aprender a pensar em componentes. E só um lembrete, Web Components não tem haver com ReactJs, esse também é outro assunto bem falado e pode gerar confusão.
    </p>
    
    <h3 id="99c8" class="graf graf--h4 graf-after--p">
      TDD — Test Driven Development
    </h3>
    
    <p id="9d74" class="graf graf--p graf-after--h4">
      Hoje em dia sou meio radical nesse assunto. Penso que em 2018 ninguém pode começar um código ou projeto novo sem pensar em <strong class="markup--strong markup--p-strong">testes</strong> <strong class="markup--strong markup--p-strong">primeiro. </strong>É o melhor investimento a longo prazo para a qualidade da sua aplicação. Para fazer TDD com ReactJs foi algo bem diferente do que eu estava acostumado.
    </p>
    
    <p id="4a34" class="graf graf--p graf-after--p">
      Testo apenas um componente ? Consigo testar de fim a fim? Devo testar as mudanças de estado? Como faço os mocks de componentes, apis, etc?
    </p>
    
    <p id="52ac" class="graf graf--p graf-after--p">
      Esse assunto foi difícil achar documentação ou ajuda, o que fiz foi ficar pulando de tutorial em tutorial e alguns livros. Entre as principais ferramentas que utilizo hoje estão <a class="markup--anchor markup--p-anchor" href="https://jestjs.io/docs/en/tutorial-react" target="_blank" rel="nofollow noopener" data-href="https://jestjs.io/docs/en/tutorial-react">Jest</a>, <a class="markup--anchor markup--p-anchor" href="https://github.com/airbnb/enzyme" target="_blank" rel="nofollow noopener" data-href="https://github.com/airbnb/enzyme">Enzyme</a>, <a class="markup--anchor markup--p-anchor" href="https://www.chaijs.com/" target="_blank" rel="nofollow noopener" data-href="https://www.chaijs.com/">Chai</a> e <a class="markup--anchor markup--p-anchor" href="https://mochajs.org/" target="_blank" rel="nofollow noopener" data-href="https://mochajs.org/">Mocha</a>.
    </p>
    
    <h3 id="4ecf" class="graf graf--h4 graf-after--p">
      Server-side renderer
    </h3>
    
    <p id="b668" class="graf graf--p graf-after--h4">
      Se você está desenvolvendo uma aplicação onde SEO é importante. Ou seja, as várias páginas da sua aplicação precisam ser indexadas por motores de busca(como o Google), você vai ter que utilizar a renderização pelo lado do servidor.
    </p>
    
    <p id="7100" class="graf graf--p graf-after--p">
      Como ReactJs é uma biblioteca Javascript executando no navegador, somente depois que todo o seu código for baixado do servidor, parseado e executado é que a sua aplicação realmente vai ganhar vida e aparência. Na perspectiva do robô do buscador, dependendo de como você programou, sua aplicação é apenas uma página em branco. E isso para o buscador tem zero relevância, você some do mapa.
    </p>
    
    <p id="ea8e" class="graf graf--p graf-after--p">
      Imagine passar um tempo desenvolvendo uma aplicação com ReactJs e quando ela vai ao ar o faturamento da empresa despenca <span class="markup--quote markup--p-quote is-other" data-creator-ids="4f0bde3b006f">porque você perdeu todo o tráfego orgânico que vinha do Google ?</span>
    </p>
    
    <p id="bacb" class="graf graf--p graf-after--p">
      <a class="markup--anchor markup--p-anchor" href="https://reactjs.org/docs/react-dom-server.html" target="_blank" rel="nofollow noopener" data-href="https://reactjs.org/docs/react-dom-server.html">Server-side renderer</a> é processo que permite você gerar no backend os componentes. É como uma típica página HTML sendo carregada, até que o Javascript será todo seja carregado ela continua sem interatividade, mas o conteúdo já está disponível e pode ser indexado pelos motores de busca.
    </p>
    
    <p id="9558" class="graf graf--p graf-after--p">
      Para mim o server-side render também tem um ganho de perfomance percebida por parte do usuário.
    </p>
    
    <h3 id="22f7" class="graf graf--h4 graf-after--p">
      Static Type Checker
    </h3>
    
    <p id="2d76" class="graf graf--p graf-after--h4">
      Esse já um conceito um pouco mais avançado. Javascript é uma linguagem dinâmica e fracamente &#8220;tipada&#8221;(acho que essa palavra não existe, mas enfim). Ela também faz a coerção de tipo automaticamente.
    </p>
    
    <p id="dabe" class="graf graf--p graf-after--p">
      Por essas questões as vezes conforme o código vai ficando maior, pode ser difícil achar gargalos de performance ou refatorar. Para ajudar nisso o Facebook criou o <a class="markup--anchor markup--p-anchor" href="https://flow.org/" target="_blank" rel="nofollow noopener" data-href="https://flow.org">Flow</a>. Ele permite que você defina os tipos de variáveis, objetos, funções e tipos customizados. Seu código fica mais legível e menos propenso a bugs por coerção de tipo.
    </p>
    
    <p id="fced" class="graf graf--p graf-after--p">
      Uma alternativa ao Flow é utilizar Typescript para desenvolver aplicações ReactJs. Eu gosto bastante de Typescript, quase utilizei no projeto. Só não fiz para experimentar algo diferente como o Flow e também para ficar mais próximo da implementação oficial do ReactJs.
    </p>
    
    <h3 id="bdd0" class="graf graf--h4 graf-after--p">
      Saiba depurar a sua aplicação
    </h3>
    
    <p id="7869" class="graf graf--p graf-after--h4">
      Depurar aplicações ReactJs é um pouco chato, mas depois que você se acostuma com as ferramentas fica fácil. Eu basicamente utilizo o React Developer tools, Redux Devtools e o debugger do Visual Studio Code junto com o Chrome.
    </p>
    
    <p id="894e" class="graf graf--p graf-after--p">
      Aprender a utilizar bem essas ferramentas vai ser de grande ajuda. E não se esqueça do Chrome Devtools.
    </p>
    
    <h3 id="f341" class="graf graf--h4 graf-after--p">
      Ecossistema ReactJs
    </h3>
    
    <p id="c0a6" class="graf graf--p graf-after--h4">
      Lembra que eu disse que ReactJS serve apenas para criar interfaces visuais ? Pois é, e como que fica o resto, pois uma aplicação robusta pode ter:
    </p>
    
    <ul class="postList">
      <li id="e75b" class="graf graf--li graf-after--p">
        Uma forma de fazer roteamento entre as páginas
      </li>
      <li id="1027" class="graf graf--li graf-after--li">
        Uma forma de fazer requisições HTTP
      </li>
      <li id="0cb2" class="graf graf--li graf-after--li">
        Como posso dar suporte a outros idiomas na minha aplicação
      </li>
    </ul>
    
    <p id="1940" class="graf graf--p graf-after--li">
      Nada dessas questões ReactJs responde, você tem a liberdade de fazer como quiser. E é ai que os problemas começam. Você tem que se virar para achar alguma outra biblioteca que resolva o seu problema ou criar você mesmo.
    </p>
    
    <p id="78f0" class="graf graf--p graf-after--p">
      <span class="markup--quote markup--p-quote is-other" data-creator-ids="4f0bde3b006f">O problema nesse caso é que você pode acabar utilizando uma biblioteca que pode não funcionar no futuro, já que o ReactJs evoluí sem se importar com esses terceiros.</span>
    </p>
    
    <p id="46c5" class="graf graf--p graf-after--p">
      Algumas da bibliotecas mais populares que compões o ecossistema ReactJs são:
    </p>
    
    <ul class="postList">
      <li id="9ce5" class="graf graf--li graf-after--p">
        <a class="markup--anchor markup--li-anchor" href="https://github.com/ReactTraining/react-router" target="_blank" rel="nofollow noopener" data-href="https://github.com/ReactTraining/react-router">React Router</a> — para fazer roteamento entre componentes e páginas principalmente.
      </li>
      <li id="72ae" class="graf graf--li graf-after--li">
        <a class="markup--anchor markup--li-anchor" href="https://github.com/axios/axios" target="_blank" rel="nofollow noopener" data-href="https://github.com/axios/axios">Axios</a> — para tratar as requisições HTTP.
      </li>
      <li id="4597" class="graf graf--li graf-after--li">
        <a class="markup--anchor markup--li-anchor" href="https://github.com/apollographql/apollo-client" target="_blank" rel="nofollow noopener" data-href="https://github.com/apollographql/apollo-client">Apollo client</a> — Pode ser que você não utilize no backend uma API REST ou simples retornos de chamadas AJAX. Mas caso já esteja mais fundo no ReactJs e conheça Graphqll. O Apollo é client é uma boa.
      </li>
      <li id="6d30" class="graf graf--li graf-after--li">
        <a class="markup--anchor markup--li-anchor" href="https://github.com/i18next/react-i18next" target="_blank" rel="nofollow noopener" data-href="https://github.com/i18next/react-i18next">Reacti18next</a> — Caso precise utilizar outros idiomas na sua aplicação.
      </li>
    </ul>
    
    <p id="79bd" class="graf graf--p graf-after--li">
      Citei apenas alguns exemplo, mas o fato que você deve gravar é que ReactJs só vai gerar seus componentes visuais, todo o resto que você precisar você vai ter que sair pesquisando, analisando a maturidade da dependência e arriscar, basicamente é isso.
    </p>
    
    <h3 id="c81e" class="graf graf--h4 graf-after--p">
      Webpack
    </h3>
    
    <p id="e5a2" class="graf graf--p graf-after--h4">
      Ao desenvolver uma aplicação ReactJs você provavelmente vai utilizar recursos que não funcionam em navegadores sem passar por um processo de transpilação do código que você escreveu para o código que o navegador entende. Exemplos disso são o JSX, funcionalidades mais recentes do Javascript entre outros. Geralmente isso vai ser feito por um <strong class="markup--strong markup--p-strong">Task-runner</strong> ou <strong class="markup--strong markup--p-strong">bundler. </strong>Webpack é os dois.
    </p>
    
    <p id="00e3" class="graf graf--p graf-after--p">
      Apesar de ser uma ferramenta a parte, entendo como um peça fundamental do seu ecossistema de desenvolvimento. A <a class="markup--anchor markup--p-anchor" href="https://survivejs.com/webpack/what-is-webpack/" target="_blank" rel="nofollow noopener" data-href="https://survivejs.com/webpack/what-is-webpack/">documentação oficial</a> é bem vasta.
    </p>
    
    <h3 id="d76a" class="graf graf--h4 graf-after--p">
      Padrões de desenvolvimento de código (Styles guides)
    </h3>
    
    <p id="62fc" class="graf graf--p graf-after--h4">
      Defina de preferência antes de começar os padrões de codificação que você ou seu time vão seguir. Se possível vincule esses padrões a obrigações em um pipeline de integração contínua, assim desenvolvedores fora do padrão não conseguem colocar isso em produção.
    </p>
    
    <p id="19b9" class="graf graf--p graf-after--p">
      Eu comecei pelos padrões abaixo que são do AirBnb:
    </p>
    
    <ul class="postList">
      <li id="d4f2" class="graf graf--li graf-after--p">
        <a class="markup--anchor markup--li-anchor" href="https://github.com/airbnb/javascript/tree/master/react" target="_blank" rel="nofollow noopener" data-href="https://github.com/airbnb/javascript/tree/master/react">AirBnb Code style ReactJs</a>
      </li>
      <li id="29cb" class="graf graf--li graf-after--li">
        <a class="markup--anchor markup--li-anchor" href="https://github.com/airbnb/javascript" target="_blank" rel="nofollow noopener" data-href="https://github.com/airbnb/javascript">AirBnb Code Style Javascript</a>
      </li>
      <li id="f528" class="graf graf--li graf-after--li">
        <a class="markup--anchor markup--li-anchor" href="https://github.com/airbnb/javascript/tree/master/css-in-javascript" target="_blank" rel="nofollow noopener" data-href="https://github.com/airbnb/javascript/tree/master/css-in-javascript">AirBnb Code Style Css in JS</a>
      </li>
    </ul>
    
    <p id="29c4" class="graf graf--p graf-after--li">
      Depois de um tempo centralizei toda essa configuração através do <a class="markup--anchor markup--p-anchor" href="https://prettier.io/" target="_blank" rel="noopener nofollow" data-href="https://prettier.io/">Prettier</a>, achei mais simples.
    </p>
    
    <p id="bd03" class="graf graf--p graf-after--p">
      Esses padrões estão configurados para a IDE que utilizo e também no <a class="markup--anchor markup--p-anchor" href="https://travis-ci.org/" target="_blank" rel="nofollow noopener noopener" data-href="https://travis-ci.org/">TravisCI</a>, <a class="markup--anchor markup--p-anchor" href="https://codeclimate.com/" target="_blank" rel="nofollow noopener noopener" data-href="https://codeclimate.com/">Code Climate</a> e <a class="markup--anchor markup--p-anchor" href="https://www.codacy.com/" target="_blank" rel="nofollow noopener noopener" data-href="https://www.codacy.com/">Codacy</a> <span class="markup--quote markup--p-quote is-other" data-creator-ids="4f0bde3b006f">e são validados a cada commit que é feito para o repositório.</span>
    </p>
    
    <h3 id="da77" class="graf graf--h4 graf-after--p">
      Como arquitetar tudo isso junto ?
    </h3>
    
    <p id="b24b" class="graf graf--p graf-after--h4">
      Esse foi para mim outra grande dúvida. Como que eu organizo meu código e mais importante, meus componentes. Mesmo que minha aplicação tenha centenas ou milhares de componentes no futuro, como faço para a sua complexidade e modularidade se manter flexível?
    </p>
    
    <p id="2266" class="graf graf--p graf-after--p">
      ReactJs também não responde isso.
    </p>
    
    <p id="f74e" class="graf graf--p graf-after--p">
      O Facebook criou uma ferramenta de boilerplate para criar um template padrão de aplicação. É chamado <a class="markup--anchor markup--p-anchor" href="https://github.com/facebook/create-react-app" target="_blank" rel="nofollow noopener" data-href="https://github.com/facebook/create-react-app">create-react-app</a>. Eu não utilizei porque quando comecei o projeto ela não suportava server-side renderer. Outro motivo que fez eu não utilizar é que era muito &#8220;caixa-preta&#8221; para mim, eu queria entender de fato como se monta um ambiente de desenvolvimento com ReactJs.
    </p>
    
    <p id="9f46" class="graf graf--p graf-after--p">
      Basicamente o create-react-app gera todos os arquivos e diretórios para você começar a desenvolver sua aplicação. Inclusive configura o ambiente para rodar testes e linters para validação de sintaxe.
    </p>
    
    <p id="ef3e" class="graf graf--p graf-after--p">
      Sobre como estruturar um projeto, essas foram as questões importantes para mim:
    </p>
    
    <ul class="postList">
      <li id="8495" class="graf graf--li graf-after--p">
        Saiba o que são componentes <strong class="markup--strong markup--li-strong">statefull</strong> e <strong class="markup--strong markup--li-strong">stateless</strong>
      </li>
      <li id="2e65" class="graf graf--li graf-after--li">
        Estude um pouco dos principais design patterns usado em aplicações ReactJs. No mínimo saiba o que são <a class="markup--anchor markup--li-anchor" href="https://medium.com/@dan_abramov/smart-and-dumb-components-7ca2f9a7c7d0" target="_blank" data-href="https://medium.com/@dan_abramov/smart-and-dumb-components-7ca2f9a7c7d0">container components e presentational components</a>.
      </li>
      <li id="b41c" class="graf graf--li graf-after--li">
        Sem tratamento devido uma aplicação ReactJs é um único arquivo Javascript que sua página tem que carregar. Logo quanto maior a sua aplicação fica, esse arquivo também. Imagina daqui a 2 anos sua aplicação com 5000 componentes e 10MB para ser carregado e executado. Para isso tem <a class="markup--anchor markup--li-anchor" href="https://reactjs.org/docs/code-splitting.html" target="_blank" rel="nofollow noopener" data-href="https://reactjs.org/docs/code-splitting.html">code-split e lazy loading</a> que servem para dividir o seu código em partes menores e carregar cada parte somente quando ela é realmente necessário. Isso é fundamental para que sua aplicação consiga manter a performance enquanto cresce.
      </li>
    </ul>
    
    <p id="ef5f" class="graf graf--p graf-after--li">
      Eu costumo ler bastante sobre programação em livros. Os livros abaixo ajudaram muito na hora de <span class="markup--quote markup--p-quote is-other" data-creator-ids="952bd0eb2593">definir</span> a estrutura da aplicação:
    </p>
    
    <ul class="postList">
      <li id="2516" class="graf graf--li graf-after--p">
        <a class="markup--anchor markup--li-anchor" href="https://www.amazon.com.br/React-Design-Patterns-Best-Practices-ebook/dp/B01LFAN88E" target="_blank" rel="nofollow noopener" data-href="https://www.amazon.com.br/React-Design-Patterns-Best-Practices-ebook/dp/B01LFAN88E">React Design Patterns and Best Practices</a>
      </li>
      <li id="f01b" class="graf graf--li graf-after--li">
        <a class="markup--anchor markup--li-anchor" href="https://www.fullstackreact.com/" target="_blank" rel="nofollow noopener" data-href="https://www.fullstackreact.com/">FullStack React</a>
      </li>
      <li id="c0d0" class="graf graf--li graf-after--li">
        <a class="markup--anchor markup--li-anchor" href="https://www.manning.com/books/react-in-action" target="_blank" rel="nofollow noopener" data-href="https://www.manning.com/books/react-in-action">React in Action</a>
      </li>
      <li id="03dd" class="graf graf--li graf-after--li">
        <a class="markup--anchor markup--li-anchor" href="https://www.manning.com/books/react-quickly" target="_blank" rel="nofollow noopener" data-href="https://www.manning.com/books/react-quickly">React Quickly</a>
      </li>
    </ul>
    
    <p id="089e" class="graf graf--p graf-after--li">
      Aqui tem 3 links que acho bons para tomar uma decisão de como estruturar seus projetos:
    </p>
    
    <ul>
      <li id="0e31" class="graf graf--mixtapeEmbed graf-after--p">
        <a class="markup--anchor markup--mixtapeEmbed-anchor" title="https://daveceddia.com/react-project-structure/" href="https://daveceddia.com/react-project-structure/" rel="nofollow" data-href="https://daveceddia.com/react-project-structure/"><strong class="markup--strong markup--mixtapeEmbed-strong">How to Structure Your React Project</strong><br /> <em class="markup--em markup--mixtapeEmbed-em">When you first run create-react-app, you&#8217;ll end up with a folder like this: All of the code you write will live under…</em>daveceddia.com</a>
      </li>
      <li id="6a95" class="graf graf--mixtapeEmbed graf-after--mixtapeEmbed">
        <a class="markup--anchor markup--mixtapeEmbed-anchor" title="https://hackernoon.com/the-100-correct-way-to-structure-a-react-app-or-why-theres-no-such-thing-3ede534ef1ed" href="https://hackernoon.com/the-100-correct-way-to-structure-a-react-app-or-why-theres-no-such-thing-3ede534ef1ed" rel="nofollow" data-href="https://hackernoon.com/the-100-correct-way-to-structure-a-react-app-or-why-theres-no-such-thing-3ede534ef1ed"><strong class="markup--strong markup--mixtapeEmbed-strong">The 100% correct way to structure a React app (or why there’s no such thing)</strong><br /> <em class="markup--em markup--mixtapeEmbed-em">When it comes to structuring a React app, the ideal structure is the one that allows you to move around your code with…</em>hackernoon.com</a>
      </li>
    </ul>
    
    <p id="e4c3" class="graf graf--p graf-after--mixtapeEmbed">
      Obs.: Não se deixe levar pela &#8220;maneira 100% correta&#8221;, mas é um excelente artigo.
    </p>
    
    <ul>
      <li id="070d" class="graf graf--mixtapeEmbed graf-after--p">
        <a class="markup--anchor markup--mixtapeEmbed-anchor" title="https://medium.com/@alexmngn/how-to-better-organize-your-react-applications-2fd3ea1920f1" href="https://medium.com/@alexmngn/how-to-better-organize-your-react-applications-2fd3ea1920f1" data-href="https://medium.com/@alexmngn/how-to-better-organize-your-react-applications-2fd3ea1920f1"><strong class="markup--strong markup--mixtapeEmbed-strong">How to better organize your React applications?</strong><br /> <em class="markup--em markup--mixtapeEmbed-em">I’ve been working on very large web applications for the past few years, starting from ground zero and, with a dozen…</em>medium.com</a>
      </li>
    </ul>
    
    <h3 id="310e" class="graf graf--h3 graf-after--mixtapeEmbed">
      Esteja atento a comunidade!
    </h3>
    
    <p id="e29c" class="graf graf--p graf-after--h3">
      Software bom é software atualizado, quanto mais próximos dos últimos releases você estiver eu considero melhor. Então fique atendo as <a class="markup--anchor markup--p-anchor" href="https://github.com/facebook/react/releases" target="_blank" rel="nofollow noopener" data-href="https://github.com/facebook/react/releases">melhorias e breaking changes</a> do repositório oficial.
    </p>
    
    <p id="b017" class="graf graf--p graf-after--p">
      Você vai ter que usar bibliotecas de terceiros que estão fora do ReactJs mas dentro do <strong class="markup--strong markup--p-strong">Ecossistema. </strong>Fique atendo a essas mudanças também e se elas estão conforme com as do repositório oficial.
    </p>
    
    <p id="aa87" class="graf graf--p graf-after--p">
      Minha principal dica qualquer que seja o seu nível de experiência é, participe do <a class="markup--anchor markup--p-anchor" href="https://react-brasil-slack.herokuapp.com/" target="_blank" rel="nofollow noopener" data-href="https://react-brasil-slack.herokuapp.com/"><strong class="markup--strong markup--p-strong">Grupo no Slack React Brasil </strong></a><strong class="markup--strong markup--p-strong">. </strong>São quase 5000 mil pessoas trocando informação o tempo todo, dá para ficar o dia todo conectado e você não consegue acompanhar. Tem conversas para quem nunca ouviu falar de ReactJs até o nível expert, ninja, master e etc(rsrs).
    </p>
    
    <ul>
      <li id="8c88" class="graf graf--mixtapeEmbed graf-after--p">
        <a class="markup--anchor markup--mixtapeEmbed-anchor" title="https://react-brasil-slack.herokuapp.com/" href="https://react-brasil-slack.herokuapp.com/" rel="nofollow" data-href="https://react-brasil-slack.herokuapp.com/"><strong class="markup--strong markup--mixtapeEmbed-strong">Join React Brasil on Slack!</strong><br /> <em class="markup--em markup--mixtapeEmbed-em">Edit description</em>react-brasil-slack.herokuapp.com</a>
      </li>
    </ul>
    
    <h2 class="graf graf--h3 graf-after--mixtapeEmbed">
    </h2>
    
    <h2 id="b2cf" class="graf graf--h3 graf-after--mixtapeEmbed">
      Conclusão
    </h2>
    
    <p id="2e55" class="graf graf--p graf-after--h3">
      O post ficou longo e pode ter dado a impressão que quero representar que desenvolver com ReactJs é super complicado e você não deve fazer isso. Não é essa a intenção, só que quando vamos desenvolver uma aplicação real que precise gerar valor para quem vai utilizar, o grande número de tutoriais que tem não ajudam muito. na realidade, no &#8220;como fazer&#8221; nem o meu post ajuda.
    </p>
    
    <p id="5610" class="graf graf--p graf-after--p">
      Só quis deixar aqui alguns tópicos que eu só percebi que precisava aprender quando aparecia um obstáculo no projeto. Você não precisa dominar esses tópicos, mas acho que pelo menos precisa saber que eles existem.
    </p>
    
    <p id="3000" class="graf graf--p graf-after--p">
      Tenho ouvido pessoas dizendo que foi erro inserir ReactJs no projeto X ou Y. Mas eles subestimaram a curva de aprendizado. Saíram de algo que conheciam muito e foram para um novo conceito achando que continuaria o mesmo ritmo. Toda a mudança tem um gap de performance no início. Ë isso que quis apontar nesse post.
    </p>
    
    <p id="2524" class="graf graf--p graf-after--p graf--trailing">
      Pretendo escrever outros artigos mostrando desde como montei o ambiente de desenvolvimento para ReactJs até a arquitetura do projeto e alguns pontos de refatoração importantes que passei.
    </p>
  </div>
</div></section>