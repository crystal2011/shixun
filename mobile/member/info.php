<?php
/**
 * 会员中心
 */
require '../../member/config.inc.php';
require '../../common.inc.php';
if(!$_userid || $action == 'logout') dheader('/mobile/member/login.php');
require DT_ROOT.'/include/post.func.php';

require DT_ROOT.'/module/member/member.class.php';
$do = new member;
$do->userid = $_userid;
$member_info = $do->get_one();

$action = isset($action)?$action:'';
$backurl = '/mobile/member/info.php';
switch($action){
    case 'username_edit':
        $seo_title = '修改用户名-';
        $topname = '修改用户名';
        include template('username_edit', 'mobile/member');
        break;
    case 'truename_edit':
        $seo_title = '修改真实姓名-';
        $topname = '修改真实姓名';
        include template('truename_edit', 'mobile/member');
        break;
    case 'gender_edit':
        $seo_title = '修改性别-';
        $topname = '修改性别';
        include template('gender_edit', 'mobile/member');
        break;
    case 'area_edit':
        $seo_title = '修改地址-';
        $topname = '修改地址';
        include template('area_edit', 'mobile/member');
        break;
    case 'info_edit':
        $seo_title = '修改个人简介-';
        $topname = '修改个人简介';
        include template('info_edit', 'mobile/member');
        break;
    case 'change_mobile':
        $seo_title = '更换手机-';
        $topname = '更换手机';
        include template('change_mobile', 'mobile/member');
        break;
    default:
        $seo_title = '个人信息-';
        $topname = '个人信息';
        $backurl = '/mobile/member/index.php';
        include template('info', 'mobile/member');
        break;
}


?>