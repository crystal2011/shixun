<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require MD_ROOT.'/member.class.php';
require DT_ROOT.'/include/post.func.php';
$do = new member;
if($submit){
    $oldpassword = isset($oldpassword)?$oldpassword:'';
    $password = isset($password)?$password:'';
    $repassword = isset($repassword)?$repassword:'';
    if(!$do->is_password($oldpassword,$oldpassword) || !$do->is_password($password,$repassword)) dalert($do->errmsg,'/member/safe.php');
    if($oldpassword==$password) dalert('新旧密码不能相同','/member/safe.php');
    $userinfo = $do->get_one('userid='.$_userid,'password');
    if($userinfo['password']!=md5(md5($oldpassword)))  dalert('旧密码错误','/member/safe.php');
    $db->query("update {$db->pre}member set password = '".md5(md5($password))."' where userid = ".$_userid);
    $do->logout();
    dalert('修改成功','/member/login.php');
}else{
    if(!$_userid) dheader($CFG['url']);
    $seo_title = '安全中心-会员中心-';
    $active = 'safe';
    $nav_selected='member';
    include template('safe', $module);
}
?>