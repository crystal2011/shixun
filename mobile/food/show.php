<?php
/**
 * 手机端-食品供应-详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/food/food.class.php';

$isajax = isset($isajax)?true:false;
if($isajax){  //用户发布、编辑预览
    $info = array_map('trim',dhtmlspecialchars(dstripslashes($_POST)));
    $codetishicode = $info['code'];
    $likenum = 1;
    $commenttypeid = 1;
    $moduleidtype = 0;
    $seo_title = $info['title'].'-餐饮供应-预览-';
    $topname = '餐饮供应-预览';
    include template('food/show','mobile');
}

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/food/index.php');
}

$oFood = new food(23);
$oFood->itemid = $id;
$info = $oFood->get_one();
if(isset($showtype)){   //羊角会预览
    $s = chcekHorn($info,0);
    if($s!==true) dalert($s,$forward);
}else {
    if (!$checkFood = $oFood->checkFood($info)) {
        dalert($oFood->errmsg, '/mobile/food/index.php');
    }
    $oFood->editHits();  //更新浏览量
    addHits($info['userid']);
}


$aRecommendFood = $oFood->getright('title,itemid,price,introduce,likes,hits,unit,thumb',10,'addtime desc');     //推荐

//当时发布的code
require_once '../../module/special/special.class.php';
$oSpecial = new special(11);
$codetishicode = $oSpecial->getShowCode($id,0);

$likenum = 1;
$commenttypeid = 1;
$moduleidtype = 0;
$seo_title = $info['title'].'-餐饮供应-';
$topname = '餐饮供应';
include template('food/show','mobile');