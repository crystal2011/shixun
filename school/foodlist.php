<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/know/know.class.php';

$where = '1=1';
//分类
$aCacheCat13 = cache_read('category-13.php');
$catinfo = isset($aCacheCat13[$catid])?$aCacheCat13[$catid]:array();
if($catinfo){
    $where .= " and catid in (".$catinfo['arrchildid'].")";
}

$oCai = new know(10);
list($aCai,$totalpage) = $oCai->schoolList('*',$where.' and status=3 ','addtime desc','20');

require_once '../module/member/member.class.php';
$oMember = new member();
$aKnow = $oMember->getListUser($aCai);
//列表导航
$aTou[] = array('url'=>'/school/foodlist.php','name'=>'全部');
if($CAT) $aTou[] = array('url'=>'/school/foodlist.php','name'=>$CAT['catname']);

$aCatList = showCat($catinfo,13);     //列表选择显示分类

$nav_selected = 'school';
$nav_show = 'food.php';
$seo_title = '菜系-名厨学堂-';
include template('foodlist', 'school');
?>