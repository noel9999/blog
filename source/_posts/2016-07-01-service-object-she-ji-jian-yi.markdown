---
layout: post
title: "Service Object 設計建議"
date: 2016-07-01 01:56:06 +0800
comments: true
categories: Rails
---
## 前言
隨著大家`ServiceObject`越用越多，也產生了不同的設計習慣與使用方式，為了讓大家有一制的設計原則，方便後人使用或減少修改成本、遵行風格建立下去，我們可以來集思廣益、訂立一套良好的準則，希望大家可以遵守之，並且耳濡目染之下相互影響。當然，如果有發現不良的地方或更好的方式都歡迎提出討論。

<!--more-->
## 命名方式
具體的動名詞或名詞 ＋ `Service`
範例:

1. `FireFighterService`
2. `SendMessageService`

一個Service最好只做一件事情以遵守`SRP`，所以如果彼此有共同的對象或元素可以增加`namespace`，並分拆：

1. `SMS::SendMessageService`
2. `SMS::SendImageMessageService`

## 越少的公開方法越好
為了遵守`SRP`的特性，公開的方法越少越好，甚至希望只有一個 公開的`execute`
方法，這樣的目的較好測試（通常也僅需測試該方法）、也不會混淆使用者該`ServiceObject`的用途，如果有多個公開方法，則可以思考是不是該分拆別的`ServiceObject`出去或是該方法到底可以被誰或什麼情況呼叫；而其他的方法都該設為`private`，一來使用者不需要知道（想想餐廳點餐的故事）、二來此方法日後也有變動或拓展的可能所以無需給他人知道。

> 統一的公開介面：`execute`> 

## 參數的設計
由於每個`ServiceObject`的使用目的與前後文都不盡相同，所以參數的設計應該盡可能的減少限制或是會變成過度設計的可能。但這裡提供幾個基本的建議：
### 參數的位置
理想的`ServiceObject`使用過程中只會經過`new`跟`execute`，所以參數位置也只會出現在這兩處：
#### 宣告在`initialize`
1. 關於需要功能初始化的設定性質的參數、或是該參數內容變動的可能性極低時；如果初始化的參數日後變動需求很低時，也可以不需要當做參數，而直接定義
3. 如果有需要使用到其他的物件或類別時，可以當做參數傳入，以減少相依性問題；該物件或類別當然也可以不經參數傳入而直接考慮隔離成一個`preivate`方法，依日後需求變化而定，見仁見智。

#### 宣告在`execute`
1. 關於資料處理範圍、特定處理對象
2. 關於操作範圍、額外功能參數

### 參數宣告
1. 雖然每個人都有自己宣告參數的偏好，但個人認為`hash`是很好的選擇，有明確的key、value，在參數的數量、順序與使用彈性上的較佳，也最簡單。

```
def execute(args = {})
  args = args.symbolize_keys
  player_a = args.fetch(:player_a, 'default_player')
  player_b = args.fetch(:player_b, 'default_player')
  do_something
end
```

## 回傳物件設計
回傳的資訊若有一致的介面的話，日後與其他object互動也更有彈性、更易開發；而回傳的資訊又有幾項要點：
1. 一個表示執行成功或失敗的布林值
2. 當`ServiceObject`涉及到資料的處理或更新時，我們關心的是最後的回傳資料結果
3. 若執行失敗或錯誤發生時，關於失敗的原因、或更進一步的資訊

```ruby
  SuccessfulResult = Struct.new :payload do
    def self.create(payload: nil)
      fail 'payload must have to respond_to method "as_json".' unless payload.respond_to? :as_json
      new(payload)
    end

    def success?
      true
    end

    def as_json(*)
      {
        success: true,
        payload: payload.as_json
      }
    end

    def error
      nil
    end
  end

  FailedResult = Struct.new :error do
    def self.create(error: ServiceObjectError.new)
      new(error)
    end

    def success?
      false
    end

    def as_json(*)
      {
        success: false,
        error: error.as_json
      }
    end
    
    def payload
      nil
    end
  end
```

然後我們可以這樣用：
```ruby
  def execute(args ={})
    do_something
    SuccessfulResult.create payload: data_to_return_or_not
  rescue ServiceObjectError => e
    FailedResult.create error: e
  end
```

這樣我們可以對結果這樣使用：
```ruby
	result = SomeService.new.execute
	if result.success?
	  do_something_with result.payload
	else
	  handle_with result.error
	end
```
### 回傳的rsult_object的介面
1. `success?`  ＝> 判斷執行成功或失敗
2. `payload` => 當執行成功，且有資料結果需要回傳做使用時，會藉由此來取得
3. `error` => 當執行失敗的時候，可透過該方法來取得資訊，e.g.: `error.code`, `error.message`；error的介面會另外寫一個準則
4. `as_json` => 進一步的化成`JSON`型態回傳


## 當存在數個外部服務可提供相同服務的ServiceObject，可考慮使用 `Adapter Pattern`

