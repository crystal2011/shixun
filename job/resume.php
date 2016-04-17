<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/job/resume.class.php';


$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/share/index.php');
}

$oResume = new resume(9);
$oResume->itemid = $id;
$aJob = $oResume->get_one();
if(!$oResume->checkBuy($aJob)) {
    dalert($oResume->errmsg, '/share/index.php');
}
$aCacheCat9 = cache_read('category-9.php');
//更新浏览量
$oResume->editHits();

$aSetting = cache_read('module-9.php');
$aSetting['education'] = explode('|',$aSetting['education']);
$aSetting['type'] = explode('|',$aSetting['type']);

$aHotFood = $oResume->getright('itemid,introduce,addtime,catid',11,'hits desc'); //热门
if($aHotFood){
    foreach($aHotFood as $k=>$v){
        $aHotFood[$k]['title'] = isset($aCacheCat9[$v['catid']]['catname'])?$aCacheCat9[$v['catid']]['catname']:'';
    }
}
$aRecommendFood = $oResume->getright('itemid,addtime,catid',10,'addtime desc');  //推荐
if($aRecommendFood){
    foreach($aRecommendFood as $k=>$v){
        $aRecommendFood[$k]['title'] = isset($aCacheCat9[$v['catid']]['catname'])?$aCacheCat9[$v['catid']]['catname']:'';
    }
}
$nav_show = 'resume.php';
$catname='求职';
$foodshowright = 33;
$norightimg = true;
$snf = 'job';

//评论
require_once '../module/extend/comment.class.php';
$oComment = new comment;
$typeid = 10;
list($aComment,$HasNextPage) = $oComment->commentList($id,$typeid);

$nav_selected = 'job';
$seo_title = $aJob['truename'].'-求职信息-餐饮招聘-';
include template('resume', 'job');
?>