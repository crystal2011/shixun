<?php
/**
 * 手机端-餐饮招聘-首页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/job/job.class.php';

$where = '1=1';
//地区
$areaid = $areaid?$areaid:($dtcity?$dtcity['areaid']:0);
$aCacheArea = cache_read('area.php');
$areainfo = isset($aCacheArea[$areaid])?$aCacheArea[$areaid]:array();
if($areainfo){
    $where .= " and areaid in (".$areainfo['arrchildid'].")";
}

//分类
$moduleidcat = 9;
$aCacheCat9 = cache_read('category-9.php');
$catinfo = isset($aCacheCat9[$catid])?$aCacheCat9[$catid]:array();
if($catinfo){
    $where .= " and catid in (".$catinfo['arrchildid'].")";
}

$oJob = new job(9);
list($list,$totalpage) = $oJob->jobList('title,itemid,company,addtime,areaid',$where.' and status=3 ','addtime desc','20');

if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<tr>
                        <td><a href="show.php?id='.$v['itemid'].'">'.dsubstr($v['title'],16).'</a></td>
                        <td><a href="show.php?id='.$v['itemid'].'">'.dsubstr($v['company'],8).'</a></td>
                        <td><a href="show.php?id='.$v['itemid'].'">面议</a></td>
                        <td><a href="show.php?id='.$v['itemid'].'">'.implode('/',array_slice(explode('/',area_pos($v['areaid'],'/')),1,2)).'</a></td>
                    </tr>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else{
    $aArea = showAllArea($areainfo);  //列表选择显示地区
    $aCatList = array_chunk(showCat($catinfo,9),4);     //列表选择显示分类
    $areaparentid = $areainfo?explode(',',$areainfo['arrparentid'].','.$areainfo['areaid']):array();
    $seo_title = '餐饮招聘-';
    $topname = '餐饮招聘';
    $moduleidtype = 2;
    $backurl = '/mobile/index.php';
    $isresume = true;
    include template('job/index','mobile');
}
