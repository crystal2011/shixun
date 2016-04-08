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

list($list,$totalpage) = $oMember->memberList('truename,userid,thumb,infonums,introduce,hits',$where.' and groupid=5 and ischu=1','regtime desc','14');

if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<li class="clear">
                    <div class="lidiv">
                        <a class="schoola" href="show.php?id='.$v['userid'].'">
                            <img src="'.($v['thumb']?$v['thumb']:$DT['memberlogo']).'" />
                        </a>
                        <a class="title clear" href="show.php?id='.$v['userid'].'">
                            <span class="fl">名厨：'.$v['truename'].'</span>
                            <span class="numsviews">
                                <img src="'.DT_SKIN.'image/public/icon-liulan.png" />('.$v['hits'].')
                            </span>
                        </a>
                        <p class="handle clear">
                            <i class="num">票数：<i>'.$v['infonums'].'</i></i>
                        </p>
                    </div>
                </li>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else {
    $aArea = showAllArea($areainfo);  //列表选择显示地区
    $areaparentid = $areainfo ? explode(',', $areainfo['arrparentid'] . ',' . $areainfo['areaid']) : array();
    $moduleidtype = 4;
    $seo_title = '名厨学堂-';
    $topname = '名厨学堂-名厨风采';
    include template('school/index', 'mobile');
}