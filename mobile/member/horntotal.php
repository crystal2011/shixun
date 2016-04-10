<?php
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require_once DT_ROOT.'/module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';

$action = isset($action)?$action:'';
if(!$_userid) $action=='ajax'?exit(json_encode(array('status'=>'n','info'=>'请先登录'))):dheader('/mobile/member/login.php');
if(!$horninfo = $oSpecial->checkHasUser()) $action=='ajax'?exit(json_encode(array('status'=>'n','info'=>$oSpecial->errmsg))):dalert($oSpecial->errmsg,'/mobile/member/index.php');
$applynum = applynum($horninfo['itemid']);

$where = ' status = 6 and  codeid = '.$horninfo['itemid'];
list($list,$totalpage) = $oSpecial->codeListGroup($where,100);

if($action=='ajax'){
    $str = '';
    if($list){
        foreach($list as $k=>$v){
            $str .= '<tr class="bottomtr"><td>'.$v['ym'].'</td><td>'.doubleval($v['allmoney']).'元</td><td>'.doubleval($v['money']).'元</td><td>'.doubleval($v['discountfee']).'折</td></tr>';
        }
    }
    exit(json_encode(array('status'=>'y','info'=>$str,'totalpage'=>$totalpage)));
}
if($list){
    $codeAll = $oSpecial->codeAll($where);
}


$where = ' codeid = '.$horninfo['itemid'].' ';
list($applylog,$totalpagess) = $oSpecial->codeListApplyList($where,'100');

$topname = '月度汇总';
$seo_title = '月度汇总-会员中心-';
$ishorn = true;
include template('horntotal', 'mobile/'.$module);

?>