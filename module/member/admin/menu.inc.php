<?php
defined('DT_ADMIN') or exit('Access Denied');
$menu = array(
	array('添加会员', '?moduleid=2&action=add'),
	array('会员列表', '?moduleid=2'),
	array('在线会员', '?moduleid=2&file=online'),
	array('一键登录', '?moduleid=2&file=oauth'),
	array('模块设置', '?moduleid=2&file=setting'),
);
$menu_finance = array(
	array($DT['money_name'].'管理', '?moduleid=2&file=record'),
	array('充值记录', '?moduleid=2&file=charge'),
	array('提现记录', '?moduleid=2&file=cash'),
	array('信息支付', '?moduleid=2&file=pay'),
	array('优惠码管理', '?moduleid=2&file=promo'),
);
$menu_relate = array(
	array('站内信件', '?moduleid=2&file=message'),
	array('客服中心', '?moduleid=2&file=ask'),
	array('登录日志', '?moduleid=2&file=loginlog'),
);
if(!$_founder) unset($menu_relate[7]);
?>