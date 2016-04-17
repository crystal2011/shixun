<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';

//文章
require_once '../module/info/info.class.php';
$table = $db->pre.'info_24';
$table_data = $db->pre.'info_data_24';
$oInfo = new info(24);
list($list,$totalpage) = $oInfo->foodList('*','status=3','addtime desc','10');

$aHotFood = $oInfo->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oInfo->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$catname='羊角会';
$foodshowright = 39;
$nav_selected = 'horn';

$seo_title = '羊角会-';
include template('list','horn');
?>