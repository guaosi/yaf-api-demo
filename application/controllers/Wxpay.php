<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
$ThirdPath=dirname(__FILE__).'/../library/ThirdPart/Qrcode/';
require $ThirdPath . 'phpqrcode.php';
class WxpayController extends Yaf_Controller_Abstract {
    public function createbillAction(){
        $itemid=$this->getRequest()->getQuery('itemid',false);
        if (!$itemid){
            echo json_encode([
                'errno'=>-6001,
                'errmsg'=>"请传递正确的商品ID"
            ]);
            return false;
        }
        session_start();
        if (!$_SESSION['user_token']||!$_SESSION['user_token_time']||!$_SESSION['user_id']||$_SESSION['user_token']!=md5('salt'.$_SESSION['user_token_time'].$_SESSION['user_id'])){
            echo json_encode([
                'errno'=>-6002,
                'errmsg'=>"请登录后进行操作"
            ]);
            return false;
        }

        $model=new WxpayModel();

        if ($data=$model->createbill($itemid,$_SESSION['user_id'])){

            echo json_encode([
                'errno'=>0,
                'errmsg'=>'',
                'data'=>$data
            ]);
            return true;
        }else{

            echo json_encode([
                'errno'=>$model->errno,
                'errmsg'=>$model->errmsg
            ]);
            return false;
        }
    }
    public function qrcodeAction(){
        $billid=$this->getRequest()->getQuery('billid',false);
        if (!$billid){
            echo json_encode([
                'errno'=>-7001,
                'errmsg'=>"请传递正确的订单ID"
            ]);
            return false;
        }

        $model=new WxpayModel();

        if ($data=$model->qrcode($billid)){
            QRcode::png($data);
        }else{
            echo json_encode([
                'errno'=>$model->errno,
                'errmsg'=>$model->errmsg
            ]);
            return false;
        }

    }
    public function callbackAction(){
        $model=new WxpayModel();
        $model->callbakc();
        echo json_encode([
                'errno'=>0,
                'errmsg'=>'',

        ]);
        return true;
    }
}
