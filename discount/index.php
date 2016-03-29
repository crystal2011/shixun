<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/brand/brand.class.php';

$where = '1=1';
//地区
$areaid = $areaid?$areaid:($dtcity?$dtcity['areaid']:0);
$aCacheArea = cache_read('area.php');
$areainfo = isset($aCacheArea[$areaid])?$aCacheArea[$areaid]:array();
if($areainfo){
    $where .= " and areaid in (".$areainfo['arrchildid'].")";
}

//分类
$aCacheCat13 = cache_read('category-13.php');
$catinfo = isset($aCacheCat13[$catid])?$aCacheCat13[$catid]:array();
if($catinfo){
    $where .= " and catid in (".$catinfo['arrchildid'].")";
}

$oBrand = new brand(13);
list($aBrand,$totalpage) = $oBrand->brandList('title,userid,itemid,thumb,introduce,company',$where.' and status=3 ','addtime desc','4');


//列表导航
$aTou[] = array('url'=>'/discount/index.php','name'=>'全部');
if($CAT) $aTou[] = array('url'=>'/discount/index.php?areaid='.$areaid,'name'=>$CAT['catname']);
if($ARE) $aTou[] = array('url'=>'/discount/index.php?catid='.$catid,'name'=>$ARE['areaname']);

$aAreaFirst = showArea($areainfo);  //列表选择显示地区
$aCatList = showCat($catinfo,13);     //列表选择显示分类
$aHotCity = cache_read('hotcity.php');  //热门地区

//热门推荐广告
require_once '../module/extend/ad.class.php';
$oAd = new ad;
$sAdPlaceHot = $oAd->getAdAllText(47);
$sAdPlaceTest = $oAd->getAdAllText(48);

$aHotFood = $oBrand->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oBrand->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$catname = '优惠';
$foodshowright = 44;

$nav_selected = 'discount';
$seo_title = '商家优惠-';
include template('index', 'discount');
?>