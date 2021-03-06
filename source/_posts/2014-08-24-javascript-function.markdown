---
layout: post
title: "Javascript Function"
date: 2014-08-24 20:16:53 +0800
comments: true
categories: javascript
---

<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Javascript(以下簡稱JS)的函式比以往所認識語言C, JAVA, PHP來得特別些。因為第一份工作主要都是在寫Rails所以接觸Ruby，覺得這語言很酷很方便，然後也因為今年開始自學JQuery也順便想弄懂Javascript的原理而開始接觸，之前有朋友說其實Ruby有些地方是從Javascript借鏡的，當時因為對JS還很不熟，所以也沒體會，但隨著看的範例多跟練習越來越多，也開始有所感觸，所以也趁著這機會記錄一下JS相關的function應用：

{% codeblock 一般使用 lang:js %}
function godzilla(food){
    console.log('I eat the '+food);
    console.log('I am a monster!');
    } 
    // godzilla('fish');
    //I eat the fish   
    //I am a monster！
{% endcodeblock %}    
    
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;這是最基本的使用方式，跟其他語言大致相當
<!-- more -->
---

##匿名Function
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;也就是所謂的匿名函式，為什麼稱作匿名呢？那是因為對JS來說function本身可以視為一個物件（它也確實是個物件），而我們把JS的function視為一個可傳遞的值或物件，所以我們也就可以把它傳入一個變數或是當做參數使用：

 {% codeblock lang:js %}
 var godzilla = function(food){
   console.log('I eat the '+food);
   console.log('I am a monster!');
 }
 typeof godzilla 
 // "function"
 godzilla('fish');
   //I eat the fish   
   //I am a monster！
{% endcodeblock %}
<br>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;當然，可以塞入一個變數，那我們就可以像是變數一樣的任意使用它，又或著是把它來當做參數給另一個變數做callback使用：

   
 {% codeblock lang:js %}
function monstersShow(monster, godzilla){
  console.log('I am the '+ monster);
  godzilla();
  }
monstersShow('KingKong', godzilla);
// I am the KingKong 
// I eat the undefined
// I am a monster! 
 {% endcodeblock  %}
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;上述作法其實比較多此一舉，因為既然它是可以是個物件，那其實在當參數使用的時候也不見得需要在把它先塞到一的變數，而是在呼叫該function的時候直接定義欲傳入function，所以我們可以直接寫：

 {% codeblock lang:js %}
monstersShow('KingKong', 
  function(food){
    console.log('I eat the '+food);
    console.log('I am a monster!');
  }
);
 {% endcodeblock %}
<br>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;直接這樣寫也可以work，而且這種作法在JQuery的event handler相當常見；此外在ruby時，我們所使用的Proc, lambda其實就是承襲JS的匿名函式過來的，當然啦，當初Matz是不是真的被JS啟發我也不確定啦～

 如果要更謹慎地使用callback，可以這樣：

   
{% codeblock lang:js %}
   function godzilla(food, callback){
     console.log('I want to eat '+ food);
     if( typeof(callback) == 'function'){ //判斷callback是否為function
       console.log('callback is actually a function, then we would execute it..');
       callback();
      }
   }
  godzilla('fish',function(){ console.log('Dear Doctort, you made it!'); });
  // 輸出
  // I want to eat fish
  // Dear Doctort, you made it!
   {% endcodeblock %}
<br>   
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更理解了嗎？其實callback就只是個參數代稱而已，你想怎麼稱呼它都可以，原理是你只要想像你欲傳遞的function全都被指定給callback，然後再以callback()來使用傳遞進去的function。


---
##Function的參數陣列arugments
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;在JS中的function其實都有個預設好的陣列參數arguments，我們都知道相對於C家族的語言，JS是很自由的，像是在呼叫的function時，即使多傳幾個沒定義的參數，程式也不會噴錯，或是明明設有參數的function，直接不帶參數呼叫該function，也是不會噴錯，只是需要用到該參數的地方會顯示undefined而已。
<br>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;然而，剛剛都只是題外話，JS的function真正酷的地方在那個預設的陣列參數arugments，其實他不是真正的參數也不是個真的陣列，我知道這樣講很抽像，但只要把它想成event在 event handler裡的這個事件物件的參數就很好懂了；而這邊這個arguments也有個屬性叫做length，顧名思義就是你傳入這個function的參數長度，而我們傳入的參數其實也可以藉由arguments[i]來呼叫。所以也可以把它想做你傳入的參數其實全都存到這個arguments陣列裡去了，那我們在定義一個JS的function時，其實你不用事先定義任何傳入的參數也可以，然後可以直接靠arguments來取用你強行塞進去這個function裡的參數，但是這樣做不見得是個好方法就是，因為你必須很清楚你傳入的參數位置為何，老是這樣argument[0], argument[1]的呼叫似乎有點不易閱讀；直接看個例子吧：

