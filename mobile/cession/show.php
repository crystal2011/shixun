<?php
/**
 * 手机端-店铺转让-详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/sell/sell.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/cession/index.php');
}

$oSell = new sell(5);
$oSell->itemid = $id;
$info= $oSell->get_one();
if(isset($showtype)){   //预览
    $s = chcekHorn($info,3);
    if($s!==true) dalert($s,$forward);
}else{
    if(!$oSell->checkSell($info)){
        dalert($oSell->errmsg,'/mobile/cession/index.php');
    }
    //更新浏览量
    $oSell->editHits();
    addHits($info['userid']);
}

$aHotFood = $oSell->getright('title,itemid,introduce,hits,likes,thumb,price',3,'hits desc'); //热门
$aRecommendFood = $oSell->getright('title,itemid,introduce,hits,likes,thumb,price',3,'addtime desc');  //推荐
$seo_title = $info['title'].'-店铺转让-';
$topname = '店铺转让';
include template('cession/show','mobile');