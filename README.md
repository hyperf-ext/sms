# Hyperf 短信发送组件

## 简介

组件实现了一套干净、简洁的 API ，并为诸多第三方短信发送服务提供了相应的驱动，让你可以快速、简单地从您的应用发送短信。驱动基本移植自 [overtrue/easy-sms](https://github.com/overtrue/easy-sms ) ，同时借鉴了其网关策略并进行了扩展。

## 安装

```shell script
composer require hyperf-ext/sms
```

## 配置

### 发布配置

```shell script
php bin/hyperf.php vendor:publish hyperf-ext/sms
```

### 发送器配置

配置文件中的 `senders` 节点配置的每个发送器配置都有自己的「驱动」和配置选项，这将允许你的应用程序使用不同的短信服务来发送特定的消息。例如，你的应用程序可能使用阿里云发送验证码类消息，而使用腾讯云发送通知类消息。再或者为同类消息配置多个发送器来提高服务可用性。

#### [阿里云](https://www.aliyun.com/product/sms)

短信消息类可用配置内容方法：`template`，`with`，`signature`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\AliyunDriver::class,
    'config' => [
        'access_key_id' => '',
        'access_key_secret' => '',
        'sign_name' => '',
    ],
]
```

#### [百度智能云](https://cloud.baidu.com/product/sms.html)

短信消息类可用配置内容方法：`template`，`with`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\BaiduCloudDriver::class,
    'config' => [
        'ak' => '',
        'sk' => '',
        'invoke_id' => '',
        'domain' => '',
    ],
]
```

#### [华为云](https://www.huaweicloud.com/product/msgsms.html)

短信消息类可用配置内容方法：`template`，`with`，`from`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\HuaweiCloudDriver::class,
    'config' => [
        'endpoint' => '', // 从管理控制台获取到的 App 接入地址
        'app_key' => '',
        'app_secret' => '',
        'from' => [
            'default' => '', // 默认签名通道号
            // 'another' => '', // 其他签名通道号
        ],
    ],
]
```

#### [聚合数据](https://www.juhe.cn/docs/api/id/54)

短信消息类可用配置内容方法：`template`，`with`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\JuheDataDriver::class,
    'config' => [
       'app_key' => '',
    ],
]
```

#### [螺丝帽](https://luosimao.com/service/sms)

短信消息类可用配置内容方法：`content`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\LuosimaoDriver::class,
    'config' => [
       'app_key' => '',
    ],
]
```

#### [七牛云](https://www.qiniu.com/products/sms)

短信消息类可用配置内容方法：`template`，`with`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\QiniuDriver::class,
    'config' => [
        'secret_key' => '',
        'access_key' => '',
    ],
]
```

#### [融云](https://www.rongcloud.cn/product/sms)

短信消息类可用配置内容方法：`template`，`with`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\RongCloudDriver::class,
    'config' => [
        'app_key' => '',
        'app_secret' => '',
    ],
]
```

#### [容联云通讯](https://www.yuntongxun.com/api/sms.html)

短信消息类可用配置内容方法：`template`，`with`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\RonglianDriver::class,
    'config' => [
        'app_id' => '',
        'account_sid' => '',
        'account_token' => '',
        'is_sub_account' => false,
    ],
]
```

#### [SendCloud](https://sendcloud.sohu.com/sms.html)

短信消息类可用配置内容方法：`template`，`with`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\SendCloudDriver::class,
    'config' => [
        'sms_user' => '',
        'sms_key' => '',
        'timestamp' => false, // 是否启用时间戳
    ],
]
```

#### [短信宝](http://www.smsbao.com/)

短信消息类可用配置内容方法：`template`，`with`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\SmsBaoDriver::class,
    'config' => [
        'user' => '',
        'password' => '',
    ],
]
```

#### [腾讯云](https://cloud.tencent.com/product/sms)

