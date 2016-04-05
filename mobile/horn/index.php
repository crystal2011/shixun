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
list($list,$totalpage) = $oSpecial->hornList('*','islong=0 and status=3','addtime desc',3);
if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<div class="content">
                        <div class="clear info">
                            <a class="thumb" href="/mobile/horn/hornshow.php?id='.$v['itemid'].'"><img src="'.$v['thumb'].'" /></a>
                            <div class="text">
                                <a class="title" href="/mobile/horn/hornshow.php?id='.$v['itemid'].'">'.$v['title'].'</a>
                            </div>
                        </div>
                        <div class="handle clear hasbutton">
                            <span class="handle1 de0000">内容审核号'.$v['code'].'</span>
                            <span><img src="'.DT_SKIN.'image/mobile/hit.png" />'.$v['hits'].'</span>
                            <a class="fr showpng" href="/mobile/horn/hornshow.php?id='.$v['itemid'].'"><img src="'.DT_SKIN.'image/mobile/show.png" /></a>
                        </div>
                    </div>';
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

//常任委员会
    $mygetcount = true;
    $pagesize = 50;
    $aIsLongUser = $oSpecial->get_list('islong=1 and status=3','addtime desc',3);



    $seo_title = '羊角会-';
    $topname = '羊角会';

    include template('horn/index','mobile');
}
