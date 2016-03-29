<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/info/info.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/horn/index.php');
}


$oInfo = new info(24);
$oInfo->table = $db->pre.'info_24';
$oInfo->table_data = $db->pre.'info_data_24';
$oInfo->itemid = $id;
$aInfo = $oInfo->get_one();
if(!$checkInfo = $oInfo->checkInfo($aInfo)){
    dalert($oInfo->errmsg,'/horn/index.php');
}

$aHotFood = $oInfo->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oInfo->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$catname='羊角会';
$foodshowright = 39;

//更新浏览量
$oInfo->editHits();

//评论
require_once '../module/extend/comment.class.php';
$oComment = new comment;
$typeid = 7;
list($aComment,$HasNextPage) = $oComment->commentList($id,$typeid);

$nav_selected = 'horn';
$seo_title = $aInfo['title'].'-羊角会-';
include template('show', 'horn');
?>