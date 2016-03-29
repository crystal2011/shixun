<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once '../module/food/food.class.php';
$oFood = new food(23);
$aCatCache = cache_read('category-23.php');

$action = isset($action)?$action:'';
switch($action){
    case 'del':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $itemid = isset($itemid)?intval($itemid):0;
        if(empty($itemid)) exit(json_encode(array('status'=>'n','info'=>'非法操作')));
        $oFood->itemid = $itemid;
        $aFood = $oFood->get_one();
        if(!$aFood || $aFood['userid']!=$_userid) exit(json_encode(array('status'=>'n','info'=>'信息已失效')));
        $oFood->recycle($itemid);
        exit(json_encode(array('status'=>'y','info'=>'删除成功')));
        break;
    default:
        if(!$_userid) dheader('/member/login.php');
        $where = ' userid = '.$_userid;
        list($aFood,$totalpage) = $oFood->foodList('thumb,title,price,unit,itemid,catid,status',$where.' and (status=3 or status = 2 or status = 1)','hits desc','10');
        $nav_selected='member';
        $active = 'food';
        $seo_title = '餐饮供应管理-会员中心-';
        include template('food', $module);
        break;
}
?>