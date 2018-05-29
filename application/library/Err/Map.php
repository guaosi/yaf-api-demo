<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/11/011
 * Time: 10:26
 */
class Err_Map{
    private static $errMap=[
        0=>'',
        1001=>'请通过正确渠道提交',
        1002=>'用户名与密码必须传递',
        1003=>'用户名不存在',
        1004=>'密码错误',
        1005=>'用户名已经存在',
        1006=>'密码不能小于8位',
        1007=>'注册失败，写入数据错误'
    ];
    public static function get($code){
        if (!array_key_exists($code,self::$errMap)){
          return [
              'errno'=>0-$code,
              'errmsg'=>'The code is undefined'
          ];
        }else{
            return [
              'errno'=>0-$code,
                'errmsg'=>self::$errMap[$code]
            ];
        }

    }
}