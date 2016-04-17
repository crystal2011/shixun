<?php
/**
 * 会员中心
 */
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if(!$_userid || $action == 'logout') dheader('/mobile/member/login.php');
require DT_ROOT.'/include/post.func.php';
$seo_title = '会员中心-';
$topname = '会员中心';
$notop = true; //不显示头部
$backurl = $forward;
include template('index', 'mobile/member');
?>