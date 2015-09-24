---
layout: post
title: "Rails 探訪html_safe"
date: 2015-09-25 01:12:27 +0800
comments: true
categories: Rails
---

## 介紹
Rails3開始為了安全性的考量`(XSS）`，怕使用者張貼一些有特殊目的`HTML`到網站上進而影響其他使用者或網站運作，所以會自動把所有`<%= %>`裡的字串都做溢出，當字串包含先特殊符號如：`< , >`等等都會被處理掉，這樣自然就不會被瀏覽器，非常地安全，而如果想不被溢出，我們此時可以使用`html_safe`這個helper來避免，詳情可以參考[ihower網路安全](https://ihower.tw/rails4/security.html)。
<!--more-->

但如果只講到這邊，那直接看`ihower`不是更快更詳細，所以這邊會在深入講解一下`html_safe`的應用與介紹。

## html_safe特性

如果我們對一個字串呼叫`html_safe`，他其實會回傳一個`ActiveSupport::SafeBuffer`的物件，基本上這個物件用起來、看起來都很像一般的字串，但他有個***特性***，就是如果該物件與其他的字串物件做結合，如使用`+ 或 <<` 等方法結合時，後加入的字串會自動被溢出：

{% codeblock lang:rb %}
  
  '<p>Foo</p>'.html_safe + '<p>Bar</p>'
  # 會變成 
  "<p>Foo</p>&lt;p&gt;Bar&lt;/p&gt;" 
{% endcodeblock %}

如果被加入的字串也是個`SafeBuffer`則不會有被溢出

{% codeblock lang:rb %}
  '<p>Foo</p>'.html_safe + '<p>Bar</p>'.html_safe
  # 會變成 
  "<p>Foo</p><p>Bar</p>" 
{% endcodeblock %}

## Render與html_safe

而`render`執行的時候，其實會先有一個`空字串的SafeBuffer`，在把template的每一行~~?~~都加入到那個字串裡面，所以本身就是`SafeBuffer`的字串就不會被溢出，純`html`也不會有是，而剩餘寫在`<%= %>`的當然都會自被溢出。

{% codeblock lang:rb %}

  html = ''.html_safe
  html << '<p>'.html_safe
  html << '<br />'
  html << '</p>'.html_safe
  html
{% endcodeblock %}

而Rails本身提供的`view helper`都已經是經過html_safe處理的，所以則可以正常運作。

## 小心使用html_safe

如果想客製化自己的`view helper`或是直接對一串可能包含***變數***的字串做`html_safe`時，我們可能會這麼做


{% codeblock lang:rb %}

  "<p>#{text}</p>".html_safe
  # or
  def my_helper(text)
    "<p>#{text}</p>".html_safe
  end
{% endcodeblock %}
則可能會很大的風險，因為我們不知道變數`text` 會包含著什麼內容，但整個字串都被已經被取消溢出了，如果`text`是含有惡意的code則就危險了。
<br>
所以，我們其實是應該`針對未知的部份`做溢出即可

{% codeblock lang:rb %}

  def my_helper(text)
    html = ''.html_safe
    html << '<p>'.html_safe
    html << text
    html << '</p>'.html_safe
  end
{% endcodeblock %}

但這樣好像有點醜，所以我們可以善用 Rails提供的`content_tag` helper來幫助我們，由於Rails helper都已經幫我們做好安全措施了，所以可以直接放心~~服用~~

{% codeblock lang:rb %}

  def my_helper(text)
    content_tag(:p, text)
  end
{% endcodeblock %}


## 結論

* 1: 永遠不要相信使用者傳來的參數或內容，要思考到最壞的打算
* 2: 避免對`含有未知內容的字串`做`html_safe`

[參考](http://makandracards.com/makandra/2579-everything-you-know-about-html_safe-is-wrong)