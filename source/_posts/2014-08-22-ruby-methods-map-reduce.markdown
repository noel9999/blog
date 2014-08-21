---
layout: post
title: "Ruby Methods Map Reduce"
date: 2014-08-22 00:36:38 +0800
comments: true
categories: ruby 

---
<br>
##關於迭代（iterator）
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;簡單的說，迭代就是重複某一過程，若以coding來，迭代器可以幫助我們走訪array或hash的每一個元素並執行某些要求或命令，而ruby則是善用迭代來讓我們更少地直接使用傳統的迴圈功能，以讓程式可以更精簡、直覺。而ruby內建的迭代函式真的很多，也非常的好用，常見的如select, find ,find_all, reject...等可以幫我們快速迭代array或是hash甚至物件內的元素，以快速達到某些目的，例如我們想找到陣列中的偶數:
<!-- more -->

###ruby內建迭代函式寫法
{% codeblock lang:ruby %}
[1,2,3,4,5,6,7,8,9].find_all do |n|
  n%2 == 0
end
# 結果 [2,4,6,8]
{% endcodeblock %}

###傳統迴圈寫法:
{% codeblock lang:ruby %}
array = [1,2,3,4,5,6,7,8,9]
result = []
for n in array
  result << n if n%2 == 0
end  
#result: [2,4,6,8]
result.clear # 洗白
i = 0
while(i<array.length)
  result << array[i] if array[i]%2 == 0
  i += 1
end 
# result: [2,4,6,8]
{% endcodeblock %}
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;寫慣c、java或php的人可能較熟習傳統迴圈寫法，雖然可以達成功能，但既然ruby已經幫我們都包成好用的迭代函式，那我們何不好好善用呢？！當然熟悉傳統迴圈的寫法也是可以讓我們理解迭代函式背後的原理，但在使用上建議各位多多利用這些函式吧，在ruby中，我們將會習慣使用迭代函式而非迴圈。

   常使用的迭代函式像有select, find, find_all, each, each_with_index, collect, reject, delete_if, grep, any?, all?, sort, sort_by, map, reduce等逐繁不及備載，而許多迭代函式的功用也非常相似，所以如何使用且看使用者習慣或喜好。

---   

##Map函式
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;map函式與each最大的不同是他會回傳一個新的陣列，而陣列的結果則是根據我們在程式區塊(block)裡所定義的來對原陣列做修改，其實這用途非常常見，像是我們有16名學生的原始成績，想幫他們做開根號在除以10後得到的加權成績:

{% codeblock lang:ruby %}
scores = [68, 84, 92, 34, 79, 82, 80, 85, 80, 31, 25, 45, 46, 30, 42, 34] 
new_scores = scores.map do |n|
  (Math.sqrt(n)*10).round(2)
end
# scores: [68, 84, 92, 34, 79, 82, 80, 85, 80, 31, 25, 45, 46, 30, 42, 34]
# new_scores: [82.46, 91.65, 95.92, 58.31, 88.88, 90.55, 89.44, 92.2, 89.44, 55.68, 50.0, 67.08, 67.82, 54.77, 64.81, 58.31]


array = [[1,2],[3,4],[5,6],[7,8]]
array.map do |n|
  n.lasy # 只要最後一個元素
end
# [2,4,6,8]

t = Topic.scoped.map(&:name)
t = Topic.scoped.pluck(:name)
{% endcodeblock %}
<br>
所以，如果我們希望能回傳迭代後的結果則使用map

---

##Reduce函式
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;reduce是比較抽象一點的迭代函式，但功能非常強大，它幫助我們逐一迭代元素外，還會保有一個結果變數可跟隨著迭代過程一起存活並最終回傳這個結果，而最後一行的值則是會迭代到下次做計算的初始值，所以最後一行切記不要使用puts或會回傳nil的函式，不然會哭哭唷！
<br>

{% codeblock lang:ruby %}
[1,2,3,4,5,6,7,8,9].reduce do |sum,value|
   sum += value
end
# 45
{% endcodeblock %}
<br>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;其中在程式區塊內我們宣告的第一個變數sum即是那個所謂的結果變數，會一直跟隨迭代所存活並作為最後結果回傳，第二個變數value則是會跟隨陣列不斷迭代的個別值，然而我們也可以在一開始使用reduce時便賦予sum一個初始值；若我們沒有給初始值的話，sum則一開始會以陣列的第一個值，然後直接從第二的值開始做迭代。
<br>


{% codeblock lang:ruby %}
[1,2,3,4,5,6,7,8,9].reduce(50) do |sum,value|
   sum += value
 end
 # 95
{% endcodeblock %}
<br> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;看到這邊可能會覺得很方便，但不以為然，若是不用reduce，我們過去的作法會是先在迭代範圍外先宣告一個變數，才能在範圍內使用此變數，而reduce則是把它包在一起使用。
<br>
{% codeblock lang:ruby %}
sum = 0
[1,2,3,4,5,6,7,8,9].each do |n|
  sum += n
end
# sum: 45

#也可以這樣使用


[1,2,3,4,5,6,7,8,9].reduce(:+)
# 45
[1,2,3,4,5,6,7,8,9].reduce(10,:+)
# 55
{% endcodeblock %}

###模仿select功能
<br>

{% codeblock lang:ruby%}
[1,2,3,4,5,6,7,8,9].reduce([]) do |result, value|
  result << value if value%2 == 0
  result  # 注意，這行如果不寫他會回傳nil，因為當1迭代進去時，不符合規則所以會回傳nil，這樣result下一次就會變成nil了而非[]
end

{% endcodeblock %}

###模仿map功能
<br>
{% codeblock lang:ruby%}
[1,2,3,4,5,6,7,8,9].reduce() do |result, value|
  result << Math.sqrt(value)/10
  result  
end
{% endcodeblock %}