<?php
/**
 * 手机端-求职信息-详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/job/resume.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/job/resume.php');
}

$oResume = new resume(9);
$oResume->itemid = $id;
$info = $oResume->get_one();

if (!$checkJob = $oResume->checkBuy($info)) {
    dalert($oJob->errmsg, '/mobile/job/resume.php');
}
$oResume->editHits();  //更新浏览量

$aRecommendFood = $oResume->getright('*',10,'addtime desc');  //推荐

$aCacheCat9 = cache_read('category-9.php');

$aSetting = cache_read('module-9.php');
$aSetting['education'] = explode('|',$aSetting['education']);
$aSetting['type'] = explode('|',$aSetting['type']);

$seo_title = $info['truename'].'-求职信息-';
$topname = '求职信息';

include template('job/resumeshow','mobile');