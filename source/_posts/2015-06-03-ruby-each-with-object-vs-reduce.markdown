---
layout: post
title: "Ruby each_with_object vs reduce"
date: 2015-06-03 23:37:29 +0800
comments: true
categories: Ruby
---

又發現一個好用的方法`each_with_object`，是屬於*Enumerable*的方法之一，最近看前輩的code才發現的，算是each家族中的一員，不得不說ruby內建的許多列舉方法實在是非常方便啊！它與前陣子介紹的`reduce`目的上有點類似，但似乎更為易懂，直接來看範例吧！
<!--more-->

{% codeblock lang:rb each_with_object 範例一 %}
	%w(red blue yello black).each_with_object({}) do |value, hash|
		hash.store(value, value.capitalize + '!')
	end
	// => {"red"=>"Red!", "blue"=>"Blue!", "yello"=>"Yello!", "black"=>"Black!"}
{% endcodeblock %}

## 介紹
範例一其實就只是把一個陣列迭代做些處理後存入hash裡，然後依序下去直到迭代完成，是不是很熟悉呢？沒錯，類似的功能`reduct/inject`也做得到，甚至你單用each也是可以，但是`each_with_object`更方便簡單，**你必須先指定一個參數**當做一個容器的初始值，此例就是hash，然後每次迭代之後他會自動記住容器的狀態，並自動代入下次迭代，這裡與`reduce`不太一樣，寫法上是更簡當方便，剛剛的範例如果用`reduce`寫的話則是：

{% codeblock lang:rb reduce 範例一 %}
	%w(red blue yello black).reduce({}) do |result, value|
		result.store(value, value.capitalize + '!')
		result
	end
	// => {"red"=>"Red!", "blue"=>"Blue!", "yello"=>"Yello!", "black"=>"Black!"}
{% endcodeblock %}
  
最後輸出是一樣的，但`reduce`我們必須自己把結果擺在最後一行(~~return~~)，`reduce`才會把它當作下次繼續迭代的***結果值***，這點對某些人可能比較不是那麼地直覺。

來看看另一個`each_with_object`的應用範例，讓我們來使用該函式做出類似select的效果  

{% codeblock lang:rb each_with_object 範例二：模仿select %}
	(1..9).each_with_object([]) do |value, array|
	    array << value unless (value % 2 == 0)
	 end	  
//  => [1, 3, 5, 7, 9]
{% endcodeblock %}

基本上你可以依需求或你傳入的參數類型來自由使用該方法進而達到任何可能的功能，而可傳入的參數可以是array、hash、甚至是openstruct，所以object或hashie當然應該也可以，有興趣的人可以自己玩玩看。


## 與reduce差別  

雖然這個方法看似很潮、很精簡，但還是有些使用上的限制與和`reduce`的差別，像是:
  
 1. each_with_object必須傳入一個參數，reduce則不然
 2. each_with_object接受的參數必須是容器型，不能是純值
 3. each_with_object程式區塊的參數順序與reduce相反，each家族都是以個別值為第一個參數

第一與第三點應該非常直覺好懂，第二點則需要說明一下，因為each_with_object可以自己記住迭代回傳結果，而該結果也必須在使用該方法時就給定，如果此時你給他的參數是純值像是int、str、boolean等型態的物件，則會因為屬於純值而無法改變或操縱物件本身，所以永遠都會回傳自己，如以下範例:
  
{% codeblock lang:rb each_with_object 範例三：參數給數字 %}
	(1..5).each_with_object(1) do |value, array|
		value * array
	 end	  
	//  => 1 而非 120
{% endcodeblock %}

以上就是該方法的介紹與分享，如果有問題或是發現小弟有哪邊有錯誤都請歡迎指教唷，感謝各位