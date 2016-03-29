<?php
/**
 * 手机端-名厨学堂-厨师列表
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/member/member.class.php';
$oMember = new member();

$where = '1=1';
//地区
$areaid = $areaid?$areaid:($dtcity?$dtcity['areaid']:0);
$aCacheArea = cache_read('area.php');
$areainfo = isset($aCacheArea[$areaid])?$aCacheArea[$areaid]:array();
if($areainfo){
    $where .= " and areaid in (".$areainfo['arrchildid'].")";
}

list($list,$totalpage) = $oMember->memberList('truename,userid,thumb,infonums,introduce,hits',$where.' and groupid=5 and ischu=1','regtime desc','6');

if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<div class="content">
                        <div class="clear info">
                            <a class="thumb" href="/mobile/school/show.php?id='.$v['userid'].'"><img src="'.($v['thumb']?$v['thumb']:$DT['memberlogo']).'" /></a>
                            <div class="text">
                                <a class="title" href="/mobile/school/show.php?id='.$v['userid'].'">'.$v['truename'].'</a>
                                <p class="introduce">'.$v['introduce'].'</p>
                            </div>
                        </div>
                        <div class="handle clear hasbutton">
                            <span class="handle1"><img src="'.DT_SKIN.'image/mobile/good.png" />'.$v['infonums'].'</span>
                            <span><img src="'.DT_SKIN.'image/mobile/hit.png" />'.$v['hits'].'</span>
                            <a class="fr showpng" href="/mobile/school/show.php?id='.$v['userid'].'"><img src="'.DT_SKIN.'image/mobile/show.png" /></a>
                        </div>
                    </div>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else {
    $aArea = showAllArea($areainfo);  //列表选择显示地区
    $areaparentid = $areainfo ? explode(',', $areainfo['arrparentid'] . ',' . $areainfo['areaid']) : array();

    $seo_title = '名厨学堂-';
    $topname = '名厨学堂';
    include template('school/list', 'mobile');
}