<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class ArtController extends Yaf_Controller_Abstract {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/yaf/index/index/index/name/root 的时候, 你就会发现不同
     */
     public function indexAction(){
         return $this->listAction();
     }
     public function listAction(){
         $pageno=$this->getRequest()->getQuery('pageno',0);
         $pagesize=$this->getRequest()->getQuery('pagesize',10);
         $cate=$this->getRequest()->getQuery('cate',0);
         $status=$this->getRequest()->getQuery('status','online');
         $model=new ArtModel();
         if ($data=$model->listpage($pageno,$pagesize,$cate,$status)){
               echo json_encode([
                  'errno'=>0,
                   'errmsg'=>'',
                   'data'=>$data
               ]);
         }else{
              echo json_encode([
                 'errno'=>$model->errno,
                  'errmsg'=>$model->errmsg
              ]);
         }
         return true;
     }
     public function addAction($artId=0){
         if (!Auth_Admin::isAdmin()){
             echo json_encode([
                'errno'=>-2000,
                 'errmsg'=>'需要管理员权限才可以操作'
             ]);
             return false;
         }
         $submit=$this->getRequest()->getQuery('submit','0');
         if ($submit!='1'){
             echo json_encode([
                'errno'=>-2001,
                 'errmsg'=>'请通过正确的渠道进行提交'
             ]);
             return false;
         }
         //获取参数
         $title=$this->getRequest()->getPost('title',false);
         $contents=$this->getRequest()->getPost('contents',false);;
         $author=$this->getRequest()->getPost('author',false);;
         $cate=$this->getRequest()->getPost('cate',false);;
         if (!$title||!$contents||!$cate||!$author){
             echo json_encode([
                 'errno'=>-2002,
                 'errmsg'=>'标题，内容，作者，分类信息不能为空'
             ]);
             return false;
         }
         $model=new ArtModel();
         if ($lastId=$model->add(trim($title),trim($contents),trim($author),trim($cate),$artId)){
             echo json_encode([
                 'errno'=>0,
                 'errmsg'=>'',
                 'data'=>['lastId'=>$lastId]
             ]);
         }else{
             echo json_encode([
                 'errno'=>$model->errno,
                 'errmsg'=>$model->errmsg
             ]);
         }
         return true;
     }
     public function editAction(){
         if (!Auth_Admin::isAdmin()){
             echo json_encode([
                 'errno'=>-2000,
                 'errmsg'=>'需要管理员权限才可以操作'
             ]);
             return false;
         }
         $artId=$this->getRequest()->getQuery('artId',"0");
         if (is_numeric($artId)&&$artId){
             return $this->addAction($artId);
         }else{
             echo json_encode([
                 'errno'=>-2003,
                 'errmsg'=>'缺少必要的文章ID参数'
             ]);
             return false;
         }
     }
     public function delAction(){
         if (!Auth_Admin::isAdmin()){
             echo json_encode([
                 'errno'=>-2000,
                 'errmsg'=>'需要管理员权限才可以操作'
             ]);
             return false;
         }
         $artid=$this->getRequest()->getQuery('artId','0');
         if (is_numeric($artid)&&$artid){
              $model=new ArtModel();
              if ($model->del($artid)){
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
         }else{
             echo json_encode([
                 'errno'=>-2003,
                 'errmsg'=>'缺少必要的文章ID参数'
             ]);
             return false;
         }
     }
     public function statusAction(){
         if (!Auth_Admin::isAdmin()){
             echo json_encode([
                 'errno'=>-2000,
                 'errmsg'=>'需要管理员权限才可以操作'
             ]);
             return false;
         }
         $artid=$this->getRequest()->getQuery('artId','0');
         $status=$this->getRequest()->getQuery('status','offline');
         if (is_numeric($artid)&&$artid){
             $model=new ArtModel();
             if ($model->status($artid,$status)){
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
         else{
             echo json_encode([
                 'errno'=>-2003,
                 'errmsg'=>'缺少必要的文章ID参数'
             ]);
             return false;
         }
     }
    public function getAction(){

        $artid=$this->getRequest()->getQuery('artId','0');
        if (is_numeric($artid)&&$artid){

            $model=new ArtModel();
            if ($data=$model->get($artid)){
                echo json_encode([
                    'errno'=>0,
                    'errmsg'=>'',
                    'data'=>$data
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
        else{
            echo json_encode([
                'errno'=>-2003,
                'errmsg'=>'缺少必要的文章ID参数'
            ]);
            return false;
        }
    }
     private function _isAdmin(){
         return true;
     }
}
