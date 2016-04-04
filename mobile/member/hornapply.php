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


$action = isset($action)?$action:'';
if($action=='apply') {
    $applymoney = applynum($horninfo['itemid']);
    if (doubleval($applymoney) == 0) {
        dalert('结算金额为0元,不能结算', '/mobile/member/hornapply.php');
    }
    $oSpecial->applymoney($horninfo['itemid'], $_userid);
    dalert('申请已提交，请等待审核...', '/mobile/member/hornapply.php');
}else if($action=='ajax'){
    $where = ' codeid = '.$horninfo['itemid'].' and status = 3 ';
    list($list,$totalpage) = $oSpecial->codeListApply($where,'10');
    $str = '';
    if($list){
        foreach($list as $k=>$v){
            $scls = !doubleval($v['discountfee'])?'class="bottomtr"':'';
            $sstr = '';
            if(doubleval($v['discountfee'])){
                $s = '';
                if($v['is_apply']==0){
                    $s = '<span style="color:#ea554f;">未结算</span>';
                }else if($v['is_apply']==1){
                    $s = '<span style="color:#f89406;">申请中</span>';
                }else{
                    $s = '<span style="color:#03b887;">已结算</span>';
                }
                $sstr = '<tr class="bottomtr"><td colspan="2">'.$s.'</td></tr>';
            }
            $str .= '<tr><td>'.date('Y-m-d',$v['checktime']).'</td><td>备注：'.$v['note'].'-'.$v['typename'].'</td></tr>
                    <tr><td>总金额：'.doubleval($v['allmoney']).'元</td><td>实支金额：'.doubleval($v['money']).'元</td></tr>
                    <tr '.$scls.'><td>折扣：'.doubleval($v['discount']).'折</td><td>折扣金额：<span style="color:#ea554f;">'.doubleval($v['discountfee']).'</span>元</td></tr>'.$sstr;
        }
    }
    exit(json_encode(array('status'=>'y','info'=>$str,'totalpage'=>$totalpage)));
}else{
    $where = ' codeid = '.$horninfo['itemid'].' and status = 3 ';
    list($list,$totalpage) = $oSpecial->codeListApply($where,'10');
    list($codenumed,$codenewno,$applynum) = horncommon($horninfo['itemid']);
    $topname = '申请结算';
    $seo_title = '申请结算-会员中心-';
    include template('hornapply', 'mobile/'.$module);
}

?>