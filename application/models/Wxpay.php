<?php

/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
$ThirdPath=dirname(__FILE__).'/../library/ThirdPart/Wxpay/';
include_once($ThirdPath."WxPay.Api.php");
include_once($ThirdPath."WxPay.Notify.php");
include_once($ThirdPath."WxPay.NativePay.php");
include_once($ThirdPath."WxPay.Data.php");

class WxpayModel
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

    public function createbill($itemid, $uid)
    {
        $query = $this->_db->prepare("select * from item where `id` = ?");
        $query->execute([$itemid]);
        $ret = $query->fetchAll();
        if (!$ret) {
            $this->errno = -6003;
            $this->errmsg = "找不到这件商品";
            return false;
        }
        $item = $ret[0];
        if (strtotime($item['etime']) <= time()) {
            $this->errno = -6004;
            $this->errmsg = "商品过期，无法购买";
            return false;
        }
        if (intval($item['stock']) <= 0) {
            $this->errno = -6005;
            $this->errmsg = "商品库存量不足，无法购买";
            return false;
        }

        $query = $this->_db->prepare("insert into `bill` (`itemid`,`uid`,`price`,`status`) VALUES (?,?,?,'unpaid')");
        $res = $query->execute([$itemid, $uid, $item['price']]);
        if (!$res) {
            $this->errno = -6006;
            $this->errmsg = "创建订单失败";
            return false;
        }
        $lastId=intval($this->_db->lastInsertId());
        $query=$this->_db->prepare("update `item` set `stock` = `stock`-1 where `id` = ?");
        $res=$query->execute([$itemid]);
        if (!$res) {
            $this->errno = -6007;
            $this->errmsg = "更新库存失败";
            return false;
        }
        return $lastId;
    }
    public function qrcode($billid){
        $query=$this->_db->prepare("select * from `bill` where `id` = ?");
        $query->execute([$billid]);
        $ret=$query->fetchAll();
        if (!$ret){
            $this->errno = -7002;
            $this->errmsg = "找不到订单信息";
            return false;
        }
        $bill=$ret[0];
        $query=$this->_db->prepare("select * from `item` where `id` = ?");
        $query->execute([$bill['itemid']]);
        $ret=$query->fetchAll();
        if (!$ret){
            $this->errno = -7003;
            $this->errmsg = "找不到商品信息";
            return false;
        }
        $item=$ret[0];
        $notify = new NativePay();
        $input = new WxPayUnifiedOrder();
        $input->SetBody($item['name']);
        $input->SetAttach($billid);
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($bill['price']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 86000*3));
        $input->SetGoods_tag($item['name']);
        $input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($billid);
        $result = $notify->GetPayUrl($input);
        $url2 = $result["code_url"];
        return $url2;
    }
    public function callback(){
        /**
         * 订单成功，更新账单
         * TODO 因为SK没有，没法与微信支付的服务端做Response确认，只能单方面记账
         */
        $xmlData = file_get_contents("php://input");
        if( substr_count( $xmlData, "<result_code><![CDATA[SUCCESS]]></result_code>" )==1 &&
            substr_count( $xmlData, "<return_code><![CDATA[SUCCESS]]></return_code>" )==1 )
        {
            preg_match( '/<attach>(.*)\[(\d+)\](.*)<\/attach>/i', $xmlData, $match );
            if( isset($match[2])&&is_numeric($match[2]) ) {
                $billId = intval( $match[2] );
            }
            preg_match( '/<transaction_id>(.*)\[(\d+)\](.*)<\/transaction_id>/i', $xmlData, $match );
            if( isset($match[2])&&is_numeric($match[2]) ) {
                $transactionId = intval( $match[2] );
            }
        }
        if( isset($billId) && isset($transactionId) ) {
            $query = $this->_db->prepare("update `bill` set `transaction`=? ,`ptime`=? ,`status`='paid' where `id`=? ");
            $query->execute( array( $transactionId, date("Y-m-d H:i:s"), $billId ) );
        }
    }
}
