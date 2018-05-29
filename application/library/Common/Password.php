<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10/010
 * Time: 16:59
 */
class Common_Password{
    public static function pwdEncode($pwd){
        return md5('salt-'.$pwd);
    }
}