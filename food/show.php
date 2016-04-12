<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/food/food.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/food/index.php');
}

$oFood = new food(23);
$oFood->itemid = $id;
$aFood = $oFood->get_one();
if(isset($showtype)){   //羊角会预览
    $s = chcekHorn($aFood,0);
    if($s!==true) dalert($s,$forward);
}else {
    if (!$checkFood = $oFood->checkFood($aFood)) {
        dalert($oFood->errmsg, '/food/index.php');
    }
    $oFood->editHits();  //更新浏览量
    addHits($aFood['userid']);
}

$foodshowright = 32; //广告
$aHotFood = $oFood->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oFood->getright('title,itemid,addtime',10,'addtime desc');     //推荐
$catname = '餐饮';

//评论
require_once '../module/extend/comment.class.php';
$oComment = new comment;
$typeid = 1;
list($aComment,$HasNextPage) = $oComment->commentList($id,$typeid);

$nav_selected = 'food';
$seo_title = $aFood['title'].'-餐饮供应-';
include template('show', 'food');
?>