<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10/010
 * Time: 15:24
 */
require __DIR__ . '/../vendor/autoload.php';
use Curl\Curl;
$host="http://yaf.com";
$username="apitest_uname_".rand();
$password="apitest_pwd_".rand();
$curl=new Curl();
/**
注册接口验证
 **/
$curl=$curl->post($host.'/user/reg',[
    'name'=>$username,
    'pwd'=>$password
]);
if ($curl->error){
    die("Error :".$curl->error_code . ":" . $curl->error_message);
}
else{
    $rep=json_decode($curl->response,true);

    if ($rep['errno']!==0){
     die("Error : 用户注册失败，注册接口异常。错误信息:".$rep['errmsg'].'\n');
    }
    echo "注册接口测试成功，注册新用户 ：".$username .'\n';
}
/**
登录接口验证
 **/
$curl=$curl->post($host.'/user/login?submit=1',[
    'name'=>$username,
    'pwd'=>$password
]);
if ($curl->error){
    die("Error :".$curl->error_code . ":" . $curl->error_message);
}
else{
    var_dump($curl->response);
    $rep=json_decode($curl->response,true);
    if ($rep['errno']!==0){
        die("Error : 用户登录失败，登录接口异常。错误信息:".$rep['errmsg'].'\n');
    }
    echo "登录接口测试成功，登录用户:".$username.",密码:".$password.'\n';
}
echo 'check down\n';