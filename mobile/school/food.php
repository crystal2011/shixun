<?php
/**
 * 手机端-名厨学堂-菜系列表
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/know/know.class.php';

$where = '1=1';
//分类
$aCacheCat13 = cache_read('category-13.php');
$catinfo = isset($aCacheCat13[$catid])?$aCacheCat13[$catid]:array();
if($catinfo){
    $where .= " and catid in (".$catinfo['arrchildid'].")";
}

$oCai = new know(10);
list($list,$totalpage) = $oCai->schoolList('*',$where.' and status=3 ','votes desc','14');

require_once '../../module/member/member.class.php';
$oMember = new member();
$aKnow = $oMember->getListUser($list);

if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<li class="clear">
                <div class="lidiv">
                    <a class="schoola" href="/mobile/school/foodshow.php?id='.$v['itemid'].'">
                        <img src="'.$v['thumb'].'" />
                        <span>名厨：'.$aKnow[$v['userid']]['truename'].'</span>
                        <span class="hiddensl">名厨：'.$aKnow[$v['userid']]['truename'].'</span>
                    </a>
                    <a class="title" href="/mobile/school/foodshow.php?id='.$v['itemid'].'">'.$v['title'].'</a>
                    <p class="handle clear">
                        <img onclick="vote('.$v['itemid'].',2,this)" class="votes" src="'.DT_SKIN.'image/mobile/vote.png" />
                        <i class="num">票数：<i>'.$v['votes'].'</i></i>
                    </p>
                </div>
            </li>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else{
    $aCatList = array_chunk(showCat($catinfo,13),4);     //列表选择显示分类
    $seo_title = '名厨学堂-名菜列表-';
    $topname = '名厨学堂-名菜列表';
    $moduleidtype = 4;
    $backurl = '/mobile/school/index.php';
    include template('school/food','mobile');
}
