<?php
/**
 * 手机端-分享-详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/buy/buy.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/share/index.php');
}

$oBuy = new buy(6);
$oBuy->itemid = $id;
$info = $oBuy->get_one();
if(isset($showtype)){   //预览
    $s = chcekHorn($info,5);
    if($s!==true) dalert($s,$forward);
}else {
    if(!$oBuy->checkBuy($info)) {
        dalert($oBuy->errmsg, '/mobile/share/index.php');
    }
    //更新浏览量
    $oBuy->editHits();
    addHits($info['userid']);
}

$aHotFood = $oBuy->getright('title,itemid,introduce,hits,likes,thumb',3,'hits desc'); //热门
$aRecommendFood = $oBuy->getright('title,itemid,introduce,hits,likes,thumb',3,'addtime desc');  //推荐
$seo_title = $info['title'].'-分享-';
$topname = '分享';
include template('share/show','mobile');