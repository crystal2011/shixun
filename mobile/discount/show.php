<?php
/**
 * 手机端-商家优惠-详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/brand/brand.class.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/discount/index.php');
}

$oBrand = new brand(9);
$oBrand->itemid = $id;
$info = $oBrand->get_one();
if(isset($showtype)){   //预览
    $s = chcekHorn($info,1);
    if($s!==true) dalert($s,$forward);

}else {
    if (!$oBrand->checkJob($info)) {
        dalert($oBrand->errmsg, '/mobile/discount/index.php');
    }
    //更新浏览量
    $oBrand->editHits();
    addHits($info['userid']);
}
$commenttypeid = 2;
$aHotFood = $oBrand->getright('title,itemid,introduce,thumb,hits,likes,address,areaid,comments,addtime',3,'hits desc'); //热门
$aRecommendFood = $oBrand->getright('title,itemid,introduce,thumb,hits,likes,address,areaid,comments,addtime',3,'address desc');  //推荐
//当时发布的code
require_once '../../module/special/special.class.php';
$oSpecial = new special(11);
$codetishicode = $oSpecial->getShowCode($id,1);
$likenum = 2;
$moduleidtype = 1;
$seo_title = $info['title'].'-商家优惠-';
$topname = '商家优惠';
include template('discount/show','mobile');