<?php
defined('DT_ADMIN') or exit('Access Denied');
$menu = array(
	array("添加".$name, "?moduleid=$moduleid&action=add"),
	array($name."列表", "?moduleid=$moduleid"),
	array("更新数据", "?moduleid=$moduleid&file=html"),
	array("模块设置", "?moduleid=$moduleid&file=setting"),
    array('优惠码使用记录', '?moduleid='.$moduleid.'&action=userds'),
    array('结算记录', '?moduleid='.$moduleid.'&action=apply'),
);
?>