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


$where = ' status = 6 and  codeid = '.$horninfo['itemid'];
$fromyear = isset($fromyear)?$fromyear:'';
$frommonth = isset($frommonth)?$frommonth:'';
$toyear = isset($toyear)?$toyear:'';
$tomonth = isset($tomonth)?$tomonth:'';
$list = array();
if($fromyear && $frommonth && $toyear && $tomonth){
    $fromtime = strtotime($fromyear.'-'.$frommonth);
    $where .= ' and checktime >= '.$fromtime;
    $totime = strtotime('+1 months'.$toyear.'-'.$tomonth);
    $where .= ' and checktime < '.$totime;
    //获取每个月的
    list($list,$totalpage) = $oSpecial->codeListGroup($where);
}
if($action=='ajax'){
    $str = '';
    if($list){
        foreach($list as $k=>$v){
            $str .= '<tr class="bottomtr"><td>'.$v['ym'].'</td><td>'.doubleval($v['allmoney']).'元</td><td>'.doubleval($v['money']).'元</td><td>'.doubleval($v['discountfee']).'折</td></tr>';
        }
    }
    exit(json_encode(array('status'=>'y','info'=>$str,'totalpage'=>$totalpage)));
}

$codeAll = $oSpecial->codeAll($where);
$topname = '月度汇总';
$seo_title = '月度汇总-会员中心-';
include template('horntotal', 'mobile/'.$module);

?>