<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/member/member.class.php';
$oMember = new member();

$where = '1=1';
//地区
$areaid = $areaid?$areaid:($dtcity?$dtcity['areaid']:0);
$aCacheArea = cache_read('area.php');
$areainfo = isset($aCacheArea[$areaid])?$aCacheArea[$areaid]:array();
if($areainfo){
    $where .= " and areaid in (".$areainfo['arrchildid'].")";
}

list($aKnow,$totalpage) = $oMember->memberList('truename,userid,thumb,infonums',$where.' and groupid=5 and ischu=1','regtime desc','20');

//列表导航
$aTou[] = array('url'=>'/school/list.php','name'=>'全部');
if($ARE) $aTou[] = array('url'=>'/school/list.php','name'=>$ARE['areaname']);

//热门推荐广告
require_once '../module/extend/ad.class.php';
$oAd = new ad;
$sAdPlaceHot = $oAd->getAdAllText(49);
$sAdPlaceTest = $oAd->getAdAllText(50);

$aAreaFirst = showArea($areainfo);  //列表选择显示地区
$aHotCity = cache_read('hotcity.php');  //热门地区


$aHotFood = $oMember->getright('truename as title,userid as itemid,introduce,regtime as addtime',11,'hits desc'); //热门
$aRecommendFood = $oMember->getright('truename as title,userid as itemid,introduce,regtime as addtime',10,'regtime desc');  //推荐
$catname='名厨';
$foodshowright = 43;

$nav_selected = 'school';
$seo_title = '名厨学堂-';
include template('list', 'school');
?>