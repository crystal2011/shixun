<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';


$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/school/foodlist.php');
}

require_once '../module/know/know.class.php';
$oCai = new know;
$oCai->itemid = $id;
$aCai = $oCai->get_one();
if(isset($showtype)){   //预览
    $s = chcekHorn($aCai,4);
    if($s!==true) dalert($s,$forward);
}else {
    if (!$oCai->checkKnow($aCai)) {
        dalert($oCai->errmsg, '/school/foodlist.php');
    }
    //更新浏览量
    $oCai->editHits();
    addHits($aCai['userid']);
}
require_once '../module/member/member.class.php';
$oMember = new member();
$oMember->userid = $aCai['userid'];
$aKnow = $oMember->get_one();


$aHotFood = $oCai->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oCai->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$catname='菜系';
$foodshowright = 43;



//评论
require_once '../module/extend/comment.class.php';
$oComment = new comment;
$typeid = 5;
list($aComment,$HasNextPage) = $oComment->commentList($id,$typeid);


$nav_selected = 'school';
$nav_show = 'food.php';
$seo_title = $aCai['title'].'-菜系-名厨学堂-';
include template('food', 'school');
?>