---
layout: post
title: "ruby 類別變數"
date: 2015-07-22 23:36:42 +0800
comments: true
categories: ruby
---
類別變數，有時也稱靜態變數，簡單說就是專屬於類別的變數，不依實體不同而有所差異，類別成員下皆共享的，因為是存在特定的記憶體區塊，所以不會因實體的存活期間所影響。  
  
各個oop語言的類別變數大致的理念都是相同的，用法上可能會略有不同，今次是就來探討`ruby`的使用狀況。

<!--more-->

## 類別變數種類
`ruby`的類別變數有區分以下兩種，而兩者都可以被類別方法正常存取與使用，但還是有差異如下：
>
@@var => Class Variable  類別變數
@var => Class Instance Variable  類別實體變數

### 主要差異為：

### 1.`@@`可以給子類別繼承; `@`不可以
{% codeblock lang:rb 類別變數繼承 %}
class Parent
  @@blood = :b
  @hobby = :car

  def self.blood
    @@blood
  end

  def self.hobby
    @hobby
  end
end

class Child < Parent
end

Parent.hobby # car
Child.hobby # nil
Parent.blood # b
Child.blodd # b

Child.instance_eval do
  @hobby = :coding
end
Child.hobby # coding
{% endcodeblock %}

---
### 2.`@@`雖可以被繼承，但由於所有類別都共用，故`@@`更改後也會影響其他類別所擁有的相同`@@`;`@`因為不會被繼承所以沒這問題
所以使用`@@`的時候請小心，一般來說較常使用`@`來當做類別變數

{% codeblock lang:rb 類別變數共享問題 %}
class Parent
  @@blood = :b

  def self.blood
    @@blood
  end

  def self.blood=(value)
    @@blood = value
  end
end

class Child < Parent
end

Parent.blood # b
Child.blood # b
Child.blood = :c
Parent.blood # c
Child.blood # c
{% endcodeblock %}

---
### 3.`@@`可以給實體方法使用; `@`不可以
因為`@`對實體方法的角度來看，會當做實體變數去讀取而非類別實體變數
{% codeblock lang:rb 實體方法使用類別變數 %}
class Parent
  @@blood = :b
  @hobby = :car

  def blood
    @@blood
  end

  def hobby
    @hobby
  end
end

dad = Parent.new
dad.blood # b
dad.hobby # nil
{% endcodeblock %}
如果實體方法想使用類別實體變數，那就改讓實體方法去呼叫類別方法即可

{% codeblock lang:rb %}
Parent.class_eval do
  def self.hobby
    @hobby
  end

  def get_hobby_by_class_method
    Parent.hobby
  end
end

mom = Parent.new
mom.hobby # nil
mom.get_hobby_by_class_method # car
{% endcodeblock %}

## 補充
換種類別方法定義的方式來看更複雜一點的例子
{% codeblock lang:rb %}
class Animal
  class << self
    @@move = true
    @@breath = "Air"
    @food = %w(meat grass)
    def description
      if @@move
        puts "We Can beathe #{@@breath}"
      end
    end

    def food
      @food ||= []
    end

    def food_list
      @food.each do |food|
        puts "We like #{food}"
      end if @food
    end
  end
end

Animal.description
# We Can beathe Air
# nil
Animal.food_list
# nil
Animal.food << "meat" 
Animal.food_list
# We like meat{% endcodeblock %}

看來我們無法在`class << self`的裡面直接定義`@`的類別實體變數，若非要在該區塊內設定`@`的類別實體變數，得借用類別方法來完成！

### 參考
[Class Variables and Methods](https://rubymonk.com/learning/books/4-ruby-primer-ascent/chapters/45-more-classes/lessons/113-class-variables)