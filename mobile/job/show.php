<?php
/**
 * 手机端-招聘信息-详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/job/job.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/job/index.php');
}

$oJob = new job(9);
$oJob->itemid = $id;
$info = $oJob->get_one();
if(isset($showtype)){   //预览
    $s = chcekHorn($info,2);
    if($s!==true) dalert($s,$forward);
}else {
    if (!$checkJob = $oJob->checkJob($info)) {
        dalert($oJob->errmsg, '/mobile/job/index.php');
    }
    $oJob->editHits();  //更新浏览量
    addHits($info['userid']);
}

$aHotFood = $oJob->getright('title,itemid,introduce,thumb,hits,likes',3,'hits desc'); //热门
$aRecommendFood = $oJob->getright('title,itemid,introduce,thumb,hits,likes',3,'addtime desc');  //推荐
$commenttypeid = 3;
$seo_title = $info['title'].'-招聘信息-';
$topname = '招聘信息';
$likenum = 3;
$moduleidtype = 2;
include template('job/show','mobile');