短信消息类可用配置内容方法：`template`，`with`，`signature`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\TencentCloudDriver::class,
    'config' => [
        'sdk_app_id' => '',
        'secret_id' => '',
        'secret_key' => '',
        'sign' => null, // 短信签名
        'from' => [ // SenderId，中国大陆地区无需配置
            'default' => '', // 默认 SenderId
            // 'another' => '', // 其他 SenderId
        ],
    ],
]
```

#### [Twillo](https://www.twilio.com/sms)

短信消息类可用配置内容方法：`content`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\TwilioDriver::class,
    'config' => [
        'account_sid' => '',
        'token' => '',
        'from' => [
            'default' => '',
            // 'another' => '',
        ],
    ],
]
```

#### [UCloud](https://www.ucloud.cn/site/product/usms.html)

短信消息类可用配置内容方法：`template`、`with`、`signature`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\UCloudDriver::class,
    'config' => [
        'private_key' => '',
        'public_key' => '',
        'sig_content' => '', // 短信签名
        'project_id' => '', // 项目ID,子账号才需要该参数
    ],
]
```

#### [云片](https://www.yunpian.com/product/domestic-sms)

短信消息类可用配置内容方法：`content`、`signature`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\YunpianDriver::class,
    'config' => [
        'api_key' => '',
        'signature' => '', // 短信签名，内容中无签名时使用
    ],
]
```

#### [网易云信](https://yunxin.163.com/sms)

短信消息类可用配置内容方法：`template`，`with`

```php
[
    'driver' => \HyperfExt\Sms\Drivers\YunxinDriver::class,
    'config' => [
       'app_key' => '',
       'app_secret' => '',
       'code_length' => 4, // 随机验证码长度，范围 4～10，默认为 4
       'need_up' => false, // 是否需要支持短信上行
    ],
]
```

## 生成短信消息类

应用发送的每种消息都被表示为短信消息类。这些类存储于 `app/Sms` 目录中。如果您的应用中没有该目录，别慌，当您使用 `gen:sms` 命令生成您的首个短信消息类时，应用将会自动创建它：

```shell script
php bin/hyperf.php gen:sms VerificationCode
```

## 编写短信消息类

所有的短信消息类的配置都在 `build` 方法中完成。您可以通过调用诸如 `content`、`template`、`signature` 和 `from` 这样的各种各样的方法来配置消息的内容及其发送。

### 配置发送器

在发送消息前，您需要指定哪些发送器可被用于发送消息。有两种方式来指定可用发送器。

#### 通过配置文件

您可以通过配置文件的 `default.senders` 节点来指定默认的被用于发送消息的发送器，值类型可以是 `array` 或 `string`。

```php
[
    'default' => [
        'senders' => ['aliyun', 'twillo'],
    ],
]
```

#### 通过 SmsMessage 类属性

您也可以通过短信消息类的 `senders` 公开属性来指定可用发送器，如果您未在短信消息类中指定，那么在发送时会自动使用配置文件中指定的发送器。

```php
use HyperfExt\Sms\SmsMessage;

class VerificationCode extends SmsMessage
{
    public $senders = ['aliyun', 'twillo'];
}
```

#### 指定地区限定的发送器

由于短信服务提供商所服务的地区是有限的，亦或是因各地区价格因素的考量，您可能会为此使用多个提供商来让您的业务覆盖尽可能多的地区，此时您可为指定地区的号码配置指定的发送器。

```php
[
    'aliyun' => ['cn', 'hk', 'mo', 'tw'], // 仅当手机号是大陆和港澳台地区时使用阿里云和腾讯云
    'tencent_cloud' => ['cn', 'hk', 'mo', 'tw'],
    'twillo', // 大陆和港澳台地区以外使用 Twillo
]
```

> 地区代码使用 ISO 3166-1 两位地区代码，不区分大小写。
>
> 发送器的筛选逻辑在内建的发送器策略中实现，您可以通过*自定义发送器策略*来根据自身需求实现自己的处理逻辑。

### 配置发送器策略

发送器策略是在使用多个发送器的情况下，用来确定发信程序以何种顺序来选择发送器的排序和筛选程序。当一个发送器发送失败后会根据顺序选择下一个发送器，直到发送成功。

组件内建 `OrderStrategy` 和 `RandomStrategy` 两个默认的策略。`OrderStrategy` 策略依照您指定发送器的顺序来选择发送器。`RandomStrategy` 策略则将发送器列表打乱来随机排序。

