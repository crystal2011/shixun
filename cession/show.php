<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/sell/sell.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/cession/index.php');
}

$oSell = new sell(5);
$oSell->itemid = $id;
$aSell= $oSell->get_one();
if(isset($showtype)){   //预览
    $s = chcekHorn($aSell,3);
    if($s!==true) dalert($s,$forward);
}else{
    if(!$oSell->checkSell($aSell)){
        dalert($oSell->errmsg,'/cession/index.php');
    }
    //更新浏览量
    $oSell->editHits();
    addHits($aSell['userid']);
}

$aHotFood = $oSell->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oSell->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$catname='店铺转让';
$foodshowright = 51;


//评论
require_once '../module/extend/comment.class.php';
$oComment = new comment;
$typeid = 4;
list($aComment,$HasNextPage) = $oComment->commentList($id,$typeid);


$nav_selected = 'cession';
$seo_title = $aSell['title'].'-店铺转让-';
include template('show', 'cession');
?>