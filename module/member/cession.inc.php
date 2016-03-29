<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once '../module/sell/sell.class.php';
$oSell = new sell(5);
$aCatCache = cache_read('category-13.php');

$action = isset($action)?$action:'';
switch($action){
    case 'del':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $itemid = isset($itemid)?intval($itemid):0;
        if(empty($itemid)) exit(json_encode(array('status'=>'n','info'=>'非法操作')));
        $oSell->itemid = $itemid;
        $aSell = $oSell->get_one();
        if(!$aSell || $aSell['userid']!=$_userid) exit(json_encode(array('status'=>'n','info'=>'信息已失效')));
        $oSell->recycle($itemid);
        exit(json_encode(array('status'=>'y','info'=>'删除成功')));
        break;
    default:
        if(!$_userid) dheader('/member/login.php');
        $where = ' userid = '.$_userid;
        list($aSell,$totalpage) = $oSell->sellList('thumb,title,itemid,catid,price,seat,acreage,status',$where.' and ( status=3 or status = 2 or status = 1) ','hits desc','10');
        $active = 'cession';
        $nav_selected='member';
        $seo_title = '店铺转让管理-会员中心-';
        include template('cession', $module);
        break;
}
?>