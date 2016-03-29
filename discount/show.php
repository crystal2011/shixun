<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/brand/brand.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/discount/index.php');
}

$oBrand = new brand(9);
$oBrand->itemid = $id;
$aBrand = $oBrand->get_one();
if(isset($showtype)){   //预览
    $s = chcekHorn($aBrand,1);
    if($s!==true) dalert($s,$forward);

}else {
    if (!$oBrand->checkJob($aBrand)) {
        dalert($oBrand->errmsg, '/discount/index.php');
    }
    //更新浏览量
    $oBrand->editHits();
    addHits($aBrand['userid']);
}

$aHotFood = $oBrand->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oBrand->getright('title,itemid,addtime',10,'address desc');  //推荐
$catname = '优惠';
$foodshowright = 43;


//评论
require_once '../module/extend/comment.class.php';
$oComment = new comment;
$typeid = 2;
list($aComment,$HasNextPage) = $oComment->commentList($id,$typeid);

$nav_selected = 'discount';
$seo_title = $aBrand['title'].'-商家优惠-';
include template('show', 'discount');
?>