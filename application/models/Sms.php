<?php

/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
class SmsModel
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
    public function send($uid,$templateId){

        $query=$this->_db->prepare("select `mobile` from `user` where `id` = ?");
        $query->execute([$uid]);
        $ret=$query->fetchAll();
        if (!$ret||empty($ret[0]['mobile'])){
            $this->errno=-3003;
            $this->errmsg="用户手机信息查找失败";
            return false;
        }

        $userMobile=$ret[0]['mobile'];
        if (!$userMobile||!is_numeric($userMobile)||strlen($userMobile)!=11){
            $this->errno=-4004;
            $this->errmsg="用户手机信息不符合标准, 手机号为:".(empty($userMobile)?'空':$userMobile);
            return false;
        }
        //接口账号
        $smsuid = '';
        //登录密码
        $smspwd = '';
        $code=rand(1000,9999);
        $ses=new ThirdPart_Sms($smsuid,$smspwd);
        $contentParam = ['code'=>$code];
        $result = $ses->send($userMobile,$contentParam,$templateId);
        if($result['stat']=='100')
        {
            $query=$this->_db->prepare("insert into `sms_record` (`uid`,`contents`,`template`) VALUES ( ? , ? , ?)");
            $ret=$query->execute([$uid,json_encode($contentParam),$templateId]);
            if (!$ret){
                $this->errno=-4005;
                $this->errmsg="消息发送成功,但是发送记录失败";
                return false;
            }
            return true;
        }
        else
        {
           $this->errno=-4005;
           $this->errmsg="发送失败:".$result['stat'].'('.$result['message'].')';
           return false;
        }
    }
}
