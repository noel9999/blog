---
layout: post
title: "Javascript This 指標"
date: 2014-09-21 16:59:57 +0800
comments: true
categories: Javascript
---

this指標是物件導向語言裡很重要的觀念與應用，像是Java, PHP等等。而自己第一次聽到這名詞是大學時學習Java的時候，後來接觸php時也又碰到了this（php物件導向也是由Java借鏡來的），但對this到底為何其實都不是很懂，被困擾了很久，單看this字面的意思還是讓人覺得抽象，不過當時也有不少同學對於this也都是一知半解，但寫程式的時候還是會知道該怎麼使用它，個人覺得這不是個好的現象，所以今天來分享一下對this的學習心得，並主要以***javascript***作為範例。
<!-- more -->

## 什麼是this?
this代表當前的物件，更直白的說就是正在使用的物件為何，我們會用this來代表。一般而言會呼叫this的場合僅在method裡面使用，而this代表的對象就是函式所屬的物件

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
## 未完...
