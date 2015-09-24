---
layout: post
title: "Rails為何要使用escape_javascript?"
date: 2015-09-25 00:35:13 +0800
comments: true
categories: rails, javascript, AJAX
---

## 前言:
在Rails裡，為了某些AJAX效果，我們會使用`RJS（Remote Javascript）`，簡單地說就是發送了個js請求給server，rails controller做了某些事情後會`render像是*.js.erb的檔案`，在這檔案裡我們可以混用ruby與js，所以可以做些我們想做的事之後再編譯成js code並回傳給browser，然後browser直接處理這段js並改變網頁文件。
<!--more-->

{% img /images/rjs_example.png 800 600 'RJS圖例說明' 'RJS圖例說明' %}
<br>
  
 而通常我們希望改變的網頁上的某些區塊例如：某表單、某欄位，所以我們最快的方法是使用Rails提供的方法像是 `render`來直接產生一些html，像是增加一個圖片連結：
 
{% codeblock lang:html %}
    <a href="http://somehost/resources/123456">Hello World!</a>
{% endcodeblock %}

我們會使用`$('#some_id').append("<%= render some_link %>")`
來更改頁面，但如果直接這樣用把剛剛那段html當做參數丟進去就會有問題，因為`append("<%= ... %>")` 的那雙引號會造成bug，會變成：

{% codeblock lang:js %}

  $('#some_id').append("<a href="http://somehost/resources/123456">Hello World</a>")
{% endcodeblock %}

兩個雙引號組成的字串，會因為其他的雙引號造成問題，
所以我們需要`escape_javascript`來幫忙跳脫雙引號的~~束縛~~，`

{% codeblock lang:js %}

  $('#some_id').append("<%= escape_javascript render some_link %>")`:
  // 會等於
  $('#some_id').append("<a href=\"http://somehost/resources/123456\">Hello World</a>")
{% endcodeblock %}

#### 那如果我們不用雙引號，改用單引號包起來的話呢？
因為在jQuery我們的確會這麼寫
{% codeblock lang:js %}
  $('#some_id').append('"<p>Hello World</p>"')
{% endcodeblock %}

但實際上只用單引號包起來仍然會碰到字串內容如果有`斷行(\n)`而造成的問題，所以也要交給`escape_javascript`處理掉。

所以，為了在`RJS`等使用情境下，為了取得

  * 1: ***有效且可執行的 javascript code***
  * 2: ***跳脫雙引號帶來的束縛***

[參考](http://stackoverflow.com/questions/1620113/why-escape-javascript-before-rendering-a-partial)