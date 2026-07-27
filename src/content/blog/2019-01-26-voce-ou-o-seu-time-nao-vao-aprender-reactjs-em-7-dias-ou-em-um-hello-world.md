---
title: "Você ou o seu time não vão aprender ReactJs em 7 dias ou em um Hello World!"
description: "O objetivo desse artigo é posicionar o leitor de que desenvolver com ReactJs uma aplicação para rodar em produção de forma estável e evoluindo pode não ser tão simples como alguns artigos dão a impressão."
pubDate: 2019-01-26
tags: ["React","JavaScript","Frontend","Software Engineering"]
series: "Frontend"
language: "pt-BR"
---

![Imagem 1 do artigo](/blog/reactjs-7-dias-hello-world/image-01.png)

O objetivo desse artigo é posicionar o leitor de que desenvolver com ReactJs uma aplicação para rodar em produção de forma estável e evoluindo, pode não ser tão simples como alguns artigos podem dar a impressão.

Com toda a popularidade atual do ReactJs, Vue.js e Angular também vem um mar de informações, artigos e video aulas simplificando( na minha opinião) a sua curva de aprendizado. Esse artigo é baseado na minha experiência ao aprender ReactJs. Comecei a utilizar em setembro de 2017, bem no lançamento do **React 15.6 para 16.0** e algumas polêmicas de troca de licença de MIT para BSD, que depois de um barulho, voltou para MIT.

## Porque comecei a usar ReactJs ?

Procurando um software para fazer gestão Lean com OKR e Kanban em 2017 não achei nenhuma solução que resolvesse o problema que tínhamos na empresa, então resolvi criar uma solução. Cheguei a conclusão que a melhor abordagem seria criar uma PWA, e quis fazer o backend e o front-end com Javascript.

Parei na dúvida que acredito que todos ainda tem. **ReactJs, Vue.js **ou** Angular** ? Eu já conhecia o Angular 1.6 e o 2.0. Vue.js e ReactJS eu nunca havia testado, nem para brincar. O Angular 2.0 eu achei complexa a arquitetura e pouca documentação oficial, e havia uma certa tensão se o Google iria continuar com ela ou não, dado o imenso breaking change da versão 1.6 para 2.0, fiquei inseguro com isso. Então comecei a comparar Vue.js e ReactJs.

Acabei optando pelo ReacJs por 2 motivos principais: **comunidade **e a **arquitetura.**

### Comunidade

Espero não o ofender a comunidade do Vue.js que é tão grande quando a de ReactJs, mas na minha opinião é menos ativa na inserção de código para o repositório. Se olhar ambos os projetos no Github, tive em 2017 e continuo tendo agora as seguintes percepções:

- Ambos os projetos tem mais **120 mil starts, Vue.js **tem até mais que o ReactJs atualmente(em 2017 ainda não tinha), mas isso eu vejo apenas como admiradores do projeto. Eles consomem(talvez), mas não contribuem necessariamente.

- O ReactJs tem quase **5X mais contribuidores** que o Vue.js. Penso que isso faça uma diferença na longevidade da plataforma, mais do que o número de starts(admiradores)

- Claro que contribuidores não significa também atividade significativa, mas se olharmos no **Insights **de cada projeto e compararmos os itens **Pulse, Contributors, Commits **e **Code Frequence, **os números do ReactJs são mais elevados. Logo entendo que de fato tem mais gente melhorando e mantendo o ReactJs do que o Vue.js.

- Pelo tamanho do Facebook, que é quem criou e principal mantenedor do ReactJs vejo maior estabilidade no longo prazo. O ReactJs é utilizado amplamente dentro do Facebook, então eles tem muito a perder no caso da biblioteca deixar de existir, não que isso não possa acontecer, obviamente, mas eu pensei assim.

