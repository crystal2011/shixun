<?php
/**
 * 手机端-文章模块-文章详情
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/article/article.class.php';
$oArticle = new article(21);

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/article/index.php');
}

$oArticle->itemid = $id;
$info= $oArticle->get_one();

if(isset($showtype)){   //预览
    $s = chcekHorn($info,6);
    if($s!==true) dalert($s,$forward);
}else{
    if(!$oArticle->checkArticle($info)){
        dalert($oArticle->errmsg,'/mobile/article/list.php');
    }
    //更新浏览量
    $oArticle->editHits();
    addHits($info['userid']);
}
$islikenum = true;
$aHotFood = $oArticle->getright('title,itemid,introduce,hits,thumb',3,'hits desc'); //热门
$aRecommendFood = $oArticle->getright('title,itemid,introduce,hits,thumb',3,'addtime desc');  //推荐
$seo_title = $info['title'].'-文章详情-';
$topname = '文章详情';
$commenttypeid = 9;
$moduleidtype = 6;
//当时发布的code
require_once '../../module/special/special.class.php';
$oSpecial = new special(11);
$codetishicode = $oSpecial->getShowCode($id,6);
include template('article/show','mobile');