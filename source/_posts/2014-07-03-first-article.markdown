---
layout: post
title: "first-article"
date: 2014-07-03 12:36:53 +0800
comments: true
categories: 
---

# My First Article using Octopress

***

~~***星期四猴子去考試***~~

第一次使用octopress架blog並且搭配佈署到heroku，參考[高見龍大大](http://blog.eddie.com.tw/2011/10/11/how-to-install-octopress-on-heroku/)的教學，其實架起來比想像的快很多，但是目前還是要習慣octopress的使用方式，跟搞熟heroku到底適不適合開分支合併回到master去，這樣版本控制才可以發揮他的作用阿，如果改錯了什麼，heroku起不來我辛苦寫的文章不就...！？

  接下來希望要來玩玩怎麼把theme改成我理想的樣子，雖然我css根本爛的可以，但是還是有東西可以給我練練了。還要試著把我本來在hackpad的文章一一搬過來，並且希望我可以持續乖乖的寫好blog...。嗯嗯，要做跟學習的事還是好多好多，但是過程是真的很有趣的，加油吧。
<!-- more -->
  
接下來下面讓我來試試markdown的特點吧。


### `怡婷老師生日大快樂～！`

![pic](../images/o0640064012326561387.jpg)

![image](http://blenderartists.org/forum/attachment.php?attachmentid=212851&stc=1&d=1358710824g)

{% img right /images/o0640064012326561387.jpg 350 350 'image' 'images' %}

{% codeblock Time to be Awesome - awesome.rb %}
puts "Awesome!"
{% endcodeblock %}

	puts "Hello World!"
	echo "Hello World";
	console.log("Hello World");
	printf("Hello World");
	cout<<"Hello World";
	System.out.println("Hello World");
	<p>Hello World</p>			

* 1
* 2
* 3
* 4
* 5



{% codeblock 當年第一次寫php，現在看不知道在寫啥鬼 lang:php start:5 %}
	public $date;
	//public $startDay; 
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Chongqing');
		$this->date = date('Y-m-d');
		echo "HAHA!!!";
		//$this->startDay =
	}
	//$dateOffset=0，0就是今天，1是明天，-1是昨天類推
	public function getDateOffSet($dateOffset = 0){
		$endDate = date('Y-m-d');
		if($dateOffset != 0){
			$nDay = $dateOffset;
			$unixDate = strtotime($endDate);
			$strStartDate = $nDay.' days';
			$endDate = date('Y-m-d', strtotime($strStartDate, $unixDate));
		}
		return $endDate;
	}
	//$startDate是從$endDate為起始點算的，一般來說必為負數，-5就是$endDate的5天前。參數$endDate格式為'Y-m-d'，目前必須先從getDateOffSet()取得
	public function getStartDate($startDate, $endDate){
		//$endDate=$this->getDateOffSet($dateOffset);	
		$unixDate = strtotime($endDate);
		$strStartDate = $startDate.' days';
		$startDate = date('Y-m-d', strtotime($strStartDate, $unixDate));
		return $startDate;
	}
	//一次取得$endDate,$startDate，回傳的是一個陣列
	public function getStartToEndDates($startDate = -1, $dateOffset = 0){
		$endDate = date('Y-m-d');
		if($dateOffset != 0){
			$nDay = $dateOffset;
			$unixDate = strtotime($endDate);
			$strStartDate = $nDay.' days';
			$endDate = date('Y-m-d', strtotime($strStartDate, $unixDate));
		}
		$unixDate = strtotime($endDate);
		$strStartDate = $startDate.' days';
		$startDate = date('Y-m-d', strtotime($strStartDate, $unixDate));
		$dates['endDate'] = $endDate;
		$dates['startDate'] = $startDate;
		return $dates;
	}
{% endcodeblock %}

{% include_code modeltest lang:php php/modeltest.php %}


* 6