<?php
/**
 * 手机端-食品供应-首页
 */

require '../../common.inc.php';
require_once '../../module/food/food.class.php';

$where = '1=1';
//地区
$areaid = $areaid?$areaid:($dtcity?$dtcity['areaid']:0);
$aCacheArea = cache_read('area.php');
$areainfo = isset($aCacheArea[$areaid])?$aCacheArea[$areaid]:array();
if($areainfo){
    $where .= " and areaid in (".$areainfo['arrchildid'].")";
}

//分类
$aCacheCat23 = cache_read('category-23.php');

$catinfo = isset($aCacheCat23[$catid])?$aCacheCat23[$catid]:array();
if($catinfo){
    $where .= " and catid in (".$catinfo['arrchildid'].")";
}

$kw = isset($kw)?$kw:'';
if(strlen($kw)!=0){
    $where .= " and title like '%".$kw."%' ";
}

$oFood = new food(23);
list($list,$totalpage) = $oFood->foodList('thumb,title,price,unit,userid,itemid,likes,hits,introduce',$where.' and status=3','addtime desc','10');

$action=isset($action)?$action:'';

if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<li class="clear">
                        <div class="lidiv">
                            <a class="schoola" href="/mobile/food/show.php?id='.$v['itemid'].'">
                                <img src="'.$v['thumb'].'" />
                            </a>
                            <a class="title" href="/mobile/food/show.php?id='.$v['itemid'].'">'.$v['title'].'</a>
                            <p class="handle clear">
                                <i class="num pricesdf"><i>'.doubleval($v['price']).'元/'.$v['unit'].'</i></i>
                            </p>
                        </div>
                    </li>
                    ';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else{
    $aArea = showAllArea($areainfo);  //列表选择显示地区
    $aCatList = array_chunk(showCat($catinfo,23),4);     //列表选择显示分类
    $areaparentid = $areainfo?explode(',',$areainfo['arrparentid'].','.$areainfo['areaid']):array();
    $seo_title = '餐饮供应-';
    $topname = '餐饮供应';
    $moduleidtype = 0;
    include template('food/index','mobile');
}

