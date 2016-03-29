<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
if($CFG['authadmin'] == 'cookie') {
	set_cookie($secretkey, '');
} else {
	session_destroy();
}
set_cookie('adminauth', '');
set_cookie('adminuserid', '');
msg('已经安全退出网站后台管理', '?');
?>