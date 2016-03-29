<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/special/special.class.php';



$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/horn/index.php');
}


$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';
$oSpecial->itemid = $id;
$aSpecial = $oSpecial->get_one();
if(!$oSpecial->checkSpecial($aSpecial)){
    dalert($oSpecial->errmsg,'/horn/index.php');
}

$aHotFood = $oSpecial->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oSpecial->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$catname='羊角会成员';
$foodshowright = 56;

//更新浏览量
$oSpecial->editHits();

//评论
require_once '../module/extend/comment.class.php';
$oComment = new comment;
$typeid = 8;
list($aComment,$HasNextPage) = $oComment->commentList($id,$typeid);

$nav_selected = 'horn';
$nav_show = 'hornshow.php';
$seo_title = $aSpecial['title'].'-羊角会-';
include template('hornshow', 'horn');
?>