就像配置发送器一样，发送器策略有着相同的两种配置方式。配置文件的 `default.strategy` 节点，或短信消息类的 `strategy` 公开属性。

#### 自定义发送器策略

您也可以自定义发送器策略，只需实现 `StrategyInterface` 接口。该接口只有一个 `apply` 方法，接受两个参数。第一个参数接受您配置的可用发送器列表，第二个参数接受要收信的手机号码 `MobileNumber` 类。

### 配置内容

您可以在短信消息类的 `build` 方法中使用 `content` 方法指定短信内容，使用 `template` 方法来指定在短信模板，使用 `with` 方法来指定短信参数，使用 `signature` 方法来指定短信签名。

您需要根据您选择的短信服务的不同来选择使用哪些方法。

#### 通过 `content` 方法

```php
public function build(SenderInterface $sender): void
{
    return $this
        ->content('您的验证码是 123456')
        ->signature('【HyperfExt】');
}
```

#### 通过 `template` 方法

```php
public function build(SenderInterface $sender): void
{
    return $this
        ->template('SMS_001')
        ->signature('【HyperfExt】')
        ->with('code', '123456');
}
```

### 配置发件人 ID

某些短信业务提供商可能会要求传递发件人 ID（一般称为 `sender id` 或 `from`），您可以在对应发送器下的 `config.from` 节点内配置，并通过短信消息类的 `from` 方法使用。以 Twillo 为例：

```php
// 在配置文件中指定
'twillo' => [
    'driver' => \HyperfExt\Sms\Drivers\TwilioDriver::class,
    'config' => [
        'account_sid' => '',
        'token' => '',
        'from' => [
            'default' => '123', // 当未在短信消息类中指定时使用 `default` 的值
            'another1' => '456',
            'another2' => '789',
        ],
    ],
],

// 在短信消息类中使用
public function build(SenderInterface $sender): void
{
    return $this
        ->from('another2');
}
```

## 发送短信

若要发送短信，使用 `Sms` 辅助类的 `to` 方法。`to` 方法接受手机号码和实现了 `HasMobileNumber` 接口的实例。一旦指定了收信人，就可以将短信消息类实例传递给 `send` 方法：

```php
<?php

namespace App\Controller;

use App\Sms\VerificationCode;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use HyperfExt\Sms\Sms;

class VerificationController
{
    public function send(RequestInterface $request): ResponseInterface
    {
        // 假定 $user 已经实现了 HasMobileNumber 接口
        $user = $request->user();
        // 发送
        Sms::to($user)->send(new VerificationCode());
    }
}
```

### 通过特定的发送器发送短信

默认情况下，组件将使用你配置的发送器来发送短信。但是，你可以使用 `sender` 方法通过特定的发送器发送：

```php
Sms::sender('twillo')
    ->to($request->user())
    ->send(new VerificationCode());
```

### 短信队列

#### 将短信消息加入队列

