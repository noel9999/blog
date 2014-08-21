---
layout: post
title: "Javascript Object and Class"
date: 2014-08-22 00:08:21 +0800
comments: true
categories: javascript
---


&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;js也是個物件導向（object－oriented）的語言，但與我們傳統認知的C＋＋、Java的物件導向有所差異，但至少理念上還是一樣。物件會有所謂的成員也有人稱作屬性（property或attribute），指的是屬於該物件的某種數值或字串又或是其他的物件（ex: argument.length, event.data）。另外，物件也有方法（method，就是我們認知的function，ex: location.href(somefile.url) ），名稱上或許容易令人混淆，但大致來說一般的物件導向都是這麼稱呼與認知的。
<!-- more -->

##前言


&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;js如我們所知是萬物皆物件的語言，但更進一步得講，其實***並非所有萬物皆為物件***，像是"foo", 5, false等就不是物件，而是原始值，但我們依然可以對它們操作"foo".length，難道它不是個物件嗎？其實這是js在我們使用原始值時會先幫我們把它進一步包裝成複合物件，等我們使用完後又會再釋放，當然我們在使用它的時候是感覺不到的，所以***更正確的說法是，js裡所有的東西使用起來皆像物件！***

   

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;一般來說我們如果是 var myArray = []; 來宣告一個陣列物件，其實就隱含了我們做了var myArray = new Array();的用法而已，同理 var myObject = {} 也隱含了 var myObject = new Object()；而剛剛我們提到字串、數字、布林值不是物件而是原始值，但如果我們是用 var myString = new String("Godzilla")來產生字串的話，此時的myString就是物件了而非原始值了！！我們可以利用typeof 來判斷是否為物件，但function物件顯示的結果會是function而非object，但他依然是個物件唷！！如果想知道一物件是由誰所建構的則可以利用 constructor:

{% codeblock 範例一 lang:js %}
var myString = 'godzilla'
typeof myString; // string
myString.constructor; // String
var myStringObject = new String('godzilla');
typeof myStringObject; // object  此時是物件而非原始值
myStringObject.constructor); // String
var myArray = [1,2]
typeof myArray // object
myArray.constructor // Array
var myArrayObject = new Array(1,2);
typeof myArrayObject // object
myArrayObject.constructor // Array
var myFunction = function(){ alert('haha') }
tpyeof myFunction // function 注意
myFunction.constructor // Function
var myFunctionObject = new Function('name','return name;');
typeof myFunctionObject // function 注意
myFunction.constructor  // Function
{% endcodeblock %}
 

 以上，大概是對js的物件基本介紹，這些有什麼用呢？其實個實際上並不能帶給你什麼酷炫的方式，但是對於觀念的釐清是很重要的！

 
---
##自定類別

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;js並無class，所以js的物件定義方使是靠關鍵字`function`來實現，跟我們一般直接定義一個function很類似：


{% codeblock 範例二 lang:js %}
function godzilla(a,b){ // godzilla只是個單純的function
console.log(a);
var mySon = new Son(b); // 利用function Son建立物件
console.log('I am ' + mySon.name);
mySon.changeName('zilla');
console.log('Here comes my new name: ' + mySon.name);
}

//利用function來制定我們想要的物件
function Son(name){
  this.name = name;
  this.changeName = function(newName){
  this.name = newName;
  }
}
godzilla('I am in a function rather than a object!', 'Noel');
//輸出
//I am in a function rather than a object! 
//Noel
//zilla 
  利用Object建構函式來產生物件

  

  var eva = new Object();
  eva.pilot = 'true four';
  eva.color = 'purple';
  eva.changePilot = function(person){
    this.pilot = person;
    }
  eva.pilot // true four
  eva.color // purple
  eva.changePilot('zero');
  eva.pilot // zero 
{% endcodeblock %}