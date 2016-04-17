<?php
/**
 * 手机端-羊角会-首页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/info/info.class.php';

$table = $db->pre.'info_24';
$table_data = $db->pre.'info_data_24';
$oInfo = new info(24);
list($list,$totalpage) = $oInfo->foodList('title,itemid,addtime','status=3','addtime desc','20');

$action = isset($action)?$action:'';
if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<li class="clear"  style="border-bottom:1px solid #e6e6e6;padding-top:5px;">
                    <span class="fr">'.date('m-d',$v['addtime']).'</span>
                    <article><a href="show.php?id='.$v['itemid'].'" >·'.$v['title'].'</a></article>
                </li>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else{
    $seo_title = '羊角会-';
    $topname = '羊角会';
    $backurl = '/mobile/horn/index.php';
    include template('horn/list','mobile');
}
