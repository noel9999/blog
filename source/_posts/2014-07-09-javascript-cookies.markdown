---
layout: post
title: "cookies練習，使用javascript"
date: 2014-07-09 15:34:51 +0800
comments: true


# Cookie介紹

---
## 目的：
w3schools開宗明義的介紹，cookie是被發明來解決***『如何讓伺服器記住使用者的資訊』***。因為當伺服器端回傳了一個response給使用者端時，伺服器就會忘記一切關於使用者的資料，而cookie如何實作到這點呢？其實就只是把資料存在使用者端（瀏覽器），例如使用者來到該網站輸入了些基本資料，而我們可以將這些資料存入cookie裡，下次該使用者在拜訪網頁時，我們就可以自動去cookie找尋之前的資料並且顯示出來，延伸應用包含使用者登入、記住使用者喜好資訊、購物車實作等等都是常見的cookie應用。而現今cookie也時常與session搭配做進一步的應用。

<!-- more -->
## 簡單介紹：

一般cookie特性如：    

* 一個cookie的大小最多為4KB
* 一個網站能夠存取20個cookie
* 瀏覽器最多可記住300個cookie，
* 內容是key=value的形式，其中value只能是字串，如果想存入array或hash得多花些技巧
* 可設定expires時間來決定cookie的存活時間
* 若使用者關閉瀏覽器但cookie仍未失效，則會寫入`cookies.txt`裡

###以http的角度來看cookie的運作方式

當client發出一個request給server時，該request的header裡會夾帶一些訊息，其中就包含`Cookie`，而server可借此取得該client端所儲存的cookie並使用；同理，當server端回response給client時，也會在header裡加上`Set-Cookie`的內容，藉此把想儲存的資料(如：使用者個人行為、使用偏好等資料)，存在client端裡，等待當下次該client端又向server端發出request時，便可檢查`Cookie`的內容來取得資料，來達成所謂***讓伺服器記住使用者的資訊***的目的。  

而一般來說cookie是server所設定且關心的，但實際上我們也可以在client端使用js來操作cookie、做即時性的操作，而不需要與server溝通，大大減少了server的工作，也增加了使用上的彈性與便利。以下就來介紹簡單的js對cookie操作：

### 範例

{% codeblock lang:js 範例：js存入cookie %}
	now=new Date()
	now.setTime( now.getTime( ) + 1000 * 60 * 5 )
	document.cookie = "foo=bar;expires=" + now.toGMTString()
{% endcodeblock %}

更進一步把它寫成個可以重複使用的function吧

{% codeblock lang:js 範例: 存取cookie函式 %}
	expires_at = new Date()
	expires_at.setTime(expires_at.getTime() - 1000 * 60 * 5)
	document.cookie = "foo=bar;expires=" + expires_at
{% endcodeblock %}



刪除cookie則把expires設得比現在的時間還早即可，就會失效了。  

{% codeblock lang:js 範例: js移除cookie %}
	expires_at = new Date()
	expires_at.setTime(expires_at.getTime() - 1000 * 60 * 5)
	document.cooki	e = "foo=bar;expires=" + expires_at
{% endcodeblock %}


讀取特定cookie，對js來說似乎沒有什麼特定方式，所以只好自己寫個function來取，連同剛剛提到的都一起寫成function吧

{% codeblock lang:js 範例: js操作特定cookie %}

function set_cookie(name, value, expires_at, path, domain){
  date = new Date()
  path = typeof path !== 'undefined' ? path : '/'
  domain = typeof domain !== 'undefined' ? domain : window.location.host
  if(expires_at === 'undefined'){
    // 預設一小時到期
    date.setTime(date.getTime() + 24 * 60 * 60 * 1000)
    expires_at = date.toGMTString()
  }
  else{
    date.setTime(date.getTime() + expires_at * 24 * 60 * 60 * 1000)
    expires_at = date.toGMTString()
  }
  cookie = name + '=' + escape(value) + ';expires=' + expires_at +  ';path=' + path + ';domain=' + domain
  document.cookie = cookie
}

function delete_cookie(name){
  set_cookie(name, '', -1)
}

function get_cookie(name){
  var result
  var cookie = document.cookie
  var index = cookie.indexOf(name)
  if (index > -1) {
    var start = index + name.length + 1
    var end = cookie.indexOf(';',start)
    if (end == -1){
      result = cookie.substring(start)
    }
    else {
      result = cookie.substring(start, end + 1)
    }
  }
  return result
}
{% endcodeblock %}

## 安全性
既然cookie是存在瀏覽器，那是否有被使用者看到、更改，或是其他第三方更改或攔截的可能呢？答案當然是有，而且許多常見的駭客行為也正好會從cookie下手。而且許多網站以session-cookie來實作會員登入機制，而session_id時常存在cookie裡，因此也增加會員帳號被盜取的可能性(如：[宏碁雲端售票](http://devco.re/blog/2015/01/30/cookie-security-insight-acer))
因此，我們盡量保持些良好的習慣與警覺性，可以降低cookie被串改或盜用的可能性

* 應該避免把敏感性資料存在cookie裡
* 若真有敏感性資料存在cookie裡需加密
* 對cookie設定`Secure Flag`（Https）
* cookie設定`Http Only Flag`（js無法取得Cookie）

遵守以上幾點，應能把cookie帶來的風險降到最小







