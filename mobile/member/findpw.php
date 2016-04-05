<?php
/**
 * 找回密码
 */
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require MD_ROOT.'/member.class.php';
require DT_ROOT.'/include/post.func.php';
$do = new member;
$forward = wapforward();

if($submit) {
    if($_userid) exit(json_encode(array('status'=>'n','info'=>'您已登录')));
    if(!check_token()) exit(json_encode(array('status'=>'n','info'=>'登录失效，请重试')));

    $mobile = isset($username)?$username:'';
    $code = isset($code)?$code:'';
    //手机验证码检测
    require_once DT_ROOT.'/include/send.class.php';
    $send = new send;
    if(!$send->check_mobile_code($code,$DT_IP,$mobile)){
        exit(json_encode(array('status' => 'n', 'info' => $send->errmsg)));
    }

    //会员信息数组
    $post = array(
        'mobile'=>$mobile,
        'password'=>isset($password)?$password:'',
        'cpassword'=>isset($cpassword)?$cpassword:'',
    );

    if($do->findpw($post)) {
        exit(json_encode(array('status'=>'y', 'info' =>"重置成功,请重新登录")));
    } else {
        exit(json_encode(array('status' => 'n', 'info' => $do->errmsg)));
    }

} else {
    if($_userid) dheader($CFG['url'].'mobile/member/index.php');
    $seo_title = '忘记密码-';
    $topname = '忘记密码';
    $nav_selected = 'findpw';
    include template('findpw', 'mobile/'.$module);
}
?>