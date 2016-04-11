<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once DT_ROOT.'/module/brand/brand.class.php';
$oBrand = new brand(9);
$aCatCache = cache_read('category-13.php');

$action = isset($action)?$action:'';
switch($action){
    case 'del':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $itemid = isset($itemid)?intval($itemid):0;
        if(empty($itemid)) exit(json_encode(array('status'=>'n','info'=>'非法操作')));
        $oBrand->itemid = $itemid;
        $aBrand = $oBrand->get_one();
        if(!$aBrand || $aBrand['userid']!=$_userid) exit(json_encode(array('status'=>'n','info'=>'信息已失效')));
        if($aBrand['status']==3) exit(json_encode(array('status'=>'n','info'=>'信息不能删除')));
        $oBrand->recycle($itemid);
        exit(json_encode(array('status'=>'y','info'=>'删除成功')));
        break;
    default:
        if(!$_userid) dheader('/member/login.php');
        $where = ' userid = '.$_userid;
        list($aBrand,$totalpage) = $oBrand->brandList('thumb,title,itemid,catid,company,fromtime,totime,status',$where.' and ( status=3 or status = 2 or status = 1) ','hits desc','10');
        $active = 'discount';
        $seo_title = '商家优惠管理-会员中心-';
        $nav_selected='member';
        include template('discount', $module);
        break;
}
?>