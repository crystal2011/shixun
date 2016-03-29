<?php
defined('IN_DESTOON') or exit('Access Denied');
if(!$_userid || $action == 'logout') dheader('/member/login.php');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require MD_ROOT.'/member.class.php';
require DT_ROOT.'/include/post.func.php';
$do = new member;
$do->userid = $_userid;
$useruser = $do->get_one();
$seo_title = '会员中心-';
$nav_selected='member';
include template('index', $module);
?>