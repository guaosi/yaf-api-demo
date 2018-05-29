<?php
/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
class UserModel {
    public $errno=0;
    public $errmsg="错误";
    private $_dao=null;
    public function __construct() {
        $this->_dao=new Db_User();
    }   
    public function login($name,$pwd){
        $userInfo=$this->_dao->find($name);
        if (!$userInfo){
            $this->errno=$this->_dao->errno();
            $this->errmsg=$this->_dao->errmsg();
            return false;
        }
        if ($userInfo['pwd']!=Common_Password::pwdEncode($pwd)){
            list($this->errno,$this->errmsg)=Err_Map::get(1004);
            return false;
        }
        return intval($userInfo['id']);

    }
    public function register($name,$pwd){

        if(!$this->_dao->ucount($name)){
            $this->errno=$this->_dao->errno();
            $this->errmsg=$this->_dao->errmsg();
            return false;
        }

        if(strlen($pwd)<8){
            list($this->errno,$this->errmsg)=Err_Map::get(1006);
            return false;
        }else{
            $password=$this->_password_generate($pwd);
        }

        if (!$this->_dao->addUser($name,$password,date('Y-m-d H:i:s'))){
            $this->errno=$this->_dao->errno();
            $this->errmsg=$this->_dao->errmsg();
            return false;
        }
       return true;
    }
    private function _password_generate($pwd){
        return md5('salt-'.$pwd);
    }

}
