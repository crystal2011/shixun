<?php
/**
 * 手机端-名厨学堂-名厨详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/member/member.class.php';
$oMember = new member();

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/school/list.php');
}

$oMember->userid = $id;
$info = $oMember->get_one();
if(!$oMember->checkKnow($info)){
    dalert($oMember->errmsg,'/mobile/school/index.php');
}
$info['content'] = $info['introduce'];
$info['title'] = $info['truename'];
$info['company'] = '';
$info['telephone'] = $info['mobile'];
//获取该名称的餐饮列表
require_once '../../module/know/know.class.php';
$oCai = new know(10);
list($list,$totalpage) = $oCai->schoolList('*','userid = '.$id.' and status=3','addtime desc','2');


$aRecommendFood = $oCai->getright('title,itemid,introduce,likes,hits,userid,thumb,votes',10,'addtime desc');  //最新
$memberlistRec = $oMember->getListUser($aRecommendFood);


//更新浏览量
$oMember->editHits();
$seo_title = $info['truename'] . '-名厨学堂-名厨风采-';
$topname = '名厨学堂-名厨风采';
include template('school/show', 'mobile');