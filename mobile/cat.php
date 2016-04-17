<?php
/**
 * 手机端-分类
 */
define('DT_REWRITE', true);
require '../common.inc.php';
//餐饮供应分类
$aCatList23 = array_chunk(showCat(array(),23),4);
//菜系分类
$aCatList13 = array_chunk(showCat(array(),13),4);
//招聘分类
$aCatList9 = array_chunk(showCat(array(),9),4);

$topname = '分类';
$seo_title = '分类-';
$backurl = '/mobile/index.php';
include template('cat','mobile');