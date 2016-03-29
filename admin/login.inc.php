<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$DT_LICENSE = md5(file_get(DT_ROOT.'/license.txt'));
if($DT_LICENSE != '5bdef29340ffdbe22c937e38b42292aa' && $DT_LICENSE != '8afcc73ad7d0cd6019899812cc2998a3') msg('网站根目录license.txt不允许修改或删除，请检查');
if(!$forward) $forward = '?';

if($_destoon_admin && $_adminuserid && $_destoon_admin == $_adminuserid) dheader($forward);

if($DT['admin_area']) {
	$AA = explode("|", trim($DT['admin_area']));
	$A = ip2area($DT_IP);
	$pass = false;
	foreach($AA as $v) {
		if(strpos($A, $v) !== false) { $pass = true; break; }
	}
	if(!$pass) dalert('未被允许的地区', $MODULE[2]['linkurl'].'logout.php?forward='.urlencode(DT_PATH));
}
if($DT['admin_ip']) {
	$IP = explode("|", trim($DT['admin_ip']));
	$pass = false;
	foreach($IP as $v) {
		if($v == $DT_IP) { $pass = true; break; }
		if(preg_match("/^".str_replace('*', '[0-9]{1,3}', $v)."$/", $DT_IP)) { $pass = true; break; }
	}
	if(!$pass) dalert('未被允许的IP段', $MODULE[2]['linkurl'].'logout.php?forward='.urlencode(DT_PATH));
}
if($DT['close']) $DT['captcha_admin'] = 0;
if($submit) {
	captcha($captcha, $DT['captcha_admin']);
	if(!$username) msg('请输入用户名');
	if(!$password) msg('请输入密码');
	include load('member.lang');
	$MOD = cache_read('module-2.php');
	require DT_ROOT.'/include/module.func.php';
	require DT_ROOT.'/admin/admin.class.php';
	$do = new admin;
	$user = $do->login($username, $password);
	if($user) {
		if($user['adminuid'] != $CFG['founderid']) {
			if(($DT['admin_week'] && !check_period(','.$DT['admin_week'])) || ($DT['admin_hour'] && !check_period($DT['admin_hour']))) {
				set_cookie('auth', '');
				dalert('未被允许的管理时间', $MODULE[2]['linkurl'].'logout.php?forward='.urlencode(DT_PATH));
			}
		}
		if($CFG['authadmin'] == 'cookie') {
			set_cookie($secretkey, $user['adminuid']);
		} else {
			$_SESSION[$secretkey] = $user['adminuid'];
		}
		$do->cache_right($user['adminuid']);
        $do->cache_menu($user['adminuid']);
		if($DT['login_log']) $do->login_log($username, $password, $user['passsalt'], 1);
		dheader($forward);
	} else {
		if($DT['login_log']) $do->login_log($username, $password, $user['passsalt'], 1, $do->errmsg);
		msg($do->errmsg);
	}
} else {
	if(strpos($DT_URL, DT_PATH) === false) dheader(DT_PATH.basename(get_env('self')));
	$username = isset($username) ? $username : $_adminusername;
	include tpl('login');
}
?>