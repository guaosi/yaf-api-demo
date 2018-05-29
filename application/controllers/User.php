<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class UserController extends Yaf_Controller_Abstract {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/yaf/index/index/index/name/root 的时候, 你就会发现不同
     */
    public function IndexAction(){
       return $this->LoginAction();
    }
    public function LoginAction(){
       $submit=Common_Request::getRequest('submit','0');
       if ($submit!="1"){
           echo json_encode(Err_Map::get(1001));
           return false;
       }
        $name=$this->getRequest()->getPost('name',false);
        $pwd=$this->getRequest()->getPost('pwd',false);
        if (!$name||!$pwd){
            echo json_encode(Err_Map::get(1002));
            return false;
        }
        $model=new UserModel();
        if ($uid=$model->login($name,$pwd)){
            session_start();
            $_SESSION['user_token']=md5('salt'.$_SERVER['REQUEST_TIME'].$uid);
            $_SESSION['user_token_time']=$_SERVER['REQUEST_TIME'];
            $_SESSION['user_id']=$uid;
            echo Common_Request::Response(0,'',['name'=>$name]);
        }else{
             echo json_encode(Err_Map::get($model->errno));
             return false;
        }
        return false;
    }

	public function RegAction() {
        $name=$this->getRequest()->getPost('name',false);
        $pwd=$this->getRequest()->getPost('pwd',false);
           if (!$name||!$pwd){
               echo json_encode(Err_Map::get(1002));
               return false;
           }
        $model=new UserModel();
        if($model->register(trim($name),trim($pwd))){
            echo Common_Request::Response(0,'',['name'=>$name]);
        }else{
            echo json_encode(Err_Map::get($model->errno));
        }
        return false;

	}
}
