<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/job/job.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/job/index.php');
}

$oJob = new job(9);
$oJob->itemid = $id;
$aJob = $oJob->get_one();
if(isset($showtype)){   //预览
    $s = chcekHorn($aJob,2);
    if($s!==true) dalert($s,$forward);
}else {
    if (!$checkJob = $oJob->checkJob($aJob)) {
        dalert($oJob->errmsg, '/job/index.php');
    }
    addHits($aJob['userid']);
}


$aHotFood = $oJob->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oJob->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$catname='招聘';
$foodshowright = 33;


$aSetting = cache_read('module-9.php');
$aSetting['education'] = explode('|',$aSetting['education']);
$aSetting['type'] = explode('|',$aSetting['type']);

//更新浏览量
$oJob->editHits();

//评论
require_once '../module/extend/comment.class.php';
$oComment = new comment;
$typeid = 3;
list($aComment,$HasNextPage) = $oComment->commentList($id,$typeid);


$nav_selected = 'job';
$seo_title = $aJob['title'].'-招聘信息-';
include template('show', 'job');
?>