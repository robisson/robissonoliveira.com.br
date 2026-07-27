---
title: "Como o Javascript funciona: entendendo as funções e suas formas de uso"
seoTitle: "Como funções JavaScript funcionam"
description: "Vamos ver o que são, como definir e invocar funções de diferentes formas."
pubDate: 2019-03-11
tags: ["JavaScript","Frontend","Programming","Functions"]
series: "Frontend"
language: "pt-BR"
---

![Imagem 1 do artigo](/blog/javascript-funcoes/image-01.png)

Eu venho traduzindo há algumas semanas a série de artigos do Alexander Zlatkov [**como o Javascript funciona**](https://medium.com/reactbrasil/como-o-javascript-funciona-uma-vis%C3%A3o-geral-da-engine-runtime-e-da-call-stack-471dd5e1aa30). São todos artigos de qualidade altíssima. Entre os artigos dele não tem um para falar das funções em Javascript, pelo menos não ainda, então esse eu resolvi escrever.

Vou partir do principio que você conhece o básico sobre funções, como defini-las e utilizá-las, e a partir desse ponto vamos aprofundar nos conceitos.

Podemos dizer que Javascript é uma linguagem funcional. Tudo com exceção do código de execução global, é executado dentro de uma função. Mas uma função em Javascript é um objeto. Confuso? Não se preocupe, até o final do texto isso vai ficar mais claro.

### Definindo rapidamente funções em Javascript

Segundo a descrição da [mozilla.org](http://mozilla.org/):

Funções são blocos de construção fundamentais em JavaScript. Uma função é um procedimento de JavaScript — um conjunto de instruções que executa uma tarefa ou calcula um valor. Para usar uma função, você deve defini-la em algum lugar no escopo do qual você quiser chamá-la.

## Funções como objetos de primeira classe

Funções em Javascript são conhecidas como objetos de primeira classe (first-class objects). Isso porque tudo o que você pode fazer com um objeto, você pode fazer com funções. Na realidade uma função é um objeto do tipo _Function._

Sendo assim, as funções em Javascript podem ser:

### Criadas de forma literal

```javascript
function myFunction(){} //definindo uma função
```

### Passadas como parâmetros para outras funções

```javascript
function myFunction(param){} //definindo a função
//invocando a função e passando como parâmetro outra funçãomyFunction(function(){ console.log("função como parâmetro") })
```

### Atribuídas para propriedades de objetos;

```javascript
//definindo um objeto com uma propriedade que é uma funçãolet obj = {
    start:function(){}
};
//atribuindo uma função como propriedade para um objeto dinamicamente
let obj = {};obj.myFunction = function(){};
```

### Retornadas como resultado de uma função

```javascript
function myFunction(){        return function(){} //retornando uma função como resultado
}
```

### Possuir propriedades que podem ser atribuídas dinamicamente

```javascript
function myFunction(){}myFunction.startTime = 0 // atribuindo uma propriedade para a função
// a forma abaixo também funcionalet myFunction = function(){}
myFunction.startTime = 0
```

Os exemplos acima mostram o porque das funções em Javascript serem objetos de primeira classe e como elas podem ser utilizadas exatamente como qualquer outro objeto em Javascript. Agora vamos ver as formas que podemos definir as funções para então utilizá-las.

## Definindo funções

Em Javascript há mais de uma forma de definir funções. Cada forma pode alterar a maneira de como e quando podemos invocá-las e manipulá-las. Podemos definir funções como: _function declarations, function expressions, arrow functions, function constructor e function generators._

### Function declarations

Essa é a forma mais utilizada de definir funções. Nós utilizamos a palavra-chave _function_seguida de um nome para a função e uma lista de parâmetros (que é opcional) entre parênteses. Podemos definir function declarations em qualquer lugar de nosso código e dentro de outras funções inclusive. Vamos ver como fica isso no código:

```javascript
// define uma função que recebe 2 argumentos e retorna a soma dos mesmosfunction sum(x, y){    return x + y;}
function createProfile(){        function isValid(){ //definindo uma função dentro de outra função        return 'valid!!'    }
}
```

Function declaration podem ser invocadas antes ou depois de serem definidas no código fonte, não faz diferença. O exemplo abaixo funciona de ambas as formas.

```javascript
function sum(x,y){return x + y} //definindo a função antes de invocar
sum(1,3)
//invocando antes de definirsum(1,3)
function sum(x,y){ return x + y }
```

Isso é chamado de [**function hoisting**](https://developer.mozilla.org/pt-BR/docs/Glossario/Hoisting)**.**

Durante a fase de criação da memória, a engine JavaScript reconhece uma declaração de função pela palavra-chave **function** — ou seja, a engine JavaScript disponibiliza a função colocando-a na memória antes de prosseguir. Por isso, ela está disponível aparentemente antes da definição da mesma quando se lê o código de cima para baixo.

### Function expressions

Nós podemos utilizar funções como qualquer outro tipo em Javascript. Podemos utilizar as funções como qualquer outra expressão na qual definimos quando e onde precisamos para executar imediatamente ou não, dentro de um dado contexto. Vamos ver alguns exemplos de function expressions.

```javascript
let sum = function(x, y) {        return x + y;}
//passando a função como argumento para outra funçãomyFunction(function(){})
// definindo uma função e invocando ela após a definição// os 5 exemplos abaixo são function expressions que vão ser imediatamente invocadas
(function(){})()
+function(){}()
-function(){}()
!function(){}()
~function(){}()
```

**Em function expressions o nome é opcional,** uma vez que atribuídas a uma variável elas podem ser invocadas pelo nome da variável. Passadas como argumento podem ser invocadas pelo nome do parâmetro da função. Invocadas imediatamente(os últimos 5 exemplos acima, vamos falar mais disso no post), o nome é irrelevante para ser executado.

Ao contrario de function declarations, **function expressions não podem ser invocadas antes de sua definição no código**. O exemplo abaixo gera um erro:

```javascript
myFunction() //ReferenceError: myFunction is not defined
let myFunction = function(){}
```

### Arrow functions

Arrow functions foram introduzidas no [ES6](https://www.ecma-international.org/ecma-262/6.0/). É basicamente uma forma mais curta de definir function expressions e ajuda a tornar o código mais fácil de ler, principalmente nas expressões curtas. São definidas da seguinte forma:

```javascript
let myFunction = (x, y) => x + y
console.log(myFunction(1,3)) // o resultado é 4
```

Essa é uma função definida como uma Arrow function que recebe 2 argumentos e retorna a soma dos mesmos. Há várias coisas diferentes nessa função:

O exemplo acima é o equivalente a isso:

```javascript
let myFunction = function(x, y){        return x + y;}
```

Muito mais simples não é? :)

Mas essa não é a única forma de definir Arrow functions. Os exemplos abaixo também são válidos:

```javascript
// corpo com mais de uma linhalet myFunction = (x, y) => {    let z = 2
    return x + y + z;}
// apenas um parâmetrolet myFunction = x => x * 2
let myFunction = x => {        return x * 2}
let myFunction = () => {        console.log("Arrow function!")}
```

Para entender melhor a diferença das definições acima vamos ver algumas regras para se definir e utilizar as Arrow functions:

### Function constructor

Esse é a forma mais incomum(minha opinião) de se definir funções. Function constructors nos permitem definir funções dinamicamente. O _dinamicamente_aqui citado é definir a assinatura e o corpo da função. Vamos ver como isso é feito no código:

```javascript
var sum = new Function('a', 'b', 'return a + b');
console.log(sum(2, 6)); //imprime 8
```

Diretamente do mozila.org essa é a assinatura de uma function constructor:

```javascript
new Function ([arg1[, arg2[, ...argN]],] functionBody)
```

Nós passamos uma lista de argumento que vão ser os parâmetros da função que está sendo criada e o último argumento é a definição do corpo da função.

Esse é um recurso muito flexível, mas tem um custo de performance de se fazer o parser via `eval`****das strings em código válido.

Utilizando esse método fica mais claro que toda a função é um objeto em Javascript. Mais especificamente um _Function object._

Para mais detalhes dos métodos disponíveis de function constructors consulte o site da [mozilla](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function).

### Callback functions

Callback functions são muito faladas no mundo Javascript. Não são uma forma de definir funções e nem um tipo novo. Ë apenas uma forma de uso de qualquer uma das formas acima explicadas.

Quando passamos uma função como argumento para outra função, para que em dado momento ela seja invocada, estamos passando uma função de callback. Vamos pegar o exemplo de uma clássica requisição ajax:

```javascript
ajaxRequest("/data", function(){   //Faça algo quando a requisição estiver completo})
```

Estamos definindo aqui uma função que será invocada no futuro quando a requisição estiver completa.

Sempre que você ver um parâmetro chamado _callback_em uma função(essa é uma convenção muito comum), é porque você deve passar uma função que será invocada em dado momento do contexto dessa função.

### Parâmetros de função

Parâmetro é o nome que se da para a variável que nós declaramos na definição de uma função. Depois das novas implementações do ES6 há três tipos de parâmetros que podemos utilizar ao definir as funções em Javascript.

A primeira e mais simples forma é especificar na assinatura da função o nome das variáveis que queremos trabalhar dentro do corpo da função:

```javascript
function sum(a, b){ // a e b são os parâmetros dessa função    return a + b}
```

Outra forma de definir os **parâmetros**é**com valores padrões(default parameters)** definidos no caso da função ser invocada sem ter os argumentos passados.

```javascript
function sum(a = 1, b = 3){    return a + b;}
sum() // retorna 4
sum(2) // retorna 5
sum(2,4) // retorna 6
```

Qualquer uma das três formas acima de invocar a função vai funcionar. Basicamente o que nós estamos dizendo para o Javascript aqui é:"Se o parâmetro _a_não for informado, assuma que o valor é 1". Default parameter é um recurso também adicionado para a linguagem no ES6, antes disso para ter o mesmo comportamento teríamos que escrever algo mais ou menos assim:

```javascript
function sum(a, b){
    a = typeof a === undefined ? 1 : b    b = typeof b === undefined ? 3 : b
    return a + b;}
sum() // retorna 4
```

Outra forma que podemos definir parâmetros é com **rest paramaters,**que também é um recurso que veio com o ES6.

Imagine que você não sabe quantos argumentos serão passados para a função quando ela for invocada, mas precisa processar todos eles. Como você faz?

```javascript
function myFunction(param1, ...moreParams){
    console.log(param1);    console.log(moreParams);
}
myFunction(2,3,4,5,6);// vai imprimir a saida abaixo// 2//[3,4,5,6]
function myFuncTwo(...params){    console.log(params)}
myFuncTwo(1,2,3,4); // retorna [1,2,3,4]
```

Entendeu como funciona? Uma vez que você define parâmetros com o rest operator, que é os `…` seguido do nome do parâmetro, todos os argumentos que você passar vão ser agrupados em um único parâmetro como um array.

No primeiro exemplo do código acima eu tenho um parâmetro normal e estou agrupando todos os demais argumentos que podem ser passados com o rest parameter. Já no segundo exemplo estou agrupando todos os parâmetros com o rest parameter.

**O rest parameter sempre deve ser o último parâmetro definido na função.**

Para finalizar a seção de como definir funções, você deve ter notado que uso os termos **argumentos**e **parâmetros**e que pode lhe parecer a mesma coisa mas não é. Me refiro a **parâmetro como a variável que é definido na****declaração de uma função**, e **argumento é o****valor passado****para a função quando ela é invocada**. Em outras palavras um argumento é atribuído para um parâmetro quando uma função é invocada.

Seguindo esse entendimento, **você pode invocar uma função com mais argumentos do que parâmetros, mas o contrário gera um erro**.

## Invocando funções

Em javascript nós temos 5 maneiras de invocar funções: _como funções, como método_, _como um constructor_ e com os _métodos apply e call._

Antes de vermos cada uma dessas formas vamos discutir rapidamente dois parâmetros que são definidos pela linguagem implicitamente quando definimos uma função e que são atribuídos valores de acordo com a forma que invocamos uma função.

### Parâmetros arguments e this

Vamos analisar o código abaixo:

```javascript
function sum(x, Y){    console.log(arguments);    console.log(this)        return X + y;    }
console.log(sum(1, 4));
// vai imprimir{"0":1,"1":4}window5
```

Nossa função tem apenas 2 parâmetros, de onde veio esse `_arguments_` e `this`_?_São os parâmetros implícitos que comentei que o Javascript atribui quando a função é invocada. o parâmetro `_arguments_`__é um objeto com todos os argumentos que a função recebeu no momento em que foi invocada. O `_this_` é o contexto em que a função foi chamada.

Podemos utilizar o `_arguments_`__quando não sabemos quantos argumentos vamos receber quando a função for invocada. É o mesmo propósito do _rest parameter,_só que este só está disponível no ES6, se você não tem como utilizar ES6 em seu projeto `_arguments_`__resolve o seu problema.

O valor de `_arguments_`__sempre vai ser a mesma coisa, os argumentos passados para a função no momento em que foi invocada. Mas o `_this_`__pode mudar dependendo da forma que você invocou a função. Vou detalhar mais ao mostrar cada forma de invocar as funções.

### Invocando uma função como função

Se você achou redundante esse título, saiba que eu também. Mas essa forma de descrever que uma função é invocada como função, é para representar que vamos invocar uma função pelo nome que ela foi definida seguida por `()`. O código abaixo mostra como é:

```javascript
function sum(x, Y){    console.log(arguments);    console.log(this)        return X + y;    }
console.log(sum(1, 4));
```

Esse é o caso mais comum de uso das funções. Para esse caso, quando a função for invocada o parâmetro `this` vai receber como argumento o objeto Window. Como exibido no primeiro exemplo da seção **Parâmetros arguments e this.**Se você estiver usando nodejs o `_this_`__vai ser o contexto global do ambiente, que contém os objetos _global, process_ entre outros.

Caso você esteja em [_strict mode_](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Reference/Strict_mode)__invocar uma função como função vai fazer com que o _this_seja `undefined`**.**

```javascript
"use strict"
function sum(x, Y){    console.log(arguments);    console.log(this)        return X + y;    }
console.log(sum(1, 4));
// vai imprimir{"0":1,"1":4}undefined5
```

Também podemos invocar funções como funções com uma forma chamada Immediately-invoked function expression(IIEF). O código abaixo é um exemplo disso:

```javascript
(function(){console.log("IIEF"})()
-function(){console.log("IIEF"}()
+function(){console.log("IIEF"}()
!function(){console.log("IIEF"}()
~function(){console.log("IIEF"}()
```

Envolvendo a função entre parênteses e adicionando `_()_`__novamente__a função é invocada. Dessa forma o nome da função e opcional, uma vez que ela vai imediatamente invocada após a sua definição. Outra opção é utilizar os operadores _+, -, !_e_~_seguido dos `_()_`_._

### Invocando uma função um como método

Um método é basicamente uma função que foi atribuída para a propriedade de um objeto. Quando invocamos uma função dessa forma dizemos que invocamos uma função como um método. Vamos ao código:

```javascript
let obj = {};
obj.sum = function(x, y){    console.log(arguments);    console.log(this)        return x + y;    }
console.log(obj.sum(1,4));
//{"0":1,"1":4}{ sum: [Function]}5
```

Note que agora o `_this_`__mudou, ele é o próprio objeto `_obj_`_._Isso nos proporciona utilizar conceitos de programação orientada a objetos. Poderíamos ter diversos métodos e propriedades nesse objeto sendo processados e compartilhados por seu contexto através de t`_his_`.

### Invocando uma função como um construtor (constructor)

Aqui estamos falando de **constructor function** e não function constructors. Este último foi o que expliquei na seção de definir funções, é uma forma de definir os parâmetros e o corpo da função com string, através da instanciação de um objeto _Function._

Estou me referindo aqui de como invocar a sua função como um construtor através de um operador `_new_`_._Vamos ao código, novamente vou mostrar o que acontece com o parâmetro `_this_`:

```javascript
function Person(){
    console.log(this);
}
const person = new Person();
//imprimePerson{}
```

Quando invocamos uma função como um constructor temos o seguinte comportamento:

```javascript
function Person(){   return true;}
const person = new Person();
console.log(person)
//imprimePerson{}
function Person(){    this.name = "Robisson";    this.lastName = "Oliveira"}
const person = new Person();
console.log(person)
//imprimePerson{name:"Robisson", lastName:"Oliveira"}
function Person(){   return {name:"Robisson"};}
const person = new Person();
console.log(person)
//imprime
{name:"Robisson"}
```

Invocar uma função como um construtor não tem nada de especial, mas nos permite organizar e conduzir o código de uma forma mais orientada a objetos. Mesmo funções que foram criadas para serem invocadas como construtor pode ser invocada como função normalmente, mas tem um ponto de atenção. Vamos ver o exemplo abaixo:

```javascript
function Person(){    this.name = "Robisson";
    return this;}
const person = Person();
console.log(person);console.log(name)//imprimewindowRobisson
```

Se você não estiver em [strict mode](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Reference/Strict_mode) ao invocar uma função como função o parâmetro `_this_` recebe o objeto `window` lembra? Nesse caso as propriedades inicializadas vão estar disponíveis no contexto global do ambiente.

### Invocando uma função com os métodos apply e call

Javascript nos permite manipular o contexto em que queremos que as funções atuem no momento em que forem invocadas. Esse é o propósito dos métodos `_call_` e `_apply_`_._

Como citei acima que toda função é um objeto, toda a função tem os métodos `_apply_` e `_call_` disponíveis. Vamos ver como eles funcionam:

```javascript
function printArray(...array) {    this.items = array;    this.items.forEach(item => console.log(item));}
const obj = {};
printArray.apply(obj,[1,2,3,4]);
console.log(obj);
//imprime1234{ items: [ 1, 2, 3, 4 ] }
```

Vamos ver o que aconteceu aqui:

O método `_call_`__faz a mesma coisa, porém sua assinatura é diferente:

```javascript
printArray.call(items,1,2,3,4);
```

Ele recebe o contexto como primeiro argumento e todos os demais argumentos serão passados como argumentos para a função invocada. Não encontrei um motivo para esses dois métodos coexistirem, isso fica para você pesquisar :).

No livro _Secrets of the Javascript ninja_tem um exemplo legal da utilidade do método apply/call. Como podemos fazer um foreach:

```javascript
//alterei a função para mostrar o index do array passado e o valor. No livro só passa o index
function forEach(list, callback) {    for (var n = 0; n < list.length; n++) {        callback.call(list[n], n, list[n]);    }}
forEach([1, 2, 3], function(index, item) {    console.log(index, item);});
//imprime0 11 22 3
```

Como podemos ver, invocar funções é muito sobre entender o contexto sobre o qual a função está trabalhando. Pela minha experiência eu já vi e vejo ainda muitos erros no dia a dia por questão de contexto, então acho legal aprofundar nessa questão.

E falando sobre contexto vale a penar ver uma particularidade com relação as arrow functions. Arrow functions não possuem o parâmetro `this` com as outras funções, elas assumem o this do contexto que ela foi definida, ou em outras palavras podemos dizer que ela assume o `this`**__**de seu nível superior. Vamos ver um exemplo que acontece bastante confusão, adicionar um evento de click a um botão:

```javascript
const button = document.getElementById("mybutton");
const myEventClick = function(){    console.log(this);}
button.addEventListener("click",myEventClick);
//imprime o próprio botão que disparou o evento.
const myEventClick = () => {    console.log(this);}
button.addEventListener("click",myEventClick);
//imprime o this onde a função definida
```

Neste caso como a arrow function está sendo definida no contexto global, o `this` é o objeto `window`_._Vejo bastante confusão quando se tentar pegar referências do elemento com `this`, e o `this` acaba não tendo a propriedade esperada. Sem perceber você pode também estar criando várias propriedades globais.

## Generator functions

Generator functions foram introduzidas no ES6, elas são definidas de forma diferente e se comportam de forma diferente também, por isso fiz uma seção no artigo só para falar delas.

Generator functions são uma sequência de valores que a função definida vai retornar, mas esses valores não são retornados todos de uma vez. Cada vez que a função é invocada ela retorna esses valores até que todos tenham sido retornados. Vamos ao código:

```javascript
function* testGenerator() {    yield 1;    yield 2;    yield 3;}
const generatorFunction = testGenerator();
console.log(generatorFunction.next());console.log(generatorFunction.next());console.log(generatorFunction.next());console.log(generatorFunction.next());
//imprime{ value: 1, done: false }{ value: 2, done: false }{ value: 3, done: false }{ value: undefined, done: true }
```

Quando uma generator function é invocada ela retorna um [**objeto** **iterator**](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Guide/Iteratores_e_geradores)**.**Caso você não saiba o que é um iterator vamos ver um resumo de acordo com o site da [mozilla.org](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Guide/Iteratores_e_geradores):

Um objeto é um **iterator(iterador)** quando sabe como acessar itens numa coleção, um por vez, enquanto mantém rastreada a posição atual em uma dada sequência. Em JavaScript um iterator é um objeto que oferece o método `next(),` o qual retorna o próximo item da sequência. Este método retorna um objeto com duas propriedades: `done` e `value`

Nós também podemos percorrer o resultado de uma generation function usando loopings:

```javascript
function* testGenerator() {    yield 1;    yield 2;    yield 3;}
for(let item of testGenerator()){    console.log(item);}
//imprime123
```

## Ainda sobre funções…

Nesse artigo eu não expliquei nada sobre Promises ou funções assíncronas com async/wait. Isso foi bem explicado no artigo [**Como o Javascript funciona: O event loop e o surgimento da programação assíncrona + 5 maneiras de codificar melhor com async/await**](https://medium.com/reactbrasil/como-o-javascript-funciona-o-event-loop-e-o-surgimento-da-programa%C3%A7%C3%A3o-ass%C3%ADncrona-5-maneiras-de-18d0b8d6849a)**.**

Também não citei nada sobre [**closure functions**](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Guide/Closures)**,**mesmo estando relacionado com uma forma de se usar funções, eu prefiro falar sobre elas em um artigo que pretendo escrever sobre os objetos em Javascript, onde as closures são muito utilizadas para encapsular e proteger partes do seu código.

## Conclusão

O objetivo desse artigo era ser um guia profundo sobre o uso de funções em Javascript. Quais funções existem, como podemos utilizá-las algumas e dicas que podem ajudar. Espero que dê uma boa base de conhecimento para quem faz uso do Javascript no dia.

### Referências

446

446

4
