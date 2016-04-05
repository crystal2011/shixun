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
list($list,$totalpage) = $oCai->schoolList('*',$where.' and status=3 ','addtime desc','6');

require_once '../../module/member/member.class.php';
$oMember = new member();
$aKnow = $oMember->getListUser($list);

if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<div class="content">
                        <div class="clear info">
                            <a class="thumb" href="/mobile/school/foodshow.php?id='.$v['itemid'].'"><img src="'.$v['thumb'].'" /></a>
                            <div class="text">
                                <a class="title" href="/mobile/school/foodshow.php?id='.$v['itemid'].'">'.$v['title'].'</a>
                                <p class="introduce">'.$v['introduce'].'</p>
                                <p class="price">
                                <span class="price1">名厨：'.$aKnow[$v['userid']]['truename'].'</span>
                            </p>
                            </div>

                        </div>
                        <div class="handle clear hasbutton">
                            <span class="handle1"><img src="'.DT_SKIN.'image/mobile/love.png" />'.$v['likes'].'</span>
                            <span><img src="'.DT_SKIN.'image/mobile/hit.png" />'.$v['hits'].'</span>
                            <a class="fr showpng" href="/mobile/school/foodshow.php?id='.$v['itemid'].'"><img src="'.DT_SKIN.'image/mobile/show.png" /></a>
                        </div>
                    </div>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else{
    $aCatList = array_chunk(showCat($catinfo,13),4);     //列表选择显示分类
    $seo_title = '菜系-';
    $topname = '菜系';
    $moduleidtype = 4;
    include template('school/food','mobile');
}
