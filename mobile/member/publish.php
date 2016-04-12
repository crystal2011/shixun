<?php
/**
 * 会员中心-输入发布审核号
 */
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if(!$_userid) dheader('/mobile/member/login.php');
require DT_ROOT.'/include/post.func.php';
$seo_title = '输入发布审核号-会员中心-';
$notop = true; //不显示头部
$moduleidtype = isset($moduleidtype)?intval($moduleidtype):-1;
if(!in_array($moduleidtype,array(0,1,2,3,4,5,6))){
    dheader('/mobile/member/index.php');
}
include template('publish', 'mobile/member');
?>