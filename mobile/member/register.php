<?php
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require MD_ROOT.'/member.class.php';
$do = new member;
$session = new dsession();
$forward = wapforward();
if($submit) {
    if($_userid) exit(json_encode(array('status'=>'n','info'=>'您已登录')));
    if(!check_token()) exit(json_encode(array('status'=>'n','info'=>'注册失效，请重试')));

    $mobile = isset($mobile)?$mobile:'';
    $code = isset($code)?$code:'';
    //手机验证码检测
    require_once DT_ROOT.'/include/send.class.php';
    $send = new send;
    if(!$send->check_mobile_code($code,$DT_IP,$mobile)){
        exit(json_encode(array('status' => 'n', 'info' => $send->errmsg)));
    }

    //会员信息数组
    $post = array(
        'username'=>isset($username)?$username:'',
        'mobile'=>$mobile,
        'password'=>isset($password)?$password:'',
        'cpassword'=>isset($cpassword)?$cpassword:'',
    );

    if($do->add($post)) {
        exit(json_encode(array('status'=>'y', 'info' =>"注册成功")));
    } else {
        exit(json_encode(array('status' => 'n', 'info' => $do->errmsg)));
    }
} else {
    if($_userid) dheader($CFG['url'].'mobile/member/index.php');
    $seo_title = '注册-';
    $topname = '注册';
    $nav_selected = 'register';
    include template('register', 'mobile/'.$module);
}