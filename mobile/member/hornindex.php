<?php
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require_once DT_ROOT.'/module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';

if(!$_userid) dheader('/mobile/member/login.php');
if(!$horninfo = $oSpecial->checkHasUser()) dalert($oSpecial->errmsg,'/mobile/member/index.php');
list($codenumed,$codenewno,$applynum) = horncommon($horninfo['itemid']);
$topname = '羊角会管理';
$seo_title = '羊角会管理-会员中心-';
include template('hornindex', 'mobile/'.$module);

?>