由于发送短信消息可能大幅度增加应用的响应时间，许多开发者选择将短信消息加入队列放在后台发送。组件使用 [`hyperf/async-queue`](https://hyperf.wiki/2.0/#/zh-cn/async-queue) 简化了这一工作，安装时已经自动依赖，请根据[文档](https://hyperf.wiki/2.0/#/zh-cn/async-queue )进行配置。

若要将短信消息加入队列，可以在指定消息的接收者后，使用 `Sms` 辅助类的 `queue` 方法：

```php
Sms::to($request->user())
    ->queue(new VerificationCode());
```

此方法自动将作业推送到队列中以便消息在后台发送。使用此特性之前，需要[配置队列](https://hyperf.wiki/2.0/#/zh-cn/async-queue )。

> 如果要将短信推送到指定队列，可以通过设置 `queue` 方法的第二个参数实现。

#### 延迟消息队列

想要延迟发送队列化的短信消息，可以使用 `later` 方法。`later` 方法的第二个参数是标示消息延后多少秒后发送：

```php
Sms::to($request->user())
    ->later(new VerificationCode(), 300); //延后  5 分钟发送
```

> 如果要将短信推送到指定队列，可以通过设置 `queue` 方法的第二个参数实现。

#### 默认使用队列

如果你希望你的短信类始终使用队列，您可以给短信消息类实现 `HyperfExt\Contract\ShouldQueue` 接口，现在即使你调用了 `send` 方法，短信依旧使用队列的方式发送。另外，如果需要将短信推送到指定队列，可以设置在短信消息类中设置 `queue` 属性。

```php
use HyperfExt\Contract\ShouldQueue;
use HyperfExt\Sms\SmsMessage;

class VerificationCode extends SmsMessage implements ShouldQueue
{
    /**
     * 列队名称。
     *
     * @var string
     */
    public $queue = 'default';
}
```

## 本地开发

当你正在开发一个短信的应用程序时，您可能不想实际地向真实手机号码发送消息。组件提供了在本地开发过程中「禁用」实际发送短信的方法。

### 日志驱动

`log` 发送器驱动不发送短信，而是将所有短信消息写入日志文件用来校验。有关为每个环境配置应用程序的更多信息，请参阅[配置文档](https://hyperf.wiki/2.0/#/zh-cn/logger )。

## 事件

在发送短信消息的时候，组件会触发两个事件。`SmsMessageSending` 事件在发送消息前触发，`SmsMessageSent` 事件在消息发送完成后触发。记住，这些事件都是在短信被**发送**时触发，而不是在队列化的时候。

## 验证器规则

为了有限的减少发送短信到无效的手机号码的情况，组件提供了两个方便使用的验证规则来手机号码的有效性。

> 需要安装配置 [`hyperf/validation`](https://hyperf.wiki/2.0/#/zh-cn/validation) 组件。需要在验证消息的多语言文件中自行添加 `mobile_number` 和 `mobile_number_format` 两条翻译。
>
> 这些验证器验证的*有效性*是指验证手机号码规则的有效，而非验证其真实性。

### mobile_number\[:...regions\]

`mobile_number` 验证规则用来验证手机号码字段值是否有效，可选验证号码所属国家或地区是否在 `regions` 列表中。`regions` 为 [ISO 3166-1 两位字母代码](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2 )（下称「地区代码」）的地区代码列表。

当 `regions` 列表为空且配置文件中默认地区代码为 `null` 时，要验证的号码必须含有 [ITU-T E.164 建议书规定的国家代码](https://en.wikipedia.org/wiki/List_of_country_calling_codes )（下称「国码」），否则将视为无效号码。

当 `regions` 列表为空且配置文件中默认地区代码有值时，无论要验证的号码是否含有国码，都验证此号码是否属于默认地区，适合业务仅面向单一地区的场景。

当 `regions` 列表仅有一个地区代码时，无论要验证的号码是否含有国码、配置文件中默认地区代码是否有值，都验证此号码是否属于该地区。

当 `regions` 列表地区代码数量大于一个时，要验证的号码都必须含有国码，否则将视为无效号码。

> 注意，该验证规则接受任意电话号码格式。如果您需要同时验证地区和格式，请额外使用 `mobile_number_format` 规则。如果仅验证号码有效性和格式，可以只用 `mobile_number_format` 规则。

```
'phone' => mobile_number'
'phone' => mobile_number:cn,hk,mo,tw'
```

### mobile_number_format:format

`mobile_number_format` 验证规则用来验证手机号码字段值是否有效，且号码格式是否符合 `format` 格式。

#### `e164`

验证号码格式是否符合 ITU-T E.164 建议书规定的格式。例如，`+8618812345678`。

#### `international`

验证号码格式是否符合国际拨号格式。例如，`+86 188 1234 5678`。

#### `national[,region]`

验证号码格式是否符合受话地号码格式，不能含有国际冠码和国码。例如，`188 1234 5678`。配置文件中默认地区代码为 `null` 时，必须提供 `region` 值。

#### `rfc3966`

验证号码格式是否符合 RFC3966。例如，`tel:+86-188-1234-5678`。

#### `digits`

验证号码格式是否都是数字。例如，`8618812345678` 或 `18812345678`。

#### 自定义正则表达式

验证号码格式是否匹配此正则表达式。

> 除 `e164`、`digits` 和自定义正则表达式外的验证格式均为 ITU-T E.123 建议书规定的格式。

```
'phone' => mobile_number_format:e164'
```

## TODO

- [ ] 测试用例