<?php
defined('DT_ADMIN') or exit('Access Denied');
$menu = array(
	array("羊角会成员管理", "?moduleid=$moduleid&file=group"),

	array("分类管理", "?file=category&mid=$moduleid"),
	array("更新数据", "?moduleid=$moduleid&file=html"),
	array("模块设置", "?moduleid=$moduleid&file=setting"),
);
?>