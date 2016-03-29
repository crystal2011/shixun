<?php
/**
 * 手机端-食品供应-详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/food/food.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/food/index.php');
}

$oFood = new food(23);
$oFood->itemid = $id;
$info = $oFood->get_one();
if(isset($showtype)){   //预览
    $s = chcekHorn($info,0);
    if($s!==true) dalert($s,$forward);
}else {
    if (!$checkFood = $oFood->checkFood($info)) {
        dalert($oFood->errmsg, '/mobile/food/index.php');
    }
    $oFood->editHits();  //更新浏览量
    addHits($info['userid']);
}

$aHotFood = $oFood->getright('title,itemid,price,introduce,likes,hits,unit,thumb',3,'hits desc'); //热门
$aRecommendFood = $oFood->getright('title,itemid,price,introduce,likes,hits,unit,thumb',3,'addtime desc');     //推荐
$seo_title = $info['title'].'-餐饮供应-';
$topname = '餐饮供应';
include template('food/show','mobile');