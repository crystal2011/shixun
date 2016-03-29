<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/article/article.class.php';
$oArticle = new article(21);

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/article/list.php');
}

$oArticle->itemid = $id;
$aArticle= $oArticle->get_one();

if(isset($showtype)){   //预览
    $s = chcekHorn($aArticle,6);
    if($s!==true) dalert($s,$forward);
}else{
    if(!$oArticle->checkArticle($aArticle)){
        dalert($oArticle->errmsg,'/article/list.php');
    }
    //更新浏览量
    $oArticle->editHits();
    addHits($aArticle['userid']);
}

$aHotFood = $oArticle->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oArticle->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$catname='文章';
$foodshowright = 76;
$nav_selected = 'article';

include template('show', 'article');
?>