<?php
/**
 * 手机端-首页
 */
define('DT_REWRITE', true);
require '../common.inc.php';
//最新新闻
require_once '../module/article/article.class.php';
$oArticle = new article(21);
$mygetcount = false;
$pagesize = 3;
$aNewsNew = $oArticle->get_list('status = 3');

require_once '../module/member/member.class.php';
$oMember = new member();

$where = ' 1=1 ';
if(isset($dtcity['areaid'])){
    $areainfo = get_area($dtcity['areaid']);
    $arrchildid = $areainfo['arrchildid'];
    $where = " areaid in ($arrchildid)";
}

//名厨
require_once '../module/know/know.class.php';
$oCai = new know();
$pagesize = 4;
$aCai = $oCai->get_list($where.' and status = 3');
$aSchool = $oMember->getListUser($aCai);

//在线分享
require_once '../module/buy/buy.class.php';
$oBuy = new buy(6);
$aHotFood = $oBuy->getright('title,itemid,thumb',6,'addtime desc');

include template('index','mobile');