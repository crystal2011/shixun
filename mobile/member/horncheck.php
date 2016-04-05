<?php
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require_once DT_ROOT.'/module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';

$at = isset($at)?$at:'';
if($at=='check'){
    if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
    if(!$horninfo = $oSpecial->checkHasUser()) exit(json_encode(array('status'=>'n','info'=>$oSpecial->errmsg)));
    $checktype = isset($checktype)?$checktype:0;
    $itemid = isset($itemid)?intval($itemid):0;
    $status = isset($status)?intval($status):0;
    $errormsg = isset($errormsg)?$errormsg:'';
    if(!$oSpecial->canCheck($checktype,$horninfo['itemid'],$itemid,$status,$errormsg)) exit(json_encode(array('status'=>'n','info'=>$oSpecial->errmsg)));
    exit(json_encode(array('status'=>'y','info'=>'操作成功')));
}else if($at=='ajax'){
    if(!$_userid) dalert('请先登录','/mobile/member/login.php');
    if(!$horninfo = $oSpecial->checkHasUser()) dalert($oSpecial->errmsg,'/mobile/member/index.php');
    $action = isset($action)?$action:'';
    $checktype = isset($checktype)?$checktype:0;
    list($list,$totalpage) = $oSpecial->codeListTable($checktype,$horninfo['itemid'],'wap');
    require_once DT_ROOT.'/module/member/member.class.php';
    $member_do = new member;
    $getListUser = $member_do->getListUser($list,'username');

    $str = '';
    if($list){
        foreach($list as $k=>$v){
            $str .= '<tr><td colspan="2"><a href="'.$v['url'].'" target="_blank" title="'.$v['t_title'].'">'.$v['t_title'].'</a></td></tr>
                    <tr><td>总金额：'.doubleval($v['allmoney']).'元</td><td>实支金额：'.doubleval($v['money']).'元</td></tr>
                    <tr><td>折扣：'.doubleval($v['discount']).'折</td><td>折扣金额：'.doubleval($v['discountfee']).'元</td></tr>
                    <tr><td>备注：'.$v['note'].'-'.$v['typename'].'</td><td>发布时间：'.date('Y-m-d',$v['addtime']).'</td></tr>
                    <tr class="bottomtr">
                        <td colspan="2">
                            <input type="button" value="通过" onclick="pass(1,'.$v['t_itemid'].',this)" style="padding:1px 3px;border:0px;background-color:#03b887;color:#fff;cursor:pointer;" />
                            <input type="button" value="不通过" df="pass(0,'.$v['t_itemid'].',this)" onclick="passinput(this)" style="padding:1px 3px;border:0px;background-color:#ea554f;color:#fff;cursor:pointer;" />
                            <span style="display:none;">原因：<input type="text" maxlength="50" style="width: 100px;height: 20px;line-height: 20px;border: 1px solid #e2e2e2;padding-left: 5px;" /></span>
                        </td>
                    </tr>';
        }
    }
    exit(json_encode(array('status'=>'y','info'=>$str,'totalpage'=>$totalpage)));

}else if($at=='checklist'){
    if(!$_userid) dalert('请先登录','/mobile/member/login.php');
    if(!$horninfo = $oSpecial->checkHasUser()) dalert($oSpecial->errmsg,'/mobile/member/index.php');
    $action = isset($action)?$action:'';
    $checktype = isset($checktype)?$checktype:0;
    list($list,$totalpage) = $oSpecial->codeListTable($checktype,$horninfo['itemid'],'wap');
    require_once DT_ROOT.'/module/member/member.class.php';
    $member_do = new member;
    $getListUser = $member_do->getListUser($list,'username');

    $topnamea = array('餐饮供应','商家优惠','招聘信息','店铺转让','名厨学堂','美食分享','文章发布');
    $seo_title = '审核'.$topnamea[$checktype].'-会员中心-';
    $topname = '审核'.$topnamea[$checktype];
    include template('checklist', 'mobile/'.$module);
}else{
    if(!$_userid) dalert('请先登录','/mobile/member/login.php');
    if(!$horninfo = $oSpecial->checkHasUser()) dalert($oSpecial->errmsg,'/mobile/member/index.php');
    $seo_title = '待审核-会员中心-';
    $topname = '待审核';
    $oSpecial->codenewno($horninfo['itemid']);
    include template('horncheck', 'mobile/'.$module);
}
?>