基于Yaf开发高性能的API接口
===============
> 🚀 接近原生,去除复杂的框架功能。
## 特性

- 原生PDO实现CRUD

- 原生实现分页

- 基于云信实现短信发送

- 基于个推实现APP消息推送

- 创建微信付款二维码

> Yaf最新版的运行环境要求PHP7以上。


## 要求

| 依赖 | 说明 |
| -------- | -------- |
| PHP| `>= 7.0.0` |
| Mysql| `>= 5.5` ||
| nginx |用于网址代理解析|

## 注意
1. 数据库结构的SQL文件已经放在项目中,需要自行导入.

2. PostMan的API接口文件也已经放在项目中,需要自行导入.

3. **yaf扩展请自行安装**

## 安装

1. 通过[Github](https://github.com/guaosi/yaf-api-demo),fork到自己的项目下
```
git clone git@github.com:<你的用户名>/yaf-api-demo.git
```
2. 在application/models/Push.php 填写个推相关账户信息
```
define('APPKEY','');
define('APPID','');
define('MASTERSECRET','');
```
3. 在application/models/Sms.php 填写云信相关账户信息
```
//接口账号
$smsuid = '';
//登录密码
$smspwd = '';
```

## nginx重写规则(官方文档有坑)
```
if (!-e $request_filename) {
    rewrite ^/(.*)  /index.php?$1 last;
  }
```

## 流程
1. 先进入入口文件index.php
2. 再进入Bootstrap.php,做一些相关操作，如路由分发
3. 根据路由找到控制器
4. 返回结果

## 路由配置
选择官网8.8的路由
```
     $router = Yaf_Dispatcher::getInstance()->getRouter();
     //创建一个路由协议实例
     $route = new Yaf_Route_Rewrite(
     　　'product/:ident',
     　　array(
     　　　　'controller' => 'products',
     　　　　'action' => 'view'
     　　)
     );
     //使用路由器装载路由协议
     $router->addRoute('product', $route);
```
yaf默认支持RestFul风格的API