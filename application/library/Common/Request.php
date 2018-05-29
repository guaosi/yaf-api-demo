<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10/010
 * Time: 16:29
 */
class Common_Request{

   public static function getRequest($key,$default=null){
       return self::Request($key,$default,'get');
   }
   public static function postRequest($key,$default=null){
       return self::Request($key,$default,'post');
   }
   public static function Request($key,$default=null,$type=null){
       if ($type=='get'){
           $result=empty($_GET[$key])?null:$_GET[$key];
       }else if ($type=='post'){
           $result=empty($_POST[$key])?null:$_POST[$key];
       }else{
           $result=empty($_POST[$key])?null:$_POST[$key];
       }
       if ($result==null&&$default!=null){
           $result=$default;
       }
       return $result;
   }
   public static function Response($errno=0,$errmsg='',$data=[]){
       $res=[
           'errno'=>$errno,
           'errmsg'=>$errmsg
       ];
       if (count($data)!=0){
           $res['data']=$data;
       }
       return json_encode($res);
   }
}