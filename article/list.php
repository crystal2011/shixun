<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';

//文章
$pagesize = 10;
$offset = ($page-1)*$pagesize;
require_once '../module/article/article.class.php';
$oArticle = new article(21);
$where = ' 1 = 1 ';
if($catid && $catinfo = get_cat($catid)){
    $where = " catid in (".$catinfo['arrchildid'].") ";
}
$aArticle = $oArticle->get_list($where .' and status = 3');
$totalpage = ceil($items/$pagesize);

$aHotFood = $oArticle->getright('title,itemid,introduce,addtime',11,'hits desc'); //热门
$aRecommendFood = $oArticle->getright('title,itemid,addtime',10,'addtime desc');  //推荐
$catname='文章';
$foodshowright = 76;
$nav_selected = 'article';

$seo_title = '文章-';
include template('list','article');
?>