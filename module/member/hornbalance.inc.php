<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once DT_ROOT.'/module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';

if(!$_userid) dalert('请先登录','/member/login.php');
if(!$horninfo = $oSpecial->checkHasUser()) dalert($oSpecial->errmsg,'/member/index.php');

$where = ' codeid = '.$horninfo['itemid'].' ';

list($codenumed,$codenewno,$applynum) = horncommon($horninfo['itemid']);

list($aCodel,$totalpage) = $oSpecial->codeListApplyList($where,'10');
$snd = 5;
$seo_title = '结算记录-会员中心-';

include template('hornbalance', $module);

?>