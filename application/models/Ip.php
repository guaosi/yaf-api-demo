<?php

/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
class IpModel
{
    public $errno = 0;
    public $errmsg = "错误";
    public function get($ip){

        $rep=ThirdPart_IP::find($ip);

        return $rep;
    }
}
