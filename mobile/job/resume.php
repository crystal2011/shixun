<?php
/**
 * 手机端-求职信息-首页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/job/resume.class.php';
$where = '1=1';
//地区
$areaid = $areaid?$areaid:($dtcity?$dtcity['areaid']:0);
$aCacheArea = cache_read('area.php');
$areainfo = isset($aCacheArea[$areaid])?$aCacheArea[$areaid]:array();
if($areainfo){
    $where .= " and areaid in (".$areainfo['arrchildid'].")";
}
$moduleidcat = 9;
//分类
$aCacheCat9 = cache_read('category-9.php');
$catinfo = isset($aCacheCat9[$catid])?$aCacheCat9[$catid]:array();
if($catinfo){
    $where .= " and catid in (".$catinfo['arrchildid'].")";
}

$oResume = new resume(9);
list($list,$totalpage) = $oResume->jobList('truename,education,itemid,catid,areaid,experience',$where.' and situation = 1 and status=3 ','edittime desc','20');
$aSetting = cache_read('module-9.php');
$aSetting['education'] = explode('|',$aSetting['education']);
if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $catnamesd = isset($aCacheCat9[$v['catid']])?$aCacheCat9[$v['catid']]['catname']:'';
            $experiencesd = $v['experience']?$v['experience'].'年':'暂无';
            $info .= '<tr>
                        <td><a href="resumeshow.php?id='.$v['itemid'].'">'.dsubstr($v['title'],16).'</a></td>
                        <td align="center"><a href="resumeshow.php?id='.$v['itemid'].'">'.$catnamesd.'</a></td>
                        <td align="center"><a href="resumeshow.php?id='.$v['itemid'].'">'.$aSetting['education'][$v['education']].'</a></td>
                        <td align="center"><a href="resumeshow.php?id='.$v['itemid'].'">'.$experiencesd.'</a></td>
                    </tr>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else{
    $aArea = showAllArea($areainfo);  //列表选择显示地区
    $aCatList = array_chunk(showCat($catinfo,9),4);     //列表选择显示分类
    $areaparentid = $areainfo?explode(',',$areainfo['arrparentid'].','.$areainfo['areaid']):array();
    $seo_title = '求职信息-';
    $topname = '求职信息';
    $backurl = '/mobile/job/index.php';
    $moduleidtype = 2;
    dsubstr()
    include template('job/resume','mobile');
}