- Se você pesquisar no [Google Trends](https://trends.google.com/trends/explore?date=today%205-y&q=%2Fm%2F012l1vxv,%2Fm%2F0j45p7w,%2Fg%2F11c0vmgx5d) também se percebe que ReactJs está em maior ênfase.

- Gosto de ver também o [Stack Overflow Survey](https://insights.stackoverflow.com/survey/2018/#technology)

**Arquitetura**

Basicamente achei muito interessante o JSX, Virtual DOM e a liberdade ao se desenvolver com ReactJs. Sei que essa liberdade tem um preço, explico isso mais adiante.

Eu acredito que qualquer problema que se resolve com ReactJs, pode ser também resolvido com a mesma eficiência com o Vue.js ou com o Angular, só depende dos desenvolvedores dominarem a ferramenta e o problema que precisam resolver. Dito isso, também tem um lado emocional na minha escolha, apesar dos pontos acima citadas, eu **também **gostei mais de codificar com ReactJs e ponto final.

Mesmo assim recomendo ficar sempre atendo as melhorias do Vue.js e do Angular, pois se pode aprender bastante com elas.

## Você realmente precisa de ReactJS? Que problema ele resolve?

Como a própria descrição do ReactJs diz no GitHub ou no site oficial, ele é:

> Uma biblioteca JavaScript declarativa, eficiente e flexível para criar interfaces com o usuário.

E nada mais! Sim, e nada mais.

Se você tem um problema que pode se beneficiar do uso de SPA e desenvolvimento orientado a componentes e essa aplicação vai crescer no futuro, sua complexidade aumentar, ReactJs é um bom candidato para gerar e orquestrar esses componentes no meu entendimento.

Em outras palavras, ReactJs serve para criar interfaces visuais, você não vai criar uma aplicação completa com ele.

Aqui começa a minha insatisfação com o grande número de artigos que vejo do tipo: Aprenda ReactJs em 7 dias, em 5 minutos ou alguns que leio como aprender ReactJs nunca foi tão fácil. Depois de 14 meses desenvolvendo com ReactJs, esses artigos parecem subestimar a curva de aprendizado para se colocar uma aplicação decente para rodar em produção com um nível de qualidade aceitável.

Se você tem um problema que pode ser resolvido pelos Hello Worlds ou rápidos tutoriais, talvez você não precise de ReactJs, uma solução mais simples já resolva inicialmente.

Eu passei pelos Hello Worlds, pelos 5 minutos e pelos 7 dias. E depois disso ainda me senti na estaca zero. Então fico pensando, será que sou só eu ? Ou realmente não é tão simples assim ?

Eu trabalho com desenvolvimento de software a 10 anos. Logo quando vou construir uma aplicação me vem questões relacionadas ao **o que, como e por que** da implementação. Penso em como reusar melhor o código, penso em testes, em arquitetura de código, padrões de desenvolvimento, penso em como resolver o problema do presente, mas deixar opções abertas para a evolução.

Para todas essas questões eu tive dificuldade de encontrar documentação ou ajuda, e acredito que pode ter outras pessoas que tem também. Dificuldade pode não ser a palavra certa, mas dúvidas do tipo: Será que estou fazendo certo isso ? Faltou alguma coisa ?

## **Curva de aprend**izado

Abaixo vou listar os conceitos que acho importantes na hora de aprender ReactJS. Todos foram pontos de inflexão para mim, depois que compreendi, tudo fez mais sentido e meu código melhorou.

### **Javascript moderno — ES6/ES7/ES8/ES9 e por ai vai…**

Você pode desenvolver com ReactJs usando ES5, mas não sei porque alguém faria isso atualmente. Logo conhecimento sobre as últimas implementações do JS é muito importante. Imagine você ver o código abaixo, quando não é o seu dia a dia:

![Imagem 2 do artigo](/blog/reactjs-7-dias-hello-world/image-02.png)

Essa sintaxe é do Javascript ? É do ReactJs ? Ela está errada ? Que coisa é essa ?

Baseado nessas questões é importante conhecer o Javascript moderno, ele vai estar por toda a parte no ReactJs, nos tutoriais, na documentação. Eles já partem do princípio que você sabe isso. E no mínimo, penso eu, você deve saber o que é a linguagem e o que é a biblioteca que você está usando.

## JSX — Syntax extension to JavaScript

De forma bem resumida o JSX é uma sintaxe introduzida no ReactJs para representar como o componente deve parecer quando foi renderizado.

Veja o código abaixo:

```jsx
function todoList(){
    return <div>Task 1</div>
}
```

Esse código vai renderizar(tem mais código envolvido no processo obviamente) no navegador uma div com um texto "Task 1".

O JSX já foi motivo de muita crítica por "parecer"que está misturando HTML com Javascript. Mas aprofundando você entende o benefício e o porque de usar. [Aqui](https://reactjs.org/docs/introducing-jsx.html) tem a documentação oficial

### Virtual DOM

O virtual DOM é uma representação em memória do DOM que o ReactJs mantém. Quando há alguma mudança no DOM, o ReactJs utiliza um processo chamado [Reconciliação](https://reactjs.org/docs/reconciliation.html) para identificar o que mudou no DOM para fazer renderização incremental. Reduzindo os custos de render do navegador. Uma explicação completa pode ser vista [n](https://reactjs.org/docs/faq-internals.html)a [documentação oficial](https://reactjs.org/docs/faq-internals.html) e nesse [video](https://www.youtube.com/watch?v=ZCuYPiUIONs).

Se você pretende ir mais fundo na compreensão do ReactJs eu aconselho mesmo você baixar o código fonte e entender o que ela está fazendo. Pois são algorítimos e conceitos bem sofisticados que fazem o ReactJs ser como é.

### **Ciclo de vida dos componentes**

Esse é um assunto que sempre converso com o time, se você está utilizando qualquer biblioteca ou framework, procure entender o ciclo de vida dele. Sempre há um.

Entender o ciclo de vida de um componente ReactJs vai lhe permitir criar código mais otimizado e no mínimo vai evitar você fazer renderizações desnecessárias, melhorando significativamente a performance.

### Programação funcional

Tive certa dificuldade no início quando via alguns conceitos de programação funcional, por isso tive que aprofundar no assunto. Você não precisa ir fundo na programação funcional para aprender ReactJs, mas para desenvolver de forma mais elegante e simplificar seu código acredito que no mínimo deve estar a par de conceitos como **funções puras **e as funções básicas para lidar com arrays como **map, reduce **e **filter**. Entender **High-Order-Function **e** imutabilidade** também são importante.

### Gerenciamento de estado

Para mim, o que mais acrescentou como programador ao aprender a usar o ReactJs sem dúvida foi o conceito de gerenciamento do estado da aplicação. Estado é um termo e bem amplo quando falamos de aplicações WEB. Aplicações ReactJs tem componentes com estado local. O estado de forma simplista é basicamente um objeto atômico ou um agregado de objetos com a informação de tudo o que precisa viver e ser modificado no navegador.

Imagine um objeto JSON que representação todo o estado visual da sua aplicação. Por exemplo: se tem uma modal, ela pode estar sendo exibido ou não, isso seria um estado de um componente ou da aplicação. Como eu disse antes gerenciamento de estado é um termo amplo.

Há bibliotecas de gerenciamento de estado quase tão famosas quando o ReactJs como o caso do Redux e o MOBX, que podem ser utilizados mesmo sem o ReactJs. Essas bibliotecas nos ajudam a fazer do estado local um estado global. Essa é uma explicação bem pobre, mas o ponto de atenção é, estude **Gerenciamento de estado.**

Agora mesmo estamos na minha visão em um divisor de águas com a nova proposta do Facebook para gerenciar o estado e ciclos de vida dos componentes com **[Hooks](https://reactjs.org/docs/hooks-intro.html).**

### SPA — Single Page Application

Acredito que a essa altura você deve saber o que é uma SPA, mas vale ressaltar que ReactJs é voltado ao desenvolvimento de SPAs. Logo você deve ter ciência disso na hora de desenvolver. Sua aplicação vai ter, ou deveria pelo menos carregar "toda "em uma única página, ou pelo menos a parte crítica da sua aplicação, todo resto seria carregado assincronamente.

Deixo a descrição da [Wikipedia](https://en.wikipedia.org/wiki/Single-page_application)

**PWA — Progressive Web apps**

No meu caso quis fazer uma PWA com ReactJs. Para quem já desenvolveu uma PWA que atenda a todos os princípios, sabe que não é tão fácil assim também. Com ReactJs + PWA vem algumas considerações, Em que momento vou inicializar meus Service Workers? Como vou tratar os componentes de uma forma para atender ao Offline-first? Como vou estilizar para atender ao Critical Render Path e Instant Loading?

- Esse [artigo do Addy Osmani](https://medium.com/@addyosmani/progressive-web-apps-with-react-js-part-i-introduction-50679aef2b12) traz algumas ideias.

Também li esse livro:

- [Progressive Web Apps with ReactJs](https://www.amazon.com/Progressive-Web-Apps-React-lightning-ebook/dp/B076SZY9P9)

### Desenvolvimento orientado a componentes

ReactJs permite que você divida a UI em pedaços reutilizáveis que podem ser combinados para formar UIs mais complexas. Em outras palavras, permite que você crie componentes e combine-os para formar outros componentes.

O que é um componente ? Quão grande deve ser um componente até que eu precise dividi-lo? Ou quão pequeno deve ser?

Na [documentação oficial](https://reactjs.org/docs/thinking-in-react.html) tem uma explicação básica do assunto. Mas o importante é você aprender a pensar em componentes. E só um lembrete, Web Components não tem haver com ReactJs, esse também é outro assunto bem falado e pode gerar confusão.

### TDD — Test Driven Development

Hoje em dia sou meio radical nesse assunto. Penso que em 2018 ninguém pode começar um código ou projeto novo sem pensar em **testes** **primeiro. **É o melhor investimento a longo prazo para a qualidade da sua aplicação. Para fazer TDD com ReactJs foi algo bem diferente do que eu estava acostumado.

Testo apenas um componente ? Consigo testar de fim a fim? Devo testar as mudanças de estado? Como faço os mocks de componentes, apis, etc?

Esse assunto foi difícil achar documentação ou ajuda, o que fiz foi ficar pulando de tutorial em tutorial e alguns livros. Entre as principais ferramentas que utilizo hoje estão [Jest](https://jestjs.io/docs/en/tutorial-react), [Enzyme](https://github.com/airbnb/enzyme), [Chai](https://www.chaijs.com/) e [Mocha](https://mochajs.org/).

### Server-side renderer

Se você está desenvolvendo uma aplicação onde SEO é importante. Ou seja, as várias páginas da sua aplicação precisam ser indexadas por motores de busca(como o Google), você vai ter que utilizar a renderização pelo lado do servidor.

Como ReactJs é uma biblioteca Javascript executando no navegador, somente depois que todo o seu código for baixado do servidor, parseado e executado é que a sua aplicação realmente vai ganhar vida e aparência. Na perspectiva do robô do buscador, dependendo de como você programou, sua aplicação é apenas uma página em branco. E isso para o buscador tem zero relevância, você some do mapa.

Imagine passar um tempo desenvolvendo uma aplicação com ReactJs e quando ela vai ao ar o faturamento da empresa despenca porque você perdeu todo o tráfego orgânico que vinha do Google ?

[Server-side renderer](https://reactjs.org/docs/react-dom-server.html) é processo que permite você gerar no backend os componentes. É como uma típica página HTML sendo carregada, até que o Javascript será todo seja carregado ela continua sem interatividade, mas o conteúdo já está disponível e pode ser indexado pelos motores de busca.

Para mim o server-side render também tem um ganho de perfomance percebida por parte do usuário.

### Static Type Checker

Esse já um conceito um pouco mais avançado. Javascript é uma linguagem dinâmica e fracamente "tipada"(acho que essa palavra não existe, mas enfim). Ela também faz a coerção de tipo automaticamente.

Por essas questões as vezes conforme o código vai ficando maior, pode ser difícil achar gargalos de performance ou refatorar. Para ajudar nisso o Facebook criou o [Flow](https://flow.org). Ele permite que você defina os tipos de variáveis, objetos, funções e tipos customizados. Seu código fica mais legível e menos propenso a bugs por coerção de tipo.

Uma alternativa ao Flow é utilizar Typescript para desenvolver aplicações ReactJs. Eu gosto bastante de Typescript, quase utilizei no projeto. Só não fiz para experimentar algo diferente como o Flow e também para ficar mais próximo da implementação oficial do ReactJs.

### Saiba depurar a sua aplicação

Depurar aplicações ReactJs é um pouco chato, mas depois que você se acostuma com as ferramentas fica fácil. Eu basicamente utilizo o React Developer tools, Redux Devtools e o debugger do Visual Studio Code junto com o Chrome.

Aprender a utilizar bem essas ferramentas vai ser de grande ajuda. E não se esqueça do Chrome Devtools.

### Ecossistema ReactJs

Lembra que eu disse que ReactJS serve apenas para criar interfaces visuais ? Pois é, e como que fica o resto, pois uma aplicação robusta pode ter:

- Uma forma de fazer roteamento entre as páginas

- Uma forma de fazer requisições HTTP

- Como posso dar suporte a outros idiomas na minha aplicação

Nada dessas questões ReactJs responde, você tem a liberdade de fazer como quiser. E é ai que os problemas começam. Você tem que se virar para achar alguma outra biblioteca que resolva o seu problema ou criar você mesmo.

O problema nesse caso é que você pode acabar utilizando uma biblioteca que pode não funcionar no futuro, já que o ReactJs evoluí sem se importar com esses terceiros.

Algumas da bibliotecas mais populares que compões o ecossistema ReactJs são:

- [React Router](https://github.com/ReactTraining/react-router) — para fazer roteamento entre componentes e páginas principalmente.

- [Axios](https://github.com/axios/axios) — para tratar as requisições HTTP.

- [Apollo client](https://github.com/apollographql/apollo-client) — Pode ser que você não utilize no backend uma API REST ou simples retornos de chamadas AJAX. Mas caso já esteja mais fundo no ReactJs e conheça Graphqll. O Apollo é client é uma boa.

- [Reacti18next](https://github.com/i18next/react-i18next) — Caso precise utilizar outros idiomas na sua aplicação.

Citei apenas alguns exemplo, mas o fato que você deve gravar é que ReactJs só vai gerar seus componentes visuais, todo o resto que você precisar você vai ter que sair pesquisando, analisando a maturidade da dependência e arriscar, basicamente é isso.

### Webpack

Ao desenvolver uma aplicação ReactJs você provavelmente vai utilizar recursos que não funcionam em navegadores sem passar por um processo de transpilação do código que você escreveu para o código que o navegador entende. Exemplos disso são o JSX, funcionalidades mais recentes do Javascript entre outros. Geralmente isso vai ser feito por um **Task-runner** ou **bundler. **Webpack é os dois.

Apesar de ser uma ferramenta a parte, entendo como um peça fundamental do seu ecossistema de desenvolvimento. A [documentação oficial](https://survivejs.com/webpack/what-is-webpack/) é bem vasta.

### Padrões de desenvolvimento de código (Styles guides)

Defina de preferência antes de começar os padrões de codificação que você ou seu time vão seguir. Se possível vincule esses padrões a obrigações em um pipeline de integração contínua, assim desenvolvedores fora do padrão não conseguem colocar isso em produção.

Eu comecei pelos padrões abaixo que são do AirBnb:

- [AirBnb Code style ReactJs](https://github.com/airbnb/javascript/tree/master/react)

- [AirBnb Code Style Javascript](https://github.com/airbnb/javascript)

- [AirBnb Code Style Css in JS](https://github.com/airbnb/javascript/tree/master/css-in-javascript)

Depois de um tempo centralizei toda essa configuração através do [Prettier](https://prettier.io/), achei mais simples.

Esses padrões estão configurados para a IDE que utilizo e também no [TravisCI](https://travis-ci.org/), [Code Climate](https://codeclimate.com/) e [Codacy](https://www.codacy.com/) e são validados a cada commit que é feito para o repositório.

### Como arquitetar tudo isso junto ?

Esse foi para mim outra grande dúvida. Como que eu organizo meu código e mais importante, meus componentes. Mesmo que minha aplicação tenha centenas ou milhares de componentes no futuro, como faço para a sua complexidade e modularidade se manter flexível?

ReactJs também não responde isso.

O Facebook criou uma ferramenta de boilerplate para criar um template padrão de aplicação. É chamado [create-react-app](https://github.com/facebook/create-react-app). Eu não utilizei porque quando comecei o projeto ela não suportava server-side renderer. Outro motivo que fez eu não utilizar é que era muito "caixa-preta" para mim, eu queria entender de fato como se monta um ambiente de desenvolvimento com ReactJs.

Basicamente o create-react-app gera todos os arquivos e diretórios para você começar a desenvolver sua aplicação. Inclusive configura o ambiente para rodar testes e linters para validação de sintaxe.

Sobre como estruturar um projeto, essas foram as questões importantes para mim:

- Saiba o que são componentes **statefull** e **stateless**

- Estude um pouco dos principais design patterns usado em aplicações ReactJs. No mínimo saiba o que são [container components e presentational components](https://medium.com/@dan_abramov/smart-and-dumb-components-7ca2f9a7c7d0).

- Sem tratamento devido uma aplicação ReactJs é um único arquivo Javascript que sua página tem que carregar. Logo quanto maior a sua aplicação fica, esse arquivo também. Imagina daqui a 2 anos sua aplicação com 5000 componentes e 10MB para ser carregado e executado. Para isso tem [code-split e lazy loading](https://reactjs.org/docs/code-splitting.html) que servem para dividir o seu código em partes menores e carregar cada parte somente quando ela é realmente necessário. Isso é fundamental para que sua aplicação consiga manter a performance enquanto cresce.

Eu costumo ler bastante sobre programação em livros. Os livros abaixo ajudaram muito na hora de definir a estrutura da aplicação:

- [React Design Patterns and Best Practices](https://www.amazon.com.br/React-Design-Patterns-Best-Practices-ebook/dp/B01LFAN88E)

- [FullStack React](https://www.fullstackreact.com/)

- [React in Action](https://www.manning.com/books/react-in-action)

- [React Quickly](https://www.manning.com/books/react-quickly)

Aqui tem 3 links que acho bons para tomar uma decisão de como estruturar seus projetos:

[How to Structure Your React Project](https://daveceddia.com/react-project-structure/)

_When you first run create-react-app, you'll end up with a folder like this: All of the code you write will live under…daveceddia.com_

[The 100% correct way to structure a React app (or why there’s no such thing)](https://hackernoon.com/the-100-correct-way-to-structure-a-react-app-or-why-theres-no-such-thing-3ede534ef1ed)

_When it comes to structuring a React app, the ideal structure is the one that allows you to move around your code with…hackernoon.com_

Obs.: Não se deixe levar pela "maneira 100% correta", mas é um excelente artigo.

[How to better organize your React applications?](https://medium.com/@alexmngn/how-to-better-organize-your-react-applications-2fd3ea1920f1)

_I’ve been working on very large web applications for the past few years, starting from ground zero and, with a dozen…medium.com_

## Esteja atento a comunidade!

Software bom é software atualizado, quanto mais próximos dos últimos releases você estiver eu considero melhor. Então fique atendo as [melhorias e breaking changes](https://github.com/facebook/react/releases) do repositório oficial.

Você vai ter que usar bibliotecas de terceiros que estão fora do ReactJs mas dentro do **Ecossistema. **Fique atendo a essas mudanças também e se elas estão conforme com as do repositório oficial.

Minha principal dica qualquer que seja o seu nível de experiência é, participe do **[Grupo no Slack React Brasil ](https://react-brasil-slack.herokuapp.com/). **São quase 5000 mil pessoas trocando informação o tempo todo, dá para ficar o dia todo conectado e você não consegue acompanhar. Tem conversas para quem nunca ouviu falar de ReactJs até o nível expert, ninja, master e etc(rsrs).

[Join React Brasil on Slack!](https://react-brasil-slack.herokuapp.com/)

_Edit descriptionreact-brasil-slack.herokuapp.com_

## Conclusão

O post ficou longo e pode ter dado a impressão que quero representar que desenvolver com ReactJs é super complicado e você não deve fazer isso. Não é essa a intenção, só que quando vamos desenvolver uma aplicação real que precise gerar valor para quem vai utilizar, o grande número de tutoriais que tem não ajudam muito. na realidade, no "como fazer" nem o meu post ajuda.

Só quis deixar aqui alguns tópicos que eu só percebi que precisava aprender quando aparecia um obstáculo no projeto. Você não precisa dominar esses tópicos, mas acho que pelo menos precisa saber que eles existem.

Tenho ouvido pessoas dizendo que foi erro inserir ReactJs no projeto X ou Y. Mas eles subestimaram a curva de aprendizado. Saíram de algo que conheciam muito e foram para um novo conceito achando que continuaria o mesmo ritmo. Toda a mudança tem um gap de performance no início. Ë isso que quis apontar nesse post.

Pretendo escrever outros artigos mostrando desde como montei o ambiente de desenvolvimento para ReactJs até a arquitetura do projeto e alguns pontos de refatoração importantes que passei.
