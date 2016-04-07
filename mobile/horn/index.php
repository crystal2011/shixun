<?php
/**
 * 手机端-羊角会-首页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/info/info.class.php';
require_once '../../module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';
$action = isset($action)?$action:'';
list($list,$totalpage) = $oSpecial->hornList('*','status=3','addtime desc',12);
if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<li class="clear">
                <div class="lidiv">
                    <a class="schoola" href="hornshow.php?id='.$v['itemid'].'"><img src="'.$v['thumb'].'" /></a>
                    <a class="title" href="hornshow.php?id='.$v['itemid'].'">荣誉创会成员：'.$v['title'].'</a>
                    <a class="title">内容审核号：<span class="lnfh">'.$v['code'].'</span></a>
                    <a class="introudce" href="hornshow.php">'.$v['introduce'].'</a>
                    <p class="clear fndklf"><span class="fl"><img src="{DT_SKIN}image/mobile/hit.png" style="width:16px;" />'.$v['hits'].'</span><span class="fr">人气榜：'.$v['userds'].'</span></p>
                </div>
            </li>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else{


    $table = $db->pre.'info_24';
    $table_data = $db->pre.'info_data_24';
    $oInfo = new info(24);
    $aInfo = $oInfo->infoList('title,itemid,addtime','status=3','addtime desc','4');

//广告Flash
    require_once '../../module/extend/ad.class.php';
    $oAd = new ad;
    $sAdPlaceHot = $oAd->getAdAllFlash(85);


    $seo_title = '羊角会-';
    $topname = '羊角会';

    include template('horn/index','mobile');
}
