---
layout: post
title: "Ruby Arguments 小技巧"
date: 2015-09-15 00:42:40 +0800
comments: true
categories: ruby
---
在ruby裡，函式參數的定義與使用可以多變且靈活，這裡我們來分享一些比較特別的但實用的例子
<!--more-->

## 參數的定義使用

當如果參數是要使用hash的話，我們都知道可以這樣用
{% codeblock lang:rb %}

def hello (options = {})
  first_name = options[:first_name]
  last_name = options[:last_name]
  %Q(Hello, #{first_name} #{last_name})
end

hello first_name: 'Wayne', last_name: 'Rooney'
// "Hello, Wayne Rooney" 
{% endcodeblock %}

### 使用 *Keyword Arguments*

但實際可以這樣用更快，少了從`options`取值的步驟，而且結果是一樣的

{% codeblock lang:rb %}

def hello (first_name:, last_name:)
  %Q(Hello, #{first_name} #{last_name})
end

hello first_name: 'Wayne', last_name: 'Rooney'
// "Hello, Wayne Rooney" 
{% endcodeblock %}

直接丟一個`hash`給它也可以，~~廢話~~

{% codeblock lang:rb %}
hello { first_name: 'Wayne', last_name: 'Rooney' } 
// "Hello, Wayne Rooney" 
{% endcodeblock %}

但這樣會失敗
{% codeblock lang:rb %}
hello first_name: 'Wayne', { last_name: 'Rooney' } 
// SyntaxError: unexpected '\n', expecting =>
{% endcodeblock %}

必須先把`merge`成一個hash，或使採用~~更潮~~的`**`運算子
{% codeblock lang:rb %}
hello first_name: 'Wayne', **{ last_name: 'Rooney' } 
"Hello, Wayne Rooney"
{% endcodeblock %}

也可以與其他參數混用，但一般來說我們會把`hash`形式的參數擺在最後面
{% codeblock lang:rb %}

 def man_united(manager, members, color:, name:)
    %Q(Manager: #{manager}, members: #{members}, color: #{color}, name: #{name} )
 end
 man_united '+2', :rooney, color: :red, name: :red_devil
 // "Manager: +2, members: rooney, color: red, name: red_devil "
{% endcodeblock %}

我們也可以這樣用，讓不固定的參數可以區分成`array`與`hash`型態

{% codeblock lang:rb %}

 def test(*array, **hash)
 	"#{array[0]} #{array[1]} #{hash[:name]}, #{hash[:age]}"
 end
 
 test(1,2,name: :noel, age: 27, extra: :not_available)
 // "1 2 noel, 27"
{% endcodeblock %}

### 如果不在乎傳入什麼值的話，可以這樣定義方法

{% codeblock lang:rb %}

 def foo(*)
 	do_something
 end
 
{% endcodeblock %}

這樣有什麼好處呢？假使今天我們想覆寫某個方法，在執行前先去增加某些邏輯，但又不影響到其他地方的使用。我們可以這樣寫：

{% codeblock lang:rb %}

 def save(*)
 	do_something
 	super
 end
 
{% endcodeblock %}

這樣同樣是呼叫`save`方法，但我已經在裡面塞入某些邏輯，但又不影響其他地方對`save`的使用，而`save`依然不需要擔心到底會接到哪些參數，反正都會原封不動的還給`super`

[參考](http://www.justinweiss.com/blog/2015/03/30/fun-with-keyword-arguments/)

