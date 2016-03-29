<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once '../module/buy/buy.class.php';
$oBuy = new buy(6);

$action = isset($action)?$action:'';
switch($action){
    case 'del':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $itemid = isset($itemid)?intval($itemid):0;
        if(empty($itemid)) exit(json_encode(array('status'=>'n','info'=>'非法操作')));
        $oBuy->itemid = $itemid;
        $aBuy = $oBuy->get_one();
        if(!$aBuy || $aBuy['userid']!=$_userid) exit(json_encode(array('status'=>'n','info'=>'信息已失效')));
        $oBuy->recycle($itemid);
        exit(json_encode(array('status'=>'y','info'=>'删除成功')));
        break;
    default:
        if(!$_userid) dheader('/member/login.php');
        $where = ' userid = '.$_userid;
        list($aBuy,$totalpage) = $oBuy->buyList('thumb,title,itemid,status',$where.' and ( status=3 or status=2 or status=1) ','hits desc','10');
        $active = 'share';
        $seo_title = '分享管理-会员中心-';
        $nav_selected='member';
        include template('share', $module);
        break;
}
?>