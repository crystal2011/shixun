<?php
/**
 * 会员中心-退出
 */
require '../../member/config.inc.php';
require '../../common.inc.php';
require '../../module/member/member.class.php';
$do = new member;
$do->logout();
$session = new dsession();
session_destroy();
$forward = $forward ? linkurl($forward) : $CFG['url'].'mobile/index.php';
$forward = wapforward();
$action = 'logout';
$api_msg = $api_url = '';
if($api_msg) message($api_msg, $forward, -1);
message($api_msg, $CFG['url'].'mobile/index.php');
?>