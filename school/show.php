<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/member/member.class.php';
$oMember = new member();

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/school/index.php');
}

$oMember->userid = $id;
$aKnow = $oMember->get_one();
if(!$oMember->checkKnow($aKnow)){
    dalert($oMember->errmsg,'/school/index.php');
}

require_once '../module/know/know.class.php';
$oCai = new know(10);
list($aCai,$totalpage) = $oCai->schoolList('*','userid = '.$id.' and status=3','addtime desc','20');

//更新浏览量
$oMember->editHits();

//评论
require_once '../module/extend/comment.class.php';
$oComment = new comment;
$typeid = 11;
list($aComment,$HasNextPage) = $oComment->commentList($id,$typeid);

$aHotFood = $oMember->getright('*',11,'hits desc'); //热门
if($aHotFood){
    foreach($aHotFood as $k=>$v){
        $aHotFood[$k]['title'] = $v['truename'];
        $aHotFood[$k]['itemid'] = $v['userid'];
        $aHotFood[$k]['addtime'] = $v['regtime'];
    }
}
$aRecommendFood = $oMember->getright('*',10,'regtime desc');  //推荐
if($aRecommendFood){
    foreach($aRecommendFood as $k=>$v){
        $aRecommendFood[$k]['title'] = $v['truename'];
        $aRecommendFood[$k]['itemid'] = $v['userid'];
        $aRecommendFood[$k]['addtime'] = $v['regtime'];
    }
}
$catname='名厨学堂';
$norightimg = true;

$nav_selected = 'school';
$seo_title = $aKnow['truename'].'-名厨学堂-';
include template('show', 'school');
?>