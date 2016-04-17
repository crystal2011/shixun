<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/job/job.class.php';

$where = '1=1';
//地区
$areaid = $areaid?$areaid:($dtcity?$dtcity['areaid']:0);
$aCacheArea = cache_read('area.php');
$areainfo = isset($aCacheArea[$areaid])?$aCacheArea[$areaid]:array();
if($areainfo){
    $where .= " and areaid in (".$areainfo['arrchildid'].")";
}

//分类
$aCacheCat9 = cache_read('category-9.php');
$catinfo = isset($aCacheCat9[$catid])?$aCacheCat9[$catid]:array();
if($catinfo){
    $where .= " and catid in (".$catinfo['arrchildid'].")";
}
$action = isset($action)?$action:'';

$oJob = new job(9);
if(empty($action)){
    list($aJob,$totalpage) = $oJob->jobList('title,userid,itemid,addtime,areaid,maxsalary,minsalary,company',$where.' and status=3 ','addtime desc','20');
}else{
    require_once '../module/job/resume.class.php';
    $aSetting = cache_read('module-9.php');
    $aSetting['education'] = explode('|',$aSetting['education']);
    $oResume = new resume(9);
    list($aJob,$totalpage) = $oResume->jobList('*',$where.' and situation = 1 and status=3 ','edittime desc','20');
}


//列表导航
$aTou[] = array('url'=>'/job/index.php?action='.$action,'name'=>'全部');
if($CAT) $aTou[] = array('url'=>'/job/index.php?action='.$action.'&areaid='.$areaid,'name'=>$CAT['catname']);
if($ARE) $aTou[] = array('url'=>'/job/index.php?action='.$action.'catid='.$catid,'name'=>$ARE['areaname']);

$aAreaFirst = showArea($areainfo);  //列表选择显示地区
$aCatList = showCat($catinfo,9);     //列表选择显示分类
$aHotCity = cache_read('hotcity.php');  //热门地区

$aHotFood = $oJob->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oJob->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$catname='招聘';
$foodshowright = 33;

$nav_selected = 'job';
$seo_title = '餐饮招聘-';
include template('index', 'job');
?>