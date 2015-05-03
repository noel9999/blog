---
layout: post
title: "Javascript This"
date: 2014-09-21 16:59:57 +0800
comments: true
categories: Javascript
---

this指標是物件導向語言裡很重要的觀念與應用，像是Java, PHP等等。而自己第一次聽到這名詞是大學時學習Java的時候，後來接觸php時也又碰到了this（php物件導向也是由Java借鏡來的），但對this到底為何其實都不是很懂，被困擾了很久，單看this字面的意思還是讓人覺得抽象，不過當時也有不少同學對於this也都是一知半解，但寫程式的時候還是會知道該怎麼使用它，個人覺得這不是個好的現象，所以今天來分享一下對this的學習心得，並主要以***javascript***作為範例。
<!-- more -->

## 什麼是this?
this代表當前的物件，更直白的說就是正在使用的物件為何，我們會用this來代表，這是很重要的一點。一般而言會呼叫this的場合僅在method裡面使用，而this代表的對象就是函式所屬的物件

{% codeblock lang:js this 簡單使用方式 %}
	var zilla = {
		name: 'godzilla',
		size: '100M 60000 tons',
		color: 'black',
		sayHello: function(){
			console.log("Hi, I am "+ this.name); // 這裡的this也可改用zilla來代表，效果是一樣的，因為此時this即是指zilla
			}
	}
	
	console.log(zilla.sayHello()); //  Hi, I am godzilla
{% endcodeblock %}

---

## this如何決定？

this的值是由環境`執行期間`決定，所以要看呼叫該方法時的環境決定，屬於window（全域範圍）或某特定物件:

{% codeblock lang:js this 的歸屬 %}
	var name = "Windows\'s Godzilla";
	var myObject = { name: "Object\'s Godzilla" };
	
	var getName = function(){
		console.log(this.name);
		}
	myObject.getName = getName;
	
	myObject.getName();
	//Object's Godzilla
	getName();
	//Windows's Godzilla
{% endcodeblock%}

---

## 當方法裡面包覆方法時，this會指向全域物件
當物件裡面含有巢狀方法時（方法裡面又包裹方法），則this會迷失方向，因此不會參考該物件，轉而參考全域物件，也就是window物件

{% codeblock lang:js 碰到巢狀時，this會指向全域物件 %}
	name = "Window Godzilla"
	var myObject = {
	   name: "Object Godzilla",
	   sayHellow: function(){
	   console.log("Hello " + this.name)},
	   lostThis: function(){
	     var gg = function (){
	     	console.log(this.name) //在這裡會印出Window Godzilla 而非 Object Godzilla
	   	 }()
	   }	   
	}
	myObject.sayHellow()
{% endcodeblock %}

即使傳入一個匿名方法，而該方法裡面又有呼叫到this時，依然會轉而參考window物件

{% codeblock lang:js 傳入一個帶有呼叫this匿名函式給物件方法 %}
name = "Window Object"
var myObject = {
     name: "Object Godzilla",
     sayHellow: function(callback){
     callback()
     console.log("Hello " + this.name)},
  }
myObject.sayHello(function(){console.log("Hellow " + this.name )})
// 印出
// Hellow Window Godzilla
// Hello Object Godzilla
{% endcodeblock %}

---

## 如果怕this迷失的話，我們可以明確指定給它
有很多方法可以避免this值迷失，常見的有下列幾種
<br>
### 1. 依靠範圍練尋找，把this指定給明確變數
{% codeblock lang:js %}
	var myObject = {
	name: "Object Godzilla",
	sayHello: function(){
		var that = this //就在這裡指定
		var useThat = function(){ console.log(that.name) }(that) 
		}
	}
{% endcodeblock %}
### 2. 使用call方法來明確指定使用哪個物件當做this
{% codeblock lang:js %}
  name = "Window Name"
  var test = function(){
  	console.log(this.name)
  }
  test()
  test.call({name: "Yoda"}) 
  //如果test方法有定義參數的話，則擺在this參數後方
  //test(this, arg1, arg2)
{% endcodeblock %}

### 3. 使用apply方法來明確指定使用哪個物件當做this
{% codeblock lang:js %}
  name = "Window Name"
  var test = function(){
  	console.log(this.name)
  }
  test()
  test.apply({name: "Yoda"}) 
  //使用方式同call，差別在於如果有其他參數要使用是全部擺在一個陣列，並在this之後的第二個參數傳入
  //test(this, [arg1, arg2])

{% endcodeblock %}

---

## 使用new來建立函式時，this會參考到該new出來的instance
聽來有點饒舌，但大概就是這樣的意思，此舉用法上很接近其他oop的實體變數的感覺，直接看範例
{% codeblock lang:js %}
	var Godzilla = function(){
		this.name = "Godzilla"
		this.gender = "Male"
		this.color = "Black"
		this.getName = function(){
		  console.log("Hi, my name is " + this.name)
		}
	}
	var jr = new Godzilla()
	jr.getName()
{% endcodeblock %}
若不使用new的話，則規則同一般使用方式

## 在prototype裡使用this，則會參考到該建構式的instance
如果說前一則提到的觀念像是使用實體變數的感覺，這邊則像是實體方法裡使用到實體變數的概念，因為定義在Object.prototype裡的方法可以被共享，之後該Object的實例共享，所以我們可以把它看做是實體方法，裡面呼叫的this則自然是參考到該實體的變數囉：
{% codeblock lang:js %}
var Godzilla = function(){
		this.name = "Godzilla"
		this.gender = "Male"
		this.color = "Black"
		}
Godzilla.prototype.getName = function(){
	  console.log("Hi, my name is " + this.name)
	}
var jr = new Godzilla()	
jr.getName()
	
{% endcodeblock %}

---

## 結論
其實this觀念並沒有這麼難懂，且大多數oop的this觀念大略是相同的，都是在幫我們釐清我們當前使用或定義的對象為何罷了。只是可能依語言特性而個有些用法上的差異等等，例如ruby裡是用self幾乎等同this的；以js危範例介紹，是因菜自js上物件的表達方式比較多變，所以this的變化也就更多元，較容易搞混，但懂得原理，只要稍微停下來思考相信很快就在也不會被難倒了，或搞混了，**千萬不要迷失自我為何啊**。

以上若有錯誤或其他建議都歡迎告知或討論唷，感謝^^
