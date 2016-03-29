<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once DT_ROOT.'/module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';

if(!$_userid) dheader('/member/login.php');
if(!$horninfo = $oSpecial->checkHasUser()) dalert($oSpecial->errmsg,'/member/index.php');


$action = isset($action)?$action:'';
$where = ' c.codeid = '.$horninfo['itemid'];

list($aCodel,$totalpage) = $oSpecial->codeList($where,'10');

require_once DT_ROOT.'/module/member/member.class.php';
$member_do = new member;
$getListUser = $member_do->getListUser($aCodel,'username');


list($codenumed,$codenewno,$applynum) = horncommon($horninfo['itemid']);

$active = 'horn';
$snd = 1;
$nav_selected='member';
$seo_title = '审核记录-会员中心-';
include template('horn', $module);

?>