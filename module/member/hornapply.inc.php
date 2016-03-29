<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once DT_ROOT.'/module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';

if(!$_userid) dalert('请先登录','/member/login.php');
if(!$horninfo = $oSpecial->checkHasUser()) dalert($oSpecial->errmsg,'/member/index.php');

$action = isset($action)?$action:'';
if($action=='apply'){
    $applymoney = applynum($horninfo['itemid']);
    if(doubleval($applymoney)==0){
        dalert('结算金额为0元,不能结算','/member/hornapply.php');
    }
    $oSpecial->applymoney($horninfo['itemid'],$_userid);
    dalert('申请已提交，请等待审核...','/member/hornapply.php');
}else{
    $where = ' codeid = '.$horninfo['itemid'].' and status = 3 ';
    list($aCodel,$totalpage) = $oSpecial->codeListApply($where,'10');
    list($codenumed,$codenewno,$applynum) = horncommon($horninfo['itemid']);
    $snd = 4;
    $seo_title = '申请结算-会员中心-';
    include template('hornapply', $module);
}

?>