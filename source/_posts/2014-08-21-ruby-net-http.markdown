---
layout: post
title: "RubyWay: Net::HTTP篇"
date: 2014-08-21 23:53:47 +0800
comments: true
categories: ruby
---
## 前言
這算是比較抽象的一篇，目前我也不是很清楚他實際的作用與原理，但先記錄一下我所學到的部分與應用。


## get_response


 有時候我們會需要在程式裡發出一個request，簡單的說就想像我們直接在瀏覽器裡輸入一串url，此時我們就可以利用Net::HTTP.get_response(*你要的uri*)，會回傳一個物件，而我們可以根據這個物件做我們想要的應用，看程式碼教學：
 
<!-- more -->
{% codeblock lang:rb 使用講解:Get %}

	require 'net/http'
	#方法一
	uri = URI.perse("example.com/bar/dosomething")  ＃先解析成uri物件比較方便
	response = Net::HTTP.get_response(uri) # 回傳物件就是我們要的東西，我們可以利用他做很多事

	#方法二
	http = Net::HTTP.new(uri.host,uri.port)
	response = http.request(Net::HTTP::Get.new(uri.request_uri))  #切記Get.new()的參數是request_uri不是uri
	
	Net::HTTP.get_print(uri)   #印出response.body
	
	
	response.code  		# 回傳http狀態碼
	response.body  		# 回傳整個body內容，通常是編碼過不是人能看的東西
	response.message 	# 回傳HTTP狀態碼代表訊息 ex："Moved Permanently" 
	response.uri 		# 回傳呼叫它的uri物件
	
	#uri物件也是個很方便的東西裡面常見的功能有
	
	uri.request_uri  	# 回傳你的request ex: /bar/dosomething
	uri.host			# 回傳domain
	uri.path 			# 同request_uri
	uri.scheme			# 回傳使用的傳輸協定
	uri.query			# 回傳querysting
	uri.port			# 回傳使用的port
	
	#query_string處理
	params = { :limit => 10, :page => 3 }
	uri.query = URI.encode_www_form(params)

{% endcodeblock  %}
應用範例：預留一個版位顯示response.body的結果，利用Net::HTTP對某伺服器發送一個請求，並且把回傳的結果存入memcache以利用來顯示到預留的版位上。嗯嗯，聽起來有點像是ajax的概念，只是由伺服器端坐的而且他是同步的...

{% codeblock lang:rb 應用範例:Get %}

	response = Net::HTTP.get_response(URI.parse("http://foobar.header.com/api"))
    if response.code.to_i == 200
      $memcached.set("header-html", response.body, 0)
    end
    
    某一處的VIEW顯現出來
    
    <%= raw $memcached.get("header-html").to_s.force_encode("utf8") %>
    
{% endcodeblock %}

---
## post_form

同理，有get方法就會有post方法，post方法一般用在傳送伺服器的的資料量大或是比較需要顧慮到安全時會用的！直接看教學範例：
{% codeblock lang:rb 使用範例:Post %}

	require "net/http"
	
	uri = URI.parse("http://example.com/foo/search")
	
	#方法一
	response = Net::HTTP.post_form(uri, {"data" => "My data blah blah", "per_page" => "50"})
	
	#方法二
	http = Net::HTTP.new(uri.host, uri.port)
	request = Net::HTTP::Post.new(uri.request_uri)
	request.set_form_date({"data" => "My data blah blah", "per_page" => "50"})
	response = http.request(request)
{% endcodeblock %}

## REST methods
有寫過rails的人相信都對RESTful不陌生，所以直接看範例吧！
{% codeblock lang:rb 使用範例:REST %}

	require "net/http"
	
	uri = URI.parse("http://api.noelsaga.net/")
	
	http = Net::HTTP.new(uri.host, uri.port) #
	
	#Get : 通常是讀取單一筆資料如show
	response = http.request(Net::HTTP::Get.new("/post/1"))
	
	#Post: 通常是用來建立資料
	request = (Net::HTTP::Post.new("/post/1"))
	request.set_form_date({:tile => "Monday", :content => "I'm wanna go home..."})
	response = http.request(request)
	
	#Put : 通常是用來更新一筆資料
	request = (Net::HTTP::Put.new("/post/1"))
	request.set_form_date({:tile => "Tuesday"})
	response = http.request(request)
	
	#Delete: 嗯嗯，字面意思很清楚了
	request = (Net::HTTP::Delete.new("/post/1"))
	response = http.request(request)
	
{% endcodeblock %}
##SSL/HTTPS request with PEM certificate 
如果是需要pem認證時，可以這麼做，此處直接使用Peter Cooper提供的範例
	
{% codeblock lang:rb 使用範例:PEM certificate %}
	require "net/https"
	require "uri"

	uri = URI.parse("https://secure.com/")
	pem = File.read("/path/to/my.pem")
	http = Net::HTTP.new(uri.host, uri.port)
	http.use_ssl = true
	http.cert = OpenSSL::X509::Certificate.new(pem)  # 根據pem檔案建立認證
	http.key = OpenSSL::PKey::RSA.new(pem)		      # 根據pem檔案建立認證
	http.verify_mode = OpenSSL::SSL::VERIFY_PEER
	request = Net::HTTP::Get.new(uri.request_uri)
{% endcodeblock %}
	
##Post傳檔部分 （從缺中）





[參考Peter Cooper文章](http://www.rubyinside.com/nethttp-cheat-sheet-2940.html)
