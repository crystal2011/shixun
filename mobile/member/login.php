<?php
/**
 * 登录
 */
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require MD_ROOT.'/member.class.php';
require DT_ROOT.'/include/post.func.php';
$do = new member;

$forward = $forward ? linkurl($forward) : $CFG['url'].'mobile/index.php';
$forward = wapforward();
if($submit) {
    if($_userid) exit(json_encode(array('status'=>'n','info'=>'您已登录')));
    if(!check_token()) exit(json_encode(array('status'=>'n','info'=>'登录失效，请重试')));
    $username = isset($username)?trim($username):'';
    $password = isset($password)?trim($password):'';
    $isautologin = isset($isautologin)?$isautologin:'';

    $user = $do->login($username, $password, $isautologin);
    if($user) {
        if($DT['login_log'] == 2) $do->login_log($username, $password,'', 0);
        exit(json_encode(array('status'=>'y','forward'=>$forward)));
    } else {
        if($DT['login_log'] == 2) $do->login_log($username, $password,'', 0, $do->errmsg);
        exit(json_encode(array('status'=>'n','info'=>$do->errmsg)));
    }
} else {
    if($_userid) dheader($CFG['url'].'mobile/member/index.php');
    set_cookie('forward_url', $forward);
    $seo_title = '登录-';
    $topname = '登录';
    include template('login', 'mobile/'.$module);
}
?>