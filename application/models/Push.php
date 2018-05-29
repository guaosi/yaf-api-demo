<?php

/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
$pushPath=dirname(__FILE__) .'/../library/ThirdPart/GeTui/';
require_once  ($pushPath . 'IGt.Push.php');
require_once  ($pushPath . 'igetui/IGt.AppMessage.php');
require_once  ($pushPath . 'igetui/IGt.APNPayload.php');
require_once  ($pushPath . 'igetui/template/IGt.BaseTemplate.php');
require_once  ($pushPath . 'IGt.Batch.php');
require_once  ($pushPath . 'igetui/utils/AppConditions.php');

//http的域名
define('HOST','http://sdk.open.api.igexin.com/apiex.htm');


//定义常量, appId、appKey、masterSecret 采用本文档 "第二步 获取访问凭证 "中获得的应用配置
define('APPKEY','');
define('APPID','');
define('MASTERSECRET','');


class PushModel
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
    public function toSingle($cid,$contents="这是一个推送"){
        $igt = new IGeTui(HOST,APPKEY,MASTERSECRET);

        //消息模版：
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
        $template = $this->_IGtNotyPopLoadTemplateDemo($contents);


        //定义"SingleMessage"
        $message = new IGtSingleMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600*12*1000);//离线时间
        $message->set_data($template);//设置推送消息类型
        $message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，2为4G/3G/2G，1为wifi推送，0为不限制推送
        //接收方
        $target = new IGtTarget();
        $target->set_appId(APPID);
        $target->set_clientId($cid);
//    $target->set_alias(Alias);

        try {
            $rep = $igt->pushMessageToSingle($message, $target);

        }catch(RequestException $e){
            $requstId =e.getRequestId();
            //失败时重发
            $rep = $igt->pushMessageToSingle($message, $target,$requstId);
            $this->errno=-7003;
            $this->errmsg=$rep['result'];
        }
        return true;
    }
    public function toAll($contents="这是一个推送"){

        $igt = new IGeTui(HOST,APPKEY,MASTERSECRET);
        //定义透传模板，设置透传内容，和收到消息是否立即启动启用
        $template = $this->_IGtNotyPopLoadTemplateDemo($contents);
        //$template = IGtLinkTemplateDemo();
        // 定义"AppMessage"类型消息对象，设置消息内容模板、发送的目标App列表、是否支持离线发送、以及离线消息有效期(单位毫秒)
        $message = new IGtAppMessage();
        $message->set_isOffline(true);
//        $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);

        $appIdList=array(APPID);
        $phoneTypeList=array('ANDROID');
        $cdt = new AppConditions();
         $cdt->addCondition(AppConditions::PHONE_TYPE, $phoneTypeList);


        $message->set_appIdList($appIdList);
        $message->conditions=$cdt;

        $igt->pushMessageToApp($message);

        return true;
    }
    public function _IGtNotyPopLoadTemplateDemo($msg){
        $template =  new IGtTransmissionTemplate();
        $template->set_appId(APPID);//应用appid
        $template->set_appkey(APPKEY);//应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent($msg);//透传内容

//          APN高级推送
        $apn = new IGtAPNPayload();
        $alertmsg=new DictionaryAlertMsg();
        $alertmsg->body="body";
        $alertmsg->actionLocKey="ActionLockey";
        $alertmsg->locKey="LocKey";
        $alertmsg->locArgs=array("locargs");
        $alertmsg->launchImage="launchimage";
//        iOS8.2 支持
        $alertmsg->title="Title";
        $alertmsg->titleLocKey="TitleLocKey";
        $alertmsg->titleLocArgs=array("TitleLocArg");

        $apn->alertMsg=$alertmsg;
        $apn->badge=7;
        $apn->sound="";
        $apn->add_customMsg("payload","payload");
        $apn->contentAvailable=1;
        $apn->category="ACTIONABLE";
        $template->set_apnInfo($apn);

        return $template;
    }
}
