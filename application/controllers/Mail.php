<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class MailController extends Yaf_Controller_Abstract {
    public function sendAction(){
        $submit=$this->getRequest()->getQuery('submit','0');
        if ($submit!="1"){
            echo json_encode([
                "errno"=>-3001,
                'errmsg'=>'请从正确的渠道提交'
            ]);
            return false;
        }
        $uid=$this->getRequest()->getPost('uid',false);
        $title=$this->getRequest()->getPost('title',false);
        $contents=$this->getRequest()->getPost('contents',false);

        if (!$uid||!$title||!$contents){
            echo json_encode([
                "errno"=>-3002,
                'errmsg'=>'用户ID，邮件标题，邮件内容均不能为空'
            ]);
            return false;
        }

        $model=new MailModel();

        if ($model->send($uid,$title,$contents)){
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
