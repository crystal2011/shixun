<?php
/**
 * 手机端-店铺优惠-首页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/brand/brand.class.php';

$where = '1=1';
//地区
$areaid = $areaid?$areaid:($dtcity?$dtcity['areaid']:0);
$aCacheArea = cache_read('area.php');
$areainfo = isset($aCacheArea[$areaid])?$aCacheArea[$areaid]:array();
if($areainfo){
    $where .= " and areaid in (".$areainfo['arrchildid'].")";
}

//分类
$aCacheCat13 = cache_read('category-13.php');
$catinfo = isset($aCacheCat13[$catid])?$aCacheCat13[$catid]:array();
if($catinfo){
    $where .= " and catid in (".$catinfo['arrchildid'].")";
}

$oBrand = new brand(13);
list($list,$totalpage) = $oBrand->brandList('title,userid,itemid,thumb,areaid,address,comments,likes,hits,addtime',$where.' and status=3 ','addtime desc','10');

$action=isset($action)?$action:'';

if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<div class="content">
                        <div class="clear info">
                            <a class="thumb" href="show.php?id='.$v['itemid'].'"><img src="'.$v['thumb'].'" /></a>
                            <div class="text">
                                <a class="title"  href="show.php?id='.$v['itemid'].'">'.$v['title'].'</a>
                                <p class="introduce">'.area_pos($v['areaid'],'').$v['address'].'</p>
                                <p class="numviesshow">
                                    <span class="fl numtimes">
                                        '.date('Y-m-d',$v['addtime']).'
                                    </span>
                                    <span class="numsview">
                                        <img src="'.DT_SKIN.'image/public/icon-liulan.png" />浏览('.$v['hits'].')&nbsp;&nbsp;&nbsp;
                                        <img src="'.DT_SKIN.'image/public/icon-pinlun.png" />评论('.$v['comments'].')
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else{
    $aArea = showAllArea($areainfo);  //列表选择显示地区
    $aCatList = array_chunk(showCat($catinfo,13),4);     //列表选择显示分类
    $areaparentid = $areainfo?explode(',',$areainfo['arrparentid'].','.$areainfo['areaid']):array();
    $seo_title = '商家优惠-';
    $topname = '商家优惠';
    $moduleidtype = 1;
    include template('discount/index','mobile');
}


