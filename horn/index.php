<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/info/info.class.php';
require_once '../module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';
$action = isset($action)?$action:'';
if($action=='ajaxlist'){
    list($aIsNotLongUser,$totalpage) = $oSpecial->hornList('*','islong=0 and status=3','sortnum asc');
    $str = '';
    if($aIsNotLongUser){
        foreach($aIsNotLongUser as $k=>$v){
            $str .= '<li>'.
                        '<a href="/horn/hornshow.php?id='.$v['itemid'].'" target="_blank"  title="'.$v['title'].'">'.
                            '<img src="'.$v['thumb'].'" width="182" height="136" alt=""/>'.
                            '<h3>'.dsubstr($v['title'],26).'</h3>'.
                            '<p>内容审核号：'.$v['code'].'</p>'.
                            '<p>'.dsubstr($v['introduce'],40).'</p>'.
                        '</a>'.
                    '</li>';
        }
    }
    exit(json_encode(array('info'=>$str,'totalpage'=>$totalpage)));

}else{


    $table = $db->pre.'info_24';
    $table_data = $db->pre.'info_data_24';
    $oInfo = new info(24);
    $aInfo = $oInfo->infoList('introduce,title,itemid','status=3','addtime desc','4');

//广告Flash
    require_once '../module/extend/ad.class.php';
    $oAd = new ad;
    $sAdPlaceHot = $oAd->getAdAllFlash(37);

//常任委员会
    $mygetcount = true;
    $pagesize = 100;
    $aIsLongUser = $oSpecial->get_list('islong=1 and status=3','sortnum asc');

    list($aIsNotLongUser,$totalpage) = $oSpecial->hornList('*','islong=0 and status=3','sortnum asc',100);

    $nav_selected = 'horn';
    $seo_title = '羊角会-';
    include template('index', 'horn');
}

?>