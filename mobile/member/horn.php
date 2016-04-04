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

$where = ' c.codeid = '.$horninfo['itemid'];
list($list,$totalpage) = $oSpecial->codeList($where,'2');
require_once DT_ROOT.'/module/member/member.class.php';
$member_do = new member;
$getListUser = $member_do->getListUser($list,'username');
if($action=='ajax'){
    $str = '';
    if($list){
        foreach($list as $k=>$v){
            $strs = $v['cc_status']==0?'<span style="color:#ea554f">不通过</span>':'<span style="color:#03b887">通过</span>';
            $bastrs = $v['cc_status']==0?'<tr class="bottomtr"><td colspan="2"><span style="color:#ea554f">原因：'.$v['cc_note'].'</span></td></tr>':'';
            $cstr = $v['cc_status']==0?'':'class="bottomtr"';
            $str .= '<tr><td>'.$getListUser[$v['userid']]['username'].' </td><td>备注：'.$v['note'].'-'.$v['typename'].'</td></tr>
                    <tr><td>总金额：'.doubleval($v['allmoney']).'元</td><td>实支金额：'.doubleval($v['money']).'元</td></tr>
                    <tr><td>折扣：'.doubleval($v['discount']).'折</td><td>折扣金额：'.doubleval($v['discountfee']).'元</td></tr>
                    <tr '.$cstr.'><td>审核时间：'.date('Y-m-d',$v['cc_addtime']).'</td><td>状态：'.$strs.'</td>'.$bastrs;
        }
    }
    exit(json_encode(array('status'=>'y','info'=>$str,'totalpage'=>$totalpage)));
}



$topname = '审核记录';
$seo_title = '审核记录-会员中心-';
include template('horn', 'mobile/'.$module);

?>