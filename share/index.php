<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/buy/buy.class.php';

$where = '1=1';
$oBuy = new buy(6);
list($aBuy,$totalpage) = $oBuy->buyList('title,userid,itemid,addtime,introduce,thumb',$where.' and status=3 ','addtime desc','6');


$aHotFood = $oBuy->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oBuy->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$catname='美食分享';
$foodshowright = 56;

$nav_selected = 'share';
$seo_title = '美食分享-';
include template('index', 'share');
?>