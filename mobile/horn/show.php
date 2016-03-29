<?php
/**
 * 手机端-羊角会-详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/info/info.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/horn/index.php');
}


$oInfo = new info(24);
$oInfo->table = $db->pre.'info_24';
$oInfo->table_data = $db->pre.'info_data_24';
$oInfo->itemid = $id;
$info = $oInfo->get_one();
if(!$checkInfo = $oInfo->checkInfo($info)){
    dalert($oInfo->errmsg,'/mobile/horn/index.php');
}

$aHotFood = $oInfo->getright('title,itemid,addtime',10,'hits desc'); //热门
$aRecommendFood = $oInfo->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$seo_title = $info['title'].'-羊角会-';
$topname = '羊角会';
include template('horn/show','mobile');