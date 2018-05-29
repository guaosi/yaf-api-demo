<?php
require __DIR__ . '/../vendor/autoload.php';
use \Curl\Curl;

$host = "http://127.0.0.1/?c=mail";
$curl = new Curl();
$uid = 14;
$title = '测试邮件'.rand(10000,99999);
$contents = "这事一封测试邮件，请忽略。";

/**
 * 注册用户
 */
$curl->post( $host."&a=send&submit=1", array(
			'uid' => $uid,
			'title'	=> $title,
			'contents'=> $contents,
		));
if ($curl->error) {
    die( 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n" );
} else {
	$rep = json_decode( $curl->response, true );
	if( $rep['errno']!==0 ) {
		die( '邮件发送失败。错误信息:'.$rep['errmsg']."\n" );
	}
	echo "邮件发送成功！请去邮箱看看，是否收到信件。\n";
}

echo "邮件接口测试完毕。\n";



