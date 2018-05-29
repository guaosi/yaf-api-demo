<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class PushController extends Yaf_Controller_Abstract {
    public function singleAction(){
       $submit=$this->getRequest()->getQuery('submit','0');
       if ($submit!='1'){
           echo json_encode([
                'errno'=>-7001,
                 'errmsg'=>'请从正确的渠道提交'
           ]);
           return false;
       }
       $cid=$this->getRequest()->getPost('cid',false);
       $contents=$this->getRequest()->getPost('contents',false);
       if (!$cid||!$contents){
           echo json_encode([
               'errno'=>-7002,
               'errmsg'=>'请输入要推送用户的设备ID与要推送的内容'
           ]);
           return false;
       }
       $model=new PushModel();
       if ($model->toSingle($cid,$contents)){
           echo json_encode([
               'errno'=>0,
               'errmsg'=>''
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
    public function toAllAction(){
        $submit=$this->getRequest()->getQuery('submit','0');
        if ($submit!='1'){
            echo json_encode([
                'errno'=>-7001,
                'errmsg'=>'请从正确的渠道提交'
            ]);
            return false;
        }
        $contents=$this->getRequest()->getPost('contents',false);
        if (!$contents){
            echo json_encode([
                'errno'=>-7002,
                'errmsg'=>'请输入要推送的内容'
            ]);
            return false;
        }

        $model=new PushModel();

        if ($model->toAll($contents)){

            echo json_encode([
                'errno'=>0,
                'errmsg'=>''
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
}
