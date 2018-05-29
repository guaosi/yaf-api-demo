<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10/010
 * Time: 17:21
 */
class Db_Base{
    public static $_db=null;
    public static $errno=0;
    public static $errmsg="";
    public static function getDb(){
        if (self::$_db==null){
            self::$_db=new PDO('mysql:host=127.0.0.1;dbname=yaf;','root','root');
        }
        return self::$_db;
    }
    public function errno(){
        return self::$errno;
    }
    public function errmsg(){
        return self::$errmsg;
    }
}