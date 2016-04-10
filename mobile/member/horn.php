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
list($list,$totalpage) = $oSpecial->codeList($where,'10');
if($list){
    foreach($list as $k=>$v){
        $table = $oSpecial->codetable($v['type']);
        $list[$k]['iteminfo'] = $db->get_one("select title from {$db->pre}{$table} where itemid = ".$v['id']);
        $list[$k]['urlarr'] = $oSpecial->urlarr($v['type'],$v['id'],'wap');
    }
}

require_once DT_ROOT.'/module/member/member.class.php';
$member_do = new member;
$getListUser = $member_do->getListUser($list,'username');
if($action=='ajax'){
    $str = '';
    if($list){
        foreach($list as $k=>$v){
            $strs = $v['cc_status']==0?'<span style="color:#ea554f">不通过</span>':'<span style="color:#03b887">通过</span>';
            $bastrs = $v['cc_status']==0?'<p class="hornmintroduce"><span style="color:#ea554f">原因：'.$v['cc_note'].'</span></td></tr>':'';

            $title = isset($v['iteminfo']['title'])?'<a href="'.$v['urlarr'].'" >'.$v['iteminfo']['title'].'</a>':'失效';
            $str .= '<li>
                    <div class="df">
                        <div>
                            <p class="hornmtitle">'.$title.'</p>
                            <p class="hornmintroduce">分类：'.$v['note'].'-'.$v['typename'].'&nbsp;&nbsp;&nbsp;时间：'.date('Y-m-d',$v['cc_addtime']).'</p>
                            <p class="hornmintroduce">状态：'.$strs.'&nbsp;&nbsp;&nbsp;金额：<span style="color:#ea554f">'.doubleval($v['discountfee']).'</span>元</p>'.$bastrs.'</div>
                    </div>
                    <div class="line"></div>
                </li>
                    ';
        }
    }
    exit(json_encode(array('status'=>'y','info'=>$str,'totalpage'=>$totalpage)));
}


$ishorn = true;
$topname = '审核记录';
$seo_title = '审核记录-会员中心-';
include template('horn', 'mobile/'.$module);

?>