以寄信為例，同時會有多個第三方服務提供，而使用上的目的幾乎都是相同的，不外都是寄信、寄多筆信等，對使用者來說都是一樣的角色，簡訊發送商，為此我們不需寫成  `Goolge::SendMailService`, `Yahoo::SendMailService`，更好的方式應該是`SendMailService.new(provider: 'google').execute`

### 簡易的範例：
```ruby
class SendMailService
  def initialize(args = {})
    provider = args.symbolize_keys.fetch(:provider, 'google')
    @mail_provider = MailProvider::Adapter.const_get(provider.to_s.capitalize).new
  end

  def execute(args ={})
    mail_provider.send_mail(args)
  end

  private

  accessor_reader :mail_provider
end

module MailProvider
  module Adapter
    class Google
      # 遵守一致寄信服務商的介面
      include ActsAsSendMailProvider

      def initialize
        google_id     = 'hooli.xyz'
        google_secret = 'hooli.xyz'
        @provider = Goolge.come_out_with(google_id, google_secret)
      end

      def send_mail(args = {})
        @provider.do_something_by_google
      end
    end
  end
end

module MailProvider
  module Adapter
    class Yahoo
      # 遵守一致寄信服務商的介面
      include ActsAsSendMailProvider

      def initialize
        yahoo_id     = 'hooli.xyz'
        yahoo_secret = 'hooli.xyz'
        @provider = Yahoo.come_out_with(yahoo_id, yahoo_secret)
      end

      def send_mail(args = {})
        @provider.do_something_by_yahoo
      end
    end
  end
end
```
---
## 實驗性範例，目前僅供參考~~（如有問題，不負責任）~~

### 建立一個`ActsAsServiceObject`module，當做該介面的角色
```ruby
module ActsAsServiceObject
  SuccessfulResult = Struct.new :payload do
    def self.create(payload: nil)
      fail 'payload must have to respond_to method "as_json".' unless payload.respond_to? :as_json
      new(payload)
    end

    def success?
      true
    end

    def as_json(*)
      {
        success: true,
        payload: payload.as_json
      }
    end

    def error
      nil
    end
  end

  FailedResult = Struct.new :error do
    def self.create(error: ServiceObjectError.new)
      new(error)
    end

    def success?
      false
    end

    def as_json(*)
      {
        success: false,
        error: error.as_json
      }
    end

    def payload
      nil
    end
  end


  class ServiceObjectError < StandardError
    attr_reader :caused_by
    def initialize(message = nil, caused_by: nil)
      super(message)
      @caused_by = caused_by
    end

    alias_method :original_message, :message

    def code
      self.class.name
    end

    def as_json(*)
      { message: message, code: code }
    end
  end

  # 唯一的對外公開方法，統一都傳hash當做參數，基本上include該module不需特別覆寫此方法  
    def execute(args = {})
    SuccessfulResult.create payload: process(args)
  rescue ServiceObjectError => e
    FailedResult.create error: e
  end

  private

 # include該module的類別，唯一需要自己實作的方法，預設都需一個args型態為hash的參數
 # 而此方法的最後回傳值會被當做SuccessfulResult物件的payload
 # 而當方法裡有任何繼承ServiceObjectError的例外發生都會被捕捉成FailedObject
 def process(args = {})
    fail 'my son will take care of you'
  end
end
```
### include `ActsAsServiceObject`並實作method `process`
```ruby
class MyTestServiceObject
  include ActsAsServiceObject

  private
  
  def process(args = {})
    args = default_process_args.merge args.symbolize_keys
    required_variable_a, required_variable_b = args.values_at(:required_variable_a, :required_variable_b)
    do_something
    data_you_want_to_return_in_payload_or_not
  end

  def default_process_args
    { required_variable_a: 'A', required_variable_b: 'B', required_variable_c: 'C' }
  end
end
```

### 說明：
照這種方式，只需實作`process`方法，而該方法的回傳值會是`SuccessfulResult`的payload；此外，不需自己實作`execute`，只需記得`execute`與`process`的參數都是接收一個`hash`，實際上`execute`就是在做`process`的工作，只是會多幫忙處理一致化回傳物件的工作，若要覆寫`execute`方法，就必須自己記得處理這一塊。

而process裡若是有任何與預期不符的結果，引發一個繼承自`ServiceObjectError`的error的話就會自動被捕作並且回傳`FailedResult`物件。

```ruby
my_result = MyTestServiceObject.new.execute(foo: 'bar')
my_result.success?
my_result.payload
my_result.error
my_result.as_json
```

## 有任何問題或更好的建議都歡迎提出指正，謝謝

## 參考：
1. [SERVICE OBJECT設計建議](https://www.sitepoint.com/ruby-error-handling-beyond-basics/)
2. [SERVICE_OBJECT設計建議2](https://www.netguru.co/blog/service-objects-in-rails-will-help)
3. [調適者模式](https://www.sitepoint.com/using-and-testing-the-adapter-design-pattern/)