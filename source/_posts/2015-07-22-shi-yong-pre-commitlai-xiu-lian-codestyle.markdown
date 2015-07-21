---
layout: post
title: "使用pre-commit來修煉codestyle"
date: 2015-07-22 01:35:26 +0800
comments: true
categories: [ruby, programming]
---
想寫出有**執行效率**、又**簡潔易讀**、~~又潮~~的ruby codestyle嗎？  

雖然不是人人都有大神幫忙做code review，但慶幸ruby有`rubocop`可以用，裡面可以偵測我們的code是否有符合ruby codestyle的最適規範，而且還可以搭配`pre-commit`，這個gem來幫我們做到每次提交commit前先掃描我們的code，看看是否有符合codestyle，沒有符合的話就不給commit，希望借此養成良好的codestyle！
<!--more-->
### 1. 首先安裝gem
```
gem 'pre-commit', require: false
gem 'rubocop', require: false
```
### 2.新增檔案 rails_project/.robocup.yml

這部份我都是手動新增的，裡面定義了一些基礎的客製化規範，照抄前輩挑選的，大家如果沒特定需求也可以照抄
{% codeblock lang:yml %}
AllCops:
  RunRailsCops: true
  Exclude:
    - db/schema.rb

Metrics/LineLength:
  Max: 120

Metrics/MethodLength:
  Max: 20

Metrics/ClassLength:
  Max: 250

Style/AsciiComments:
  Enabled: false

Style/ClassAndModuleChildren:
  EnforcedStyle: compact

Style/Documentation:
  Enabled: false

Style/IfUnlessModifier:
  Enabled: false
{% endcodeblock %}

### 3.新增檔案 config/pre_commit.yml


{% codeblock lang:yml %}
---
:checks_remove: []
:checks_add:
- :tabs
- :nb_space
- :whitespace
- :merge_conflict
- :debugger
- :pry
- :local
- :jshint
- :console_log
- :migration
- :rubocop
{% endcodeblock %}

### 4. 執行`pre-commit install`
之後，每次開始提交commit就會在terminal下看到執行的結果了！然後就乖乖的養成良好的習慣慢慢改吧，雖然一開始會不習慣，但是之後會發現真的挺受用的！  
如果有看不懂的提示也可以去Google找或是去[ruby-style-guide](https://github.com/bbatsov/ruby-style-guide#underscore-unused-vars)翻閱看看，裡面都有解釋為何這樣設計或是範例。

### 5.補充
若想偷懶強制commit上去可以加上參數`-n`  

`git commit -am 'some message' -n`


### 參考：[github: pre-commit](https://github.com/jish/pre-commit)
