---
layout: post
title: "JSONP 介紹"
date: 2015-07-06 01:56:17 +0800
comments: true
categories: jQuery
---
JSONP(JSON with Padding)，聽起來跟`JSON`很像？兩者有什麼關連嗎？！JSONP是一種跨網域資料交換的***方式***，而JSON則是一種資料交換的***格式***。而兩者的關聯就是JSON是`AJAX`在交換資料所常用的格式，而JSONP則是AJAX為突破`同源政策`(Same-origin policy)，而可讓不同網域之間一樣可以靠xhr取得資料的手段。

<!--more-->

---

## JSONP由來：
在探討JSONP前一樣先來看看它是為何而生的呢？因為AJAX基於安全的考量，不允許對不同網域的網站發送請求，以防某些網站會在背後偷偷試圖連到其他網站去幹壞事（如：銀行網站之類的）；但跨域的AJAX需求仍然常發生，因此當然有諸多因應之道，常見的有下：

*  在同樣的網域下建立一支proxy api再例用這proxy去call外部的資源
*  伺服器端必須`Access-Control-Allow-Origin`設為`*`或給某些特定網域
*  利用script tag的`src`載入

而JSONP主要則是利用其中的第三點來達成，這是因為src這個attribute不受是否跨網域的限制(e.g. img, script, iframe)，所以我們可以利用這個特性傳回一段***可執行***的js code，例如
  
對於某個外部請求，server會回傳一段js code
{% codeblock lang:rb %}
	alert('WTF')
{% endcodeblock %}  

而clinet這邊可以這樣來獲取
{% codeblock lang:html %}
	<script src="http://somewhere.com/test"></script>
{% endcodeblock %}

  
結果就是會在頁面上跳出一句`WTF`，由此證明我們可以利用src不受跨網域限制的特性來克服以往AJAX所達不到的目的，而我們更進一步可以讓server端將希望回傳的資料(JSON)包成可執行的js code回傳給我們
如：
{% codeblock lang:js %}
	someFunction({ foo: 'bar', snsd: 'yoona'})
{% endcodeblock %}
而我們只需要在clinet裡面先定義好`someFunction`這個function就可以發揮如同callback的效用達成跨網域的請求了!

### JSONP目的
所以，JSONP主要就是讓server端將所有要回傳的資料包在任意名稱的function參數裡回傳給client端使用，`JSON with Padding`的名稱也非常貼切它的目的。而剛剛所提到任意名稱的function，是因為我們既然要事先定義該function，才可使用server回傳給我們的js code，那必然也得讓server要傳什麼名稱給我們，簡單的說就是需要動態的產生function名，而這只需要把function的名稱當做參數傳過去即可，而後端的處理可以這樣寫：
{% codeblock lang:rb some_controller.rb %}
	def jsonp
	  @data = { name: 'Yoda', role: 'master' }.to_json
	  @func_name = params[:callback]
	end
{% endcodeblock %}

{% codeblock lang:js jsonp.js.erb %}
	<%= @func_name %>(<%= raw @data %>)
{% endcodeblock %}

client的code會長這樣
{% codeblock lang:html %}
 	<script>
	  var say_hi = function(data){
	    alert('Hi '+ data.name)
	  }
	</script>
	<script src="http://somewhere.com/test?callback=say_hi">
	</script>
{% endcodeblock %}

---
## JSONP 使用 jQuery
### 標準用法
實際上JSONP不算是官方正式提出用來解決跨網域需求存取的解法，算是非官方的協定。然而JSONP本身也非使用xhr物件來達到跨網域的請求，但jQuery還是支援jsonp而且用法相當簡單：
{% codeblock lang:js %}
 	function test(data){
 		//do_something
 	}

	$.ajax({
	  url: 'http://somewhere.com/test',
	  type: 'get',
	  dataType: 'jsonp',
	  jsonp: 'callback',
	  jsonpCallback: 'test'
	})
{% endcodeblock %}

只要把`dataType`設為`jsonp`即可。而參數`jsonp`: 預設為callback，所以是callback=?，如果jsonp設為foo那就是 foo=? 。    

`jsonpCallback`: 預設是由jQeury亂數產生的唯一值當做function name，如果 jsonpCallback設為bar，那就是 callback=bar。 最後前端就必須要有定義function bar讓他可以執行。  

建議好像是讓兩者都為預設就可以了，因為cache的問題，亂數產生的function name像是callback=XXXXX就會每次都被視為新的request就比較不會有被cache住，而拿不到即時更新過資料的問題了。
### 精簡用法
{% codeblock lang:js %}
	$.ajax({
	  url: 'http://somewhere.com/test',
	  type: 'get',
	  dataType: 'jsonp',
	  success: function(data){
	    //do_something
	  },
	  error: function(){
	  	console.log('fail)
	  }
	})
{% endcodeblock %}

jsonp預設為callback，所以可以不需要寫，jsonpCallback也建議不用設定，使用預設的亂數值即可，而且聰明的jQuery會自動把回傳的data帶入success這個callback使用，也不需要額外在定義function去接了，非常方便！！

網路上看到使用$.getJSON網路上有可以這樣試，jQuery似乎會自動去偵測callback這個querystring(~~不過本人還沒測試過~~)
{% codeblock lang:js %}
	$.getJSON('http://somewhere.com/test?callback=?', function(data){
	    //do_something
	})
{% endcodeblock %}

以上就是關於jsonp的學習心得與分享，如有錯誤或其他建議都歡迎指正～




