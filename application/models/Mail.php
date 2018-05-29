<?php

/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
require_once __DIR__ ."/../../vendor/autoload.php";
use Nette\Mail\Message;
class MailModel
{
    public $errno = 0;
    public $errmsg = "错误";
    private $_db = null;

    public function __construct()
    {
        $this->_db = new PDO('mysql:host=127.0.0.1;dbname=yaf;', 'root', 'root');
        /** 设置下面这行 阻止PDO拼接SQL时将 int 0 转为 string 0  **/
        $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    public function send($uid,$title,$contents){

        $query=$this->_db->prepare("select `email` from `user` where `id` = ?");
        $query->execute([$uid]);
        $ret=$query->fetchAll();
        if (!$ret||empty($ret[0]['email'])){
            $this->errno=-3003;
            $this->errmsg="用户邮箱信息查找失败";
            return false;
        }

        $userEmail=$ret[0]['email'];
        if (!filter_var($userEmail,FILTER_VALIDATE_EMAIL)){
            $this->errno=-3004;
            $this->errmsg="用户邮箱信息不符合标准, 邮箱地址为:".$userEmail;
            return false;
        }
        $mail = new Message;
        $mail->setFrom('测试邮件 <>')
            ->addTo($userEmail)
            ->setSubject($title)
            ->setBody($contents);
        $mailer = new Nette\Mail\SmtpMailer([
            'host' => '',
            'username' => '',
            'password' => '',
            'secure' => 'ssl',
        ]);
        $mailer->send($mail);
        return true;
    }
}
