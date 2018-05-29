<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10/010
 * Time: 17:21
 */
class Db_User extends Db_Base {
    public function find($name){
        $query=self::getDb()->prepare("select pwd,id from `user` where `name`=?");
        $query->execute([$name]);
        $ret=$query->fetchAll();
        if (!$ret||count($ret)!=1){
            list(self::$errno,self::$errmsg)=Err_Map::get(1003);
            return false;
        }
        return $ret[0];
    }
    public function ucount($name){
        $query=self::getDb()->prepare("select count(*) as c from `user` where `name` = ? ");
        $query->execute(array($name));
        $count=$query->fetchAll();
        if($count[0]['c']!=0){
            list(self::$errno,self::$errmsg)=Err_Map::get(1005);
            self::$errno=-1005;
            self::$errmsg='用户名已经存在';
            return false;
        }
        return true;
    }
    public function addUser($name, $password, $nowtime){
        $query = self::getDb()->prepare("insert into `user` (`id`,`name`,`pwd`,`reg_time`) VALUES (null,?,?,?)");
        $ret=$query->execute([$name, $password, $nowtime]);
        if (!$ret){
            list(self::$errno,self::$errmsg)=Err_Map::get(1007);
            return false;
        }
        return true;
    }
}