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


$where = ' codeid = '.$horninfo['itemid'].' ';
list($list,$totalpage) = $oSpecial->codeListApplyList($where,'10');
if($action=='ajax') {
    $str = '';
    if ($list) {
        foreach ($list as $k => $v) {
            $s = '';
            if ($v['status']==0) {
                $s = '<span style="color:#ea554f;">结算中</span>';
            } else if ($v['status']==1) {
                $s = '<span style="color:#f89406;">拒绝</span>';
            } else {
                $s = '<span style="color:#03b887;">完成</span>';
            }
            $str .= '<tr class="bottomtr"><td>'.date('Y-m-d H:i:s',$v['addtime']).'</td><td>'.doubleval($v['money']).'元</td><td></td><td>'.$s.'</td></tr>';
        }
    }
    exit(json_encode(array('status' => 'y', 'info' => $str, 'totalpage' => $totalpage)));
}

list($codenumed,$codenewno,$applynum) = horncommon($horninfo['itemid']);
$topname = '结算记录';
$seo_title = '结算记录-会员中心-';
include template('hornbalance', 'mobile/'.$module);

?>