{% codeblock lang:js %} 
function arrayPlus(){
  var total = 0;
  for(i=0;i<arguments.length;i++){
    total += arguments[i];
  }  
  return total; 
}
console.log(arrayPlus(1,2,3,4,5)
// 15
 {% endcodeblock %}
<br>  
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;arguments當然還有其他功用，但這就得自己多去摸索了，如果想更詳細了解arguments也可以去查文件囉！
<br>



---
##進階範例：運用Prototype和Callback來自定forEach給Array
<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;在JQuery跟Ruby裡都已經預設有forEach或each等方便給array或hash使用的方法，簡單地說，此function會直接迭代呼叫它的array或hash內的元素並傳給它本身的callback方法，像是[1,2,3,4,5].forEach(function(element){ console.log(element);} )，其中傳給forEach的參數匿名function正是之前提到的callback方法（一般來說就是可當參數傳入使用的方法），而該function的參數element正是被迭代入的array或hash元素。
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;這個forEach等會自動走訪array或hash的元素並且讓他們執行某callback是種直覺右方變得用法，在以前的JAVA或PHP要做到同樣的事寫起來就比較麻煩，code會多好幾行甚至可能數以倍計，而到底這種方便的方法是怎麼被實作入JS的勒，用以下的範例來試試看吧！！在之中我們會先用到prototype這物件來實現JS的繼承功能（把想被繼承的方法或屬性塞入給prototype就可以了，更深入了解prototype將會在另外討論）:
 {% codeblock lang:js %}
Array.prototype.myEach = function(callback){
  for(i=0;i<this.length;i++){
  callback(this[i]);
  }
};//這邊是先定義myEach並且透過prototype指定給Array，讓之後所有的Array都可以用此方法
[1,2,3,4,5].myEach(function(element){
  console.log(element);
});
//輸出
//1
//2
//3
//4
//5
 {% endcodeblock  %}
<br>     
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;這是我之前的高手同事建議我如果有興趣可以去深入探討的地方，可以去多研究Design Patterns，看看JQuery或是JS一些方便、優秀的功能是如何實作出的，對JS的功力會有所幫助！
<br>
     
---
##Function的其他使用 - 自我調用（Self-Invoking）
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;簡單地說就是宣告完function便利及執行，而且該function只會執行一次，之後無法在用，有點像oop的建構子的概念:
 {% codeblock lang:js %}
(function(drink){
  console.log('I like to have a cup of '+ drink);
})('ice tea');
// 輸出
// 'I like to have a cup of ice tea'
 {% endcodeblock %}
 <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;這有什麼用處呢？一般是用來初始化或是只需執行一次的任務，但其實本人自己也沒用過幾次，所以就先學起來吧。
 {% codeblock lang:js %}
var a = function(drink){
  console.log('I like to hava a cup of '+ drink);
}('ice tea');
// 輸出
// I like to have a cup of ice tea
 {% endcodeblock %}
 <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;這樣寫也work，但是好像更多此一舉了點就是。

---
##Function的其他使用 - 內部函數
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;簡單地說，就是function裡面又定義function，而在內部的function就稱為內部或私有function，不能被直接呼叫，而內部function的範圍內的變數也無法被外部function所存取使用。另一方面，對於內部function來說，則有發生closure（閉包）的機會，就是內部function可以存取外部function的閒置變數，拿近來使用，因此延長了該閒置變數的存活期間，詳細的closure介紹與使用如果有機會再另外做記錄，這裡先來給個簡單的範例：

 {% codeblock lang:js %}
function outer(){
  var outerSpace = 'space godzilla';
  console.log(outerSpace + 'is a super monster!');
  function inner(){
    var innerSpace = 'Just godzilla';
    console.log(innerSpace + 'is local monster and ' + outerSpace + 'is not!');
  }
  inner();
  return inner;
}
outer();
// 輸出
// space godzillais a super monster!
// Just godzillais local monster and space godzillais not! 
inner();
// ReferenceError: inner is not defined   失敗
var getInner = outer();
// space godzillais a super monster!                                             
// Just godzillais local monster and space godzillais not! 
getInner();
// Just godzillais local monster and space godzillais not! 
outer()();
// space godzillais a super monster!
// Just godzillais local monster and space godzillais not! 
// Just godzillais local monster and space godzillais not!     多出現了一次
 {% endcodeblock %}
<br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;來解釋吧，當第一次直接呼叫outer()時，結果很直覺的就是跑了它該跑的。那我想直接呼叫inner的話勒，則會顯示失敗的錯誤訊息，因為內部function不能直接使用。然後接著我再17行的地方又把outer()的回傳結果傳給變數getInner，因為第9行outer有定義return Inner，所以此時getInner就代表Inner了，所以可以直接使用！！而至於為何會有18,19行的關係是因為小弟這隻範例程式寫的不夠好，因為只要呼叫到outer();無論如何都會先執行一次啊，所以就會有那兩行結果。而最後，第22行是直接靠outer呼叫來執行Inner的結果，但這種作法其實會先執行一次outer本身後才跑Inner，所以第25行才會又出現一次。
<br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;其實回顧一下第6行的outerSpace，它是屬於外部function的變數，但是仍然可以被內部function使用，也因此延長它的存活時間，這算是種closure的基本例子。而這outerSpace對內部function來說我們可以稱作為閒置變數，你也可以覆寫它，然而因為JS的closure是綁住變數本身而非變數的值，所以一旦覆寫了話，當然也會連動影響到外部function的outerSpace，範例如下：

  
 {% codeblock lang:js %}
function outer(){
  var outerSpace = 'space godzilla';
  console.log(outerSpace + 'is a super monster!');
  function inner(){
    var innerSpace = 'Just godzilla';
    console.log(innerSpace + 'is local monster and ' + outerSpace + 'is not!');
    outerSpace = 'space godzilla is changed from inner!'
  }
  inner();
  console.log(outerSpace);
  return inner;
}
outer();
// 輸出
// space godzilla is a super monster!
// Just godzilla is local monster and space godzilla is not!
// space godzilla is changed from inner!
 {% endcodeblock %}
<br>
在第7行的地方又覆寫了一次outSpace所以最後outer輸出的outerSpace也跟著被改變了！
<br>
###目前關於Function的部份就先寫到這，期待下篇應用再繼續努力，有問題或錯誤的話歡迎指正，感謝～！

  

