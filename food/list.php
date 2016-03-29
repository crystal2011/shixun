<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/food/food.class.php';

$where = '1=1';
//地区
$areaid = $areaid?$areaid:($dtcity?$dtcity['areaid']:0);
$aCacheArea = cache_read('area.php');
$areainfo = isset($aCacheArea[$areaid])?$aCacheArea[$areaid]:array();
if($areainfo){
    $where .= " and areaid in (".$areainfo['arrchildid'].")";
}

//分类
$aCacheCat23 = cache_read('category-23.php');
$catinfo = isset($aCacheCat23[$catid])?$aCacheCat23[$catid]:array();
if($catinfo){
    $where .= " and catid in (".$catinfo['arrchildid'].")";
}

$kw = isset($kw)?$kw:'';
if(strlen($kw)!=0){
    $where .= " and title like '%".$kw."%' ";
}

$oFood = new food(23);
list($aFood,$totalpage) = $oFood->foodList('thumb,title,price,unit,userid,itemid',$where.' and status=3','addtime desc','30');

//列表导航
$aTou[] = array('url'=>'/food/list.php','name'=>'全部');
if($CAT) $aTou[] = array('url'=>'/food/list.php?areaid='.$areaid.'&kw='.$kw,'name'=>$CAT['catname']);
if($ARE) $aTou[] = array('url'=>'/food/list.php?catid='.$catid.'&kw='.$kw,'name'=>$ARE['areaname']);
if($kw) $aTou[] = array('url'=>'/food/list.php?catid='.$catid.'&areaid='.$areaid,'name'=>$kw);

$aAreaFirst = showArea($areainfo);  //列表选择显示地区
$aCatList = showCat($catinfo,23);     //列表选择显示分类
$aHotCity = cache_read('hotcity.php');  //热门地区

//热门推荐广告
require_once '../module/extend/ad.class.php';
$oAd = new ad;
$sAdPlaceHot = $oAd->getAdAllText(30);
$sAdPlaceTest = $oAd->getAdAllText(31);

$nav_selected = 'food';
$seo_title = '餐饮供应-';
include template('list', 'food');
?>