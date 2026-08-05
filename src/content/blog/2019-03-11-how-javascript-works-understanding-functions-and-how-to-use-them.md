---
title: "How JavaScript works: understanding functions and how to use them"
seoTitle: "How JavaScript works: understanding functions and how to use them"
description: "Let's look at what functions are, how to define them, and how to invoke them in different ways."
pubDate: 2019-03-11
tags: ["JavaScript", "Frontend", "Programming", "Functions"]
series: "Frontend"
language: "en"
---

> Read the Portuguese version here: [Como o Javascript funciona: entendendo as funções e suas formas de uso](/blog/2019-03-11-como-o-javascript-funciona-entendendo-as-funcoes-e-suas-formas-de-uso/).


![Article image 1](/blog/javascript-funcoes/image-01.png)

I had been translating Alexander Zlatkov's series [**How JavaScript works**](https://medium.com/reactbrasil/como-o-javascript-funciona-uma-vis%C3%A3o-geral-da-engine-runtime-e-da-call-stack-471dd5e1aa30) for a few weeks. They are all very high-quality articles. Among his articles there was not one about functions in JavaScript, at least not yet, so I decided to write this one.

I will assume you already know the basics about functions, how to define and use them, and from that point we will go deeper into the concepts.

We can say JavaScript is a functional language. Everything except global execution code is executed inside a function. But a function in JavaScript is an object. Confusing? Do not worry; by the end of the text this will be clearer.

### Quickly defining functions in JavaScript

According to [mozilla.org](http://mozilla.org/):

Functions are fundamental building blocks in JavaScript. A function is a JavaScript procedure, a set of statements that performs a task or calculates a value. To use a function, you must define it somewhere in the scope from which you want to call it.

## Functions as first-class objects

Functions in JavaScript are known as first-class objects. That is because everything you can do with an object, you can also do with functions. In reality, a function is an object of type _Function_.

Therefore, JavaScript functions can be:

### Created literally

~~~javascript
function myFunction(){} // defining a function
~~~

### Passed as parameters to other functions

~~~javascript
function myFunction(param){} // defining the function

// invoking the function and passing another function as a parameter
myFunction(function(){
  console.log("function as parameter")
})
~~~

### Assigned to object properties

~~~javascript
// defining an object with a property that is a function
let obj = {
  start: function(){}
};

// assigning a function dynamically as a property of an object
let otherObj = {};
otherObj.myFunction = function(){};
~~~

### Returned as the result of a function

~~~javascript
function myFunction(){
  return function(){} // returning a function as result
}
~~~

### Have properties that can be assigned dynamically

~~~javascript
function myFunction(){}
myFunction.startTime = 0 // assigning a property to the function

// the form below also works
let otherFunction = function(){}
otherFunction.startTime = 0
~~~

The examples above show why JavaScript functions are first-class objects and how they can be used exactly like any other object in JavaScript. Now let us see the ways we can define functions and then use them.

## Defining functions

In JavaScript there is more than one way to define functions. Each form can change how and when we can invoke and manipulate them. We can define functions as _function declarations, function expressions, arrow functions, function constructors_ and _generator functions_.

### Function declarations

This is the most common way to define functions. We use the keyword _function_ followed by a function name and an optional list of parameters between parentheses. We can define function declarations anywhere in our code and even inside other functions.

~~~javascript
function sum(x, y){
  return x + y;
}

function createProfile(){
  function isValid(){
    return 'valid!!'
  }
}
~~~

Function declarations can be invoked before or after they are defined in the source code. It makes no difference.

~~~javascript
function sum(x,y){ return x + y }
sum(1,3)

sum(1,3)
function sum(x,y){ return x + y }
~~~

This is called [**function hoisting**](https://developer.mozilla.org/pt-BR/docs/Glossario/Hoisting). During the memory creation phase, the JavaScript engine recognizes a function declaration by the `function` keyword and makes the function available before continuing.

### Function expressions

We can use functions like any other JavaScript type. We can use them as expressions that are defined when and where we need them, to execute immediately or not, inside a given context.

~~~javascript
let sum = function(x, y) {
  return x + y;
}

myFunction(function(){})

(function(){})()
+function(){}()
-function(){}()
!function(){}()
~function(){}()
~~~

**In function expressions the name is optional**, because once assigned to a variable they can be invoked by the variable name. Unlike function declarations, **function expressions cannot be invoked before their definition in the code**.

~~~javascript
myFunction() // ReferenceError: myFunction is not defined
let myFunction = function(){}
~~~

### Arrow functions

Arrow functions were introduced in [ES6](https://www.ecma-international.org/ecma-262/6.0/). They are a shorter way to define function expressions and help make code easier to read, especially short expressions.

~~~javascript
let myFunction = (x, y) => x + y
console.log(myFunction(1,3)) // result is 4
~~~

This function receives 2 arguments and returns their sum. There is no need for the `function` keyword, no need to wrap the body in `{}` for single-expression functions, no need for `return`, and there is a new `=>` operator.

The example above is equivalent to this:

~~~javascript
let myFunction = function(x, y){
  return x + y;
}
~~~

Other valid forms:

~~~javascript
let myFunction = (x, y) => {
  let z = 2
  return x + y + z;
}

let double = x => x * 2

let log = () => {
  console.log("Arrow function!")
}
~~~

If the function body has more than one line, it must be wrapped in braces. If it returns a value from a multi-line body, `return` is required. If the function has only one parameter, parentheses are optional.

### Function constructor

This is the least common way, in my opinion, to define functions. Function constructors allow us to define functions dynamically, including the signature and body.

~~~javascript
var sum = new Function('a', 'b', 'return a + b');
console.log(sum(2, 6)); // prints 8
~~~

Directly from mozilla.org, this is the signature:

~~~javascript
new Function ([arg1[, arg2[, ...argN]],] functionBody)
~~~

We pass a list of arguments that will become the function parameters, and the last argument is the function body. This is flexible, but it has a performance cost because strings need to be parsed as valid code through `eval`.

Using this method makes it clearer that every function is an object in JavaScript, more specifically a _Function object_. For more details, check [mozilla](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function).

### Callback functions

Callback functions are widely discussed in JavaScript. They are not a way to define functions and not a new type. They are only a usage pattern for any of the forms explained above.

~~~javascript
ajaxRequest("/data", function(){
  // Do something when the request is complete
})
~~~

Whenever you see a parameter named _callback_, which is a very common convention, it means you should pass a function that will be invoked at some point inside that function's context.

### Function parameters

A parameter is the variable name declared in a function definition. After ES6, there are three types of parameters we can use when defining JavaScript functions.

~~~javascript
function sum(a, b){
  return a + b
}
~~~

We can also define **parameters with default values**, used when the function is invoked without those arguments.

~~~javascript
function sum(a = 1, b = 3){
  return a + b;
}

sum() // returns 4
sum(2) // returns 5
sum(2,4) // returns 6
~~~

Before ES6, we would need to write something like this:

~~~javascript
function sum(a, b){
  a = typeof a === undefined ? 1 : b
  b = typeof b === undefined ? 3 : b
  return a + b;
}
~~~

Another option is **rest parameters**.

~~~javascript
function myFunction(param1, ...moreParams){
  console.log(param1);
  console.log(moreParams);
}

myFunction(2,3,4,5,6);
// 2
// [3,4,5,6]

function myFuncTwo(...params){
  console.log(params)
}

myFuncTwo(1,2,3,4); // [1,2,3,4]
~~~

With the rest operator, `...` followed by the parameter name, all remaining arguments are grouped into a single array parameter. **The rest parameter must always be the last parameter defined in the function.**

A **parameter** is the variable defined in the function declaration. An **argument** is the value passed to the function when it is invoked. An argument is assigned to a parameter at invocation time.

## Invoking functions

In JavaScript we have five ways to invoke functions: _as functions_, _as methods_, _as constructors_ and with the _apply_ and _call_ methods.

### The arguments and this parameters

~~~javascript
function sum(x, y){
  console.log(arguments);
  console.log(this)
  return x + y;
}

console.log(sum(1, 4));
~~~

Our function has only two parameters, so where did `arguments` and `this` come from? They are implicit parameters assigned by JavaScript when the function is invoked. `arguments` is an object with all arguments received by the function. `this` is the context in which the function was called.

We can use `arguments` when we do not know how many arguments we will receive. The value of `arguments` is always the arguments passed to the function, but `this` can change depending on how the function is invoked.

### Invoking a function as a function

This is the most common use: invoking a function by its name followed by `()`.

~~~javascript
function sum(x, y){
  console.log(arguments);
  console.log(this)
  return x + y;
}

console.log(sum(1, 4));
~~~

In this case, `this` receives the Window object in the browser. In Node.js, it will be the global environment context. If you are in [strict mode](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Reference/Strict_mode), invoking a function this way makes `this` be `undefined`.

We can also use Immediately Invoked Function Expressions, or IIFEs:

~~~javascript
(function(){ console.log("IIFE") })()
-function(){ console.log("IIFE") }()
+function(){ console.log("IIFE") }()
!function(){ console.log("IIFE") }()
~function(){ console.log("IIFE") }()
~~~

### Invoking a function as a method

A method is a function assigned to a property of an object.

~~~javascript
let obj = {};

obj.sum = function(x, y){
  console.log(arguments);
  console.log(this)
  return x + y;
}

console.log(obj.sum(1,4));
~~~

Now `this` is the object `obj` itself. This gives us a way to use object-oriented programming concepts, sharing methods and properties through the object context.

### Invoking a function as a constructor

Here we are talking about **constructor functions**, not function constructors. We invoke a function as a constructor using the `new` operator.

~~~javascript
function Person(){
  console.log(this);
}

const person = new Person();
~~~

When we invoke a function as a constructor, a new object is created and assigned to `this`. If a primitive value is returned, it is ignored; if an object is returned, that object is returned.

~~~javascript
function Person(){
  this.name = "Robisson";
  this.age = 33;
}

const person = new Person();
console.log(person)
~~~

Functions created to be invoked as constructors can still be invoked as regular functions, but that can create problems when not using strict mode because `this` may point to `window`.

### Invoking a function with apply and call

JavaScript lets us manipulate the context in which functions operate when invoked. That is the purpose of `call` and `apply`.

~~~javascript
function printArray(...array) {
  this.items = array;
  this.items.forEach(item => console.log(item));
}

const obj = {};
printArray.apply(obj, [1,2,3,4]);
~~~

The `apply` method invokes a function and receives, as its first argument, the object that should be assigned to `this`. The second argument is an array that becomes the invoked function's arguments.

The `call` method does the same thing, but its signature is different:

~~~javascript
printArray.call(obj, 1, 2, 3, 4);
~~~

It receives the context as the first argument and all other arguments are passed directly to the invoked function.

In _Secrets of the JavaScript Ninja_, there is a good example of using apply/call to build a foreach:

~~~javascript
function forEach(list, callback) {
  for (var n = 0; n < list.length; n++) {
    callback.call(list[n], n, list[n]);
  }
}
~~~

Invoking functions is largely about understanding the context in which the function is working. In my experience, many everyday errors happen because of context, so it is worth going deeper into this topic.

Arrow functions have a particular behavior: they do not have `this` like other functions. They assume the `this` from the context in which they were defined.

~~~javascript
const button = document.getElementById("mybutton");

const myEventClick = function(){
  console.log(this);
}

button.addEventListener("click", myEventClick);

const myArrowEventClick = () => {
  console.log(this);
}

button.addEventListener("click", myArrowEventClick);
~~~

Because the arrow function is defined in the global context, `this` is the `window` object. This creates confusion when people try to reference the element with `this` and the expected property is not there.

## Generator functions

Generator functions were introduced in ES6. They are defined differently and behave differently too, so I dedicated a section to them.

Generator functions return a sequence of values, but not all at once. Each time the function is invoked, it returns values until all of them have been returned.

~~~javascript
function* testGenerator() {
  yield 1;
  yield 2;
  yield 3;
}

const generatorFunction = testGenerator();
console.log(generatorFunction.next());
console.log(generatorFunction.next());
console.log(generatorFunction.next());
~~~

When a generator function is invoked, it returns an [**iterator object**](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Guide/Iteratores_e_geradores). An object is an iterator when it knows how to access items in a collection one at a time while keeping track of the current position in a sequence. In JavaScript, an iterator offers the `next()` method, which returns the next item with `done` and `value` properties.

We can also loop through the result of a generator function:

~~~javascript
function* testGenerator() {
  yield 1;
  yield 2;
  yield 3;
}

for(let item of testGenerator()){
  console.log(item);
}
~~~

## More about functions

In this article I did not explain Promises or asynchronous functions with async/await. That was explained in the article [**How JavaScript works: the event loop and the rise of asynchronous programming plus 5 ways to write better async/await code**](https://medium.com/reactbrasil/como-o-javascript-funciona-o-event-loop-e-o-surgimento-da-programa%C3%A7%C3%A3o-ass%C3%ADncrona-5-maneiras-de-18d0b8d6849a).

I also did not mention [**closures**](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Guide/Closures), even though they are related to function usage. I prefer to talk about them in a future article about objects in JavaScript, where closures are widely used to encapsulate and protect parts of code.

## Conclusion

The goal of this article was to be a deep guide on using functions in JavaScript: what functions exist, how we can use them, and some tips that may help. I hope it provides a good knowledge base for anyone using JavaScript day to day.

### References

- [https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Guide/Fun%C3%A7%C3%B5es](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Guide/Fun%C3%A7%C3%B5es)

- [https://www.ecma-international.org/ecma-262/5.1/#sec-13](https://www.ecma-international.org/ecma-262/5.1/#sec-13)

- [https://www.ecma-international.org/ecma-262/6.0/#sec-function-definitions](https://www.ecma-international.org/ecma-262/6.0/#sec-function-definitions)

- [http://braziljs.github.io/eloquente-javascript/chapters/funcoes/](http://braziljs.github.io/eloquente-javascript/chapters/funcoes/)

- [Secrets of the Javascript ninja, second edition](https://www.manning.com/books/secrets-of-the-javascript-ninja-second-edition) — John Resig

- [https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function)

- [https://openclassrooms.com/en/courses/3523231-learn-to-code-with-javascript/4379006-use-constructor-functions](https://openclassrooms.com/en/courses/3523231-learn-to-code-with-javascript/4379006-use-constructor-functions)

- [https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Reference/Strict_mode](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Reference/Strict_mode)

- [https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Guide/Iteratores_e_geradores](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Guide/Iteratores_e_geradores)

- [https://github.com/getify/You-Dont-Know-JS](https://github.com/getify/You-Dont-Know-JS)

- [https://developer.mozilla.org/pt-BR/docs/Glossario/Hoisting](https://developer.mozilla.org/pt-BR/docs/Glossario/Hoisting)

- [https://www.ecma-international.org/ecma-262/6.0/](https://www.ecma-international.org/ecma-262/6.0/)
