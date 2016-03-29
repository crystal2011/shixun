<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once DT_ROOT.'/module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';

if(!$_userid) dheader('/member/login.php');
if(!$horninfo = $oSpecial->checkHasUser()) dalert($oSpecial->errmsg,'/member/index.php');


$where = ' status = 6 and  codeid = '.$horninfo['itemid'];
$fromyear = isset($fromyear)?$fromyear:'';
$frommonth = isset($frommonth)?$frommonth:'';
$toyear = isset($toyear)?$toyear:'';
$tomonth = isset($tomonth)?$tomonth:'';

if($fromyear && $frommonth && $toyear && $tomonth){
    $fromtime = strtotime($fromyear.'-'.$frommonth);
    $where .= ' and checktime >= '.$fromtime;

    $totime = strtotime('+1 months'.$toyear.'-'.$tomonth);
    $where .= ' and checktime < '.$totime;
    //获取每个月的
    list($aCodel,$totalpage) = $oSpecial->codeListGroup($where);
}

//羊角会公共部分
list($codenumed,$codenewno,$applynum) = horncommon($horninfo['itemid']);

$snd = 3;
$codeAll = $oSpecial->codeAll($where);
$nav_selected='member';
$active = 'horn';
$seo_title = '月度汇总-会员中心-';
include template('horntotal', $module);

?>