<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
require_once '../module/know/know.class.php';
$obj = new know(10);

$action = isset($action)?$action:'';
switch($action){
    case 'del':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $itemid = isset($itemid)?intval($itemid):0;
        if(empty($itemid)) exit(json_encode(array('status'=>'n','info'=>'非法操作')));
        $obj->itemid = $itemid;
        $aCai = $obj->get_one();
        if(!$aCai || $aCai['userid']!=$_userid) exit(json_encode(array('status'=>'n','info'=>'信息已失效')));
        $obj->recycle($itemid);
        exit(json_encode(array('status'=>'y','info'=>'删除成功')));
        break;
    default:
        if(!$_userid) dheader('/member/login.php');
        $where = ' userid = '.$_userid;
        list($aCai,$totalpage) = $obj->schoolList('thumb,title,itemid,catid,userid,status',$where.' and (status=3 or status = 2 or status = 1)','hits desc','10');

        $active = 'dish';
        $seo_title = '菜系管理-会员中心-';
        $aCatCache = cache_read('category-13.php');
        $nav_selected='member';
        include template('dish', $module);
        break;
}
?>