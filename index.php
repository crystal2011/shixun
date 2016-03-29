<?php
require 'common.inc.php';

require_once 'module/member/member.class.php';
$oMember = new member();

$aCatList23 = getCacheList(23);

//大块广告
require_once 'module/extend/ad.class.php';
$oAd = new ad;
$aAd = $oAd->moread(array(60,61,62,63,64,65,66,67,68));


//文章
require_once 'module/article/article.class.php';
$oArticle = new article(21);
$mygetcount = false;
$pagesize = 1;
$aNewsNew = $oArticle->get_list('level=1 and status = 3'); //最新新闻
$pagesize = 1;
$aNewsHot = $oArticle->get_list('level=2 and status = 3'); //今日热门
$pagesize = 1;
$aNewsHotPromote = $oArticle->get_list('level=3 and status = 3'); //今日热门推荐

$pagesize = 12;
$aNewsHotPromoteCor = $oArticle->get_list('level=4 and status = 3'); //今日热门相关
$aNewsHotPromoteCor = array_chunk($aNewsHotPromoteCor,3);
$catPromote1 = $db->get_one("select catname,catid,arrchildid from {$db->pre}category where promote1 = 1");
if($catPromote1){
    $arrchildid = $catPromote1['arrchildid'];
    $pagesize = 4;
    $aNewsHotCor = $oArticle->get_list("catid in ($arrchildid) and level=5 and status = 3"); //推荐文章
}

$catPromote2 = $db->get_one("select catname,catid,arrchildid from {$db->pre}category where promote2 = 1");
if($catPromote2){
    $arrchildid = $catPromote2['arrchildid'];
    $pagesize = 2;
    $aNewsPromote = $oArticle->get_list(" catid in ($arrchildid) and level=5 and status = 3 "); //推荐文章
    $pagesize = 8;
    $aNewsPromote_Cor = $oArticle->get_list("catid in ($arrchildid) and level=6 and status = 3"); //推荐相关
}


$where = ' 1=1 ';
if(isset($dtcity['areaid'])){
    $areainfo = get_area($dtcity['areaid']);
    $arrchildid = $areainfo['arrchildid'];
    $where = " areaid in ($arrchildid)";
}


//名厨
require_once 'module/know/know.class.php';
$oCai = new know();
$pagesize = 4;
$aCai = $oCai->get_list($where.' and status = 3');
$aSchool = $oMember->getListUser($aCai);

//商家优惠
require_once 'module/brand/brand.class.php';
$oBrand = new brand();
$pagesize =1;
$aBrandHot = $oBrand->get_list($where.' and level=1 and status = 3');
$pagesize =10;
$aBrandHotCor = $oBrand->get_list($where.' and level=2 and status = 3');

//在线分享
require_once 'module/buy/buy.class.php';
$oBuy = new buy();
$pagesize=1;
$aButHot = $oBuy->get_list($where.' and level = 1 and status = 3');
$ar22 = array();
$where1 = $where;
if($aButHot){

    $memberinfo = $oMember->get_one('userid = '.$aButHot[0]['userid'],'username');
    $aButHot[0]['username'] = $memberinfo['username'];
    $where1 = $where.' and itemid != '.$aButHot[0]['itemid'];
    $ar22[] = $aButHot[0]['itemid'];
}
$pagesize=6;
$aBuyHotCor = $oBuy->get_list($where1.' and status = 3 ');
if($aBuyHotCor){
    foreach($aBuyHotCor as $k=>$v){
        $ar22[] = $v['itemid'];
    }
}
$where2 = $where;
if($ar22){
    $itemidsss = implode(',',$ar22);
    $where2 =  $where.' and itemid not in ('.$itemidsss.') ';
}
$pagesize=6;
$aBuyPormote = $oBuy->get_list($where2.' and status = 3');

//羊角会
require_once 'module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';
$pagesize=6;
$aSpecial = $oSpecial->get_list('level = 1 and status = 3');
$memberlist = $oMember->getListUser($aSpecial);

$seo_title = '';
include template('index');
?>