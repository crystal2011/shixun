<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/buy/buy.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/share/index.php');
}

$oBuy = new buy(6);
$oBuy->itemid = $id;
$aBuy = $oBuy->get_one();
if(isset($showtype)){   //预览
    $s = chcekHorn($aBuy,5);
    if($s!==true) dalert($s,$forward);
}else {
    if(!$oBuy->checkBuy($aBuy)) {
        dalert($oBuy->errmsg, '/share/index.php');
    }
    //更新浏览量
    $oBuy->editHits();
    addHits($aBuy['userid']);
}

$aHotFood = $oBuy->getLevelBuy('title,itemid,introduce,addtime',11,0,4); //热门
$aRecommendFood = $oBuy->getLevelBuy('title,itemid,addtime',10,0,5);  //推荐
$catname='美食分享';
$foodshowright = 56;

$truenameshow = get_user($aBuy['userid'],'userid','truename');
$truenameshow = $truenameshow?$truenameshow:'匿名';

//评论
require_once '../module/extend/comment.class.php';
$oComment = new comment;
$typeid = 6;
list($aComment,$HasNextPage) = $oComment->commentList($id,$typeid);

$nav_selected = 'share';
$seo_title = $aBuy['title'].'-美食分享-';
include template('show', 'share');
?>