<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once DT_ROOT.'/module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';

$at = isset($at)?$at:'';
if($at=='check'){
    if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
    if(!$horninfo = $oSpecial->checkHasUser()) exit(json_encode(array('status'=>'n','info'=>$oSpecial->errmsg)));
    $checktype = isset($checktype)?$checktype:0;
    $itemid = isset($itemid)?intval($itemid):0;
    $status = isset($status)?intval($status):0;
    $errormsg = isset($errormsg)?$errormsg:'';
    if(!$oSpecial->canCheck($checktype,$horninfo['itemid'],$itemid,$status,$errormsg)) exit(json_encode(array('status'=>'n','info'=>$oSpecial->errmsg)));
    exit(json_encode(array('status'=>'y','info'=>'操作成功')));
}else{
    if(!$_userid) dalert('请先登录','/member/login.php');
    if(!$horninfo = $oSpecial->checkHasUser()) dalert($oSpecial->errmsg,'/member/index.php');


    $action = isset($action)?$action:'';
    $checktype = isset($checktype)?$checktype:0;
    list($aCodel,$totalpage) = $oSpecial->codeListTable($checktype,$horninfo['itemid']);
    $snd = 2;
    require_once DT_ROOT.'/module/member/member.class.php';
    $member_do = new member;
    $getListUser = $member_do->getListUser($aCodel,'username');
    $nav_selected='member';
    $active = 'horn';
    list($codenumed,$codenewno,$applynum) = horncommon($horninfo['itemid']);
    $seo_title = '待审核-会员中心-';
    include template('horncheck', $module);
}
?>