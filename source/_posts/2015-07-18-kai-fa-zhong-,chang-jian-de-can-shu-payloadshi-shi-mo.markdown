---
layout: post
title: "開發中，常見的參數payload是什麼?"
date: 2015-07-18 02:34:43 +0800
comments: true
categories: programming
---
開發中，常常見到許多文件裡的方法或函式帶有名為`payload`的參數，像是最近在因為新專案開始使用了`react.js`+`flux`，正在看前輩的code學習與~~觀摩中~~，就非常常見到payload這參數，直覺想到『
啊這個字就是負載量啊!?沒什麼的嘛』但其實知道他英文叫做負載量但我還是不知道這參數是要幹什麼的，或是為什麼要這樣取名，不直接叫`data`, `params`之類的。甚至不小心在看pg官方文件裡又出現payload這個參數名，讓我更驚覺到原來不是只有`flux`裡面這樣用，別的地方也會這樣命名，也意味著這其實是個常見且通用的使用名詞，所以還是來個一探究竟的好。

<!--more-->
 
---
 
## Payload的定義

payload意思即為承載量，在開發中則是指出在一堆資料中我們所***關心***的部分!

網路找到的定義為：

>
On the Internet, a payload is either: The essential data that is being carried within a packet or other transmission unit.

## Payload的由來

google到一篇很好的文章對payload為何這樣叫有很好的解釋，文中指出這個名詞是借用運輸工具上的觀念而來的，例如：卡車、油罐車、貨輪等所謂的**載具**，然後通常一個載具的總重量一定**大於**載具的承載量，例如油罐車的總重量包含了他所運載的油量、司機的重量、油罐車行駛所需的油量，但我們所關心僅是油罐車所承載的油量而已。

~~故，得證~~

>Payload, the load that pays  

對programming來說，我們可以某api的response為例子來看

{% codeblock lang:js %}
	{
    	"status":"OK",
	    "data":
        	{
            	"message":"Hello, world!"
        	}
	}
{% endcodeblock %}

 其中的Hello, world!正是payload，也是我們關心的部分。
 
 到這裡我們應該了解為何參數名要叫做`payload`，而非`data`或是`params`是有其目的性的，而更進一步的熟悉與使用payload這個參數，則就要再深入看該方法或函式的使用與定義了。
 
## 參考
[文中的解釋與例子出處](http://programmers.stackexchange.com/questions/158603/what-does-the-term-payload-mean-in-programming)









