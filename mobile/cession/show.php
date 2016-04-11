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
$likenum = 4;
$commenttypeid = 4;

$aRecommendFood = $oSell->getright('title,itemid,hits,likes,thumb,price,address,areaid,addtime,comments',10,'addtime desc');  //推荐

//当时发布的code
require_once '../../module/special/special.class.php';
$oSpecial = new special(11);
$codetishicode = $oSpecial->getShowCode($id,3);

$seo_title = $info['title'].'-店铺转让-';
$topname = '店铺转让';
$moduleidtype = 3;
include template('cession/show','mobile');