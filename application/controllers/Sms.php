<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class SmsController extends Yaf_Controller_Abstract {
    public function sendAction(){
        $submit=$this->getRequest()->getQuery('submit','0');
        if ($submit!="1"){
            echo json_encode([
                "errno"=>-4001,
                'errmsg'=>'请从正确的渠道提交'
            ]);
            return false;
        }
        $uid=$this->getRequest()->getPost('uid',false);
        $templateId=$this->getRequest()->getPost('templateId',false);
        if (!$uid||!$templateId){
            echo json_encode([
                "errno"=>-4002,
                'errmsg'=>'用户ID，模板ID不能为空'
            ]);
            return false;
        }

        $model=new SmsModel();

        if ($model->send($uid,$templateId)){
            echo json_encode([
               'errno'=>0,
                'errmsg'=>''
            ]);
        }else{
            echo json_encode([
               'errno'=>$model->errno,
                'errmsg'=>$model->errmsg
            ]);
            return false;
        }
    }

}
