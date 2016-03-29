<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/know/know.class.php';

require_once '../module/member/member.class.php';
$oMember = new member();
$mygetcount = false;
$pagesize = 20;
$aKnow = $oMember->get_list('ischu=1 and groupid = 5');

$aCacheCat13 = cache_read('moduleid-13.php');
$oKnow = new know(10);
$pagesize = 16;
$aCai = $oKnow->get_list('status=3');
$memberlist = $oMember->getListUser($aCai);

$aKnowFend = $oKnow->get_fenglist();


$nav_selected = 'school';
$seo_title = '名厨学堂-';
include template('index', 'school');
?>