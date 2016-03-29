<?php
/**
 * 手机端-名厨学堂-首页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/know/know.class.php';

require_once '../../module/member/member.class.php';
$oMember = new member();
$mygetcount = false;
$pagesize = 3;
$aKnow = $oMember->get_list('ischu=1 and groupid = 5','infonums desc');

$oKnow = new know(10);
$pagesize = 3;
$aCai = $oKnow->get_list('status=3','votes desc');
$memberlist = $oMember->getListUser($aCai);

$seo_title = '名厨学堂-';
$topname = '名厨学堂';
include template('school/index','mobile');