<?php

/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
class ArtModel
{
    public $errno = 0;
    public $errmsg = "错误";
    private $_db = null;

    public function __construct()
    {
        $this->_db = new PDO('mysql:host=127.0.0.1;dbname=yaf;', 'root', 'root');
        /** 设置下面这行 阻止PDO拼接SQL时将 int 0 转为 string 0  **/
        $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function add($title, $contents, $author, $cate, $artId)
    {
        $isEdit = false;
        if ($artId != 0 && is_numeric($artId)) {
            // 修改操作
            $query = $this->_db->prepare("select count(*) from `art` where `id` = ?");
            $query->execute([$artId]);
            $ret = $query->fetchAll();
            if (!$ret || $ret[0][0] != 1) {
                $this->errno = -2004;
                $this->errmsg = "找不到要编辑的文章";
                return false;
            }
            $isEdit = true;
        } else {
            //新增操作
            $query = $this->_db->prepare("select count(*) from `cate` where `id` = ?");
            $query->execute([$cate]);
            $ret = $query->fetchAll();
            if (!$ret || $ret[0][0] != 1) {
                $this->errno = -2005;
                $this->errmsg = '找不到对应的ID的信息分类,cate id:' . $cate . ",请先创建该分类.";
                return false;
            }
        }
        $data = [$title, $contents, $author, $cate];
        if ($isEdit) {
            $query = $this->_db->prepare("update `art` set `title` = ?,`contents`= ? ,`author`= ? ,`cate`= ? where `id`= ?");
            $data[] = $artId;
        } else {
            $query = $this->_db->prepare("insert into `art` (`title`,`contents`,`author`,`cate`) VALUES (?,?,?,?)");
        }
        $ret = $query->execute($data);
        if (!$ret) {
            $this->errno = -2006;
            $this->errmsg = "操作文章数据失败,ErrInfo:" . end($query->errorInfo());
            return false;
        }
        /** 返回文章最后的ID值 **/
        if ($isEdit) {
            return intval($artId);
        } else {
            return $this->_db->lastInsertId();
        }
    }

    public function del($artId)
    {
        $query = $this->_db->prepare("delete from `art` where `id` = ?");
        $ret = $query->execute([$artId]);
        if (!$ret) {
            $this->errno = -2007;
            $this->errmsg = "删除失败 ，ErrInfo:" . end($query->errorInfo());
            return false;
        }
        return true;
    }

    public function status($artId, $status = 'offline')
    {
        $query = $this->_db->prepare("update `art` set `status` = ? where `id` = ?");
        $ret = $query->execute([$status, $artId]);
        if (!$ret) {
            $this->errno = -2008;
            $this->errmsg = "更新文章状态失败 , ErrInfo:" . end($query->errorInfo());
        }
        return true;
    }

    public function get($artId)
    {
        $query = $this->_db->prepare("select `title`,`contents`,`author`,`cate`,`ctime`,`mtime`,`status` from `art` where `id` = ?");
        $query->execute([$artId]);
        $ret = $query->fetchAll();
        if (!$ret) {
            $this->errno = -2009;
            $this->errmsg = "查询失败,ErrInfo:" . end($query->errorInfo());
            return false;
        }
        $artInfo = $ret[0];
        /** 获取分类信息 **/
        $query = $this->_db->prepare("select `name` from `cate` where `id` = ?");
        $query->execute([$artInfo['cate']]);
        $ret = $query->fetchAll();
        if (!$ret) {
            $this->errno = -2010;
            $this->errmsg = "获取分类信息失败，ErrInfo:" . end($query->errorInfo());
            return false;
        }
        $artInfo['catename'] = $ret[0]['name'];
        $data = [
            'id' => intval($artId),
            'title' => $artInfo['title'],
            'contents' => $artInfo['contents'],
            'author' => $artInfo['author'],
            'cateName' => $artInfo['catename'],
            'cateId' => intval($artInfo['cate']),
            'ctime' => $artInfo['ctime'],
            'mtime' => $artInfo['mtime'],
            'status' => $artInfo['status'],
        ];
        return $data;
    }

    public function listpage($pageno = 1, $pagesize = 10, $cate = 0, $status = 'online')
    {
        if (!is_numeric($pageno) || $pageno <= 0 || empty($pageno)) {
            $pageno = 1;
        }
        $start = ($pageno - 1) * $pagesize;
        if ($cate == 0) {
            $filter=[$status,$start,$pagesize];
            $query=$this->_db->prepare("select `id`,`title`,`contents`,`author`,`cate`,`ctime`,`mtime`,`status` from `art` where `status` = ? order by `ctime` desc limit ? , ?");
        } else {
            $filter=[$status,$cate,$start,$pagesize];
            $query=$this->_db->prepare("select `id`,`title`,`contents`,`author`,`cate`,`ctime`,`mtime`,`status` from `art` where `status` = ? and `cate` = ? order by `ctime` desc limit ? , ?");
        }
        $query->execute($filter);
        $ret=$query->fetchAll();
        if (!$ret){
            $this->errno=-2011;
            $this->errmsg="获取文章列表失败 , ErrInfo:".end($query->errorInfo());
            return false;
        }
        $cateInfo=[];
        $data=[];
        foreach ($ret as $item){
            if (empty($cateInfo[$item['cate']])){
                $query=$this->_db->prepare("select `name` from `cate` where `id` = ?");
                $query->execute([$item['cate']]);
                $rs=$query->fetchAll();
                if (!$rs){
                    $this->errno=-2010;
                    $this->errmsg="获取分类信息失败 , ErrInfo :".end($query->errorInfo());
                    return false;
                }
                $cateName=$cateInfo[$item['cate']]=$rs[0]['name'];
            }else{
                $cateName=$cateInfo[$item['cate']];
            }
           $contents=mb_strlen($item['contents'])>30 ? mb_substr($item['contents'],0,30).'...' : $item['contents'];
            $data[]=[
                'id' => intval($item['id']),
                'title' => $item['title'],
                'contents' => $contents,
                'author' => $item['author'],
                'cateName' => $cateName,
                'cateId' => intval($item['cate']),
                'ctime' => $item['ctime'],
                'mtime' => $item['mtime'],
                'status' => $item['status'],
            ];
        }
        return $data;
    }
}
