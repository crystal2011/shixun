<?php
defined('DT_ADMIN') or exit('Access Denied');
$TYPE = get_type('vote', 1);
require MD_ROOT.'/vote.class.php';
$do = new vote();
$menus = array (
    array('投票列表', '?moduleid='.$moduleid.'&file='.$file),
);
$type = isset($type)?intval($type):0;
$id = isset($id)?intval($id):0;
$userid = isset($userid)?intval($userid):0;
$sfields = array('按条件', '标题', '外链', '内容');
$dfields = array('title','title','linkto','content');
$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '投票次数降序', '投票次数升序', '浏览次数降序', '浏览次数升序', '开始时间降序', '开始时间升序', '到期时间降序', '到期时间升序');
$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'votes DESC', 'votes ASC', 'hits DESC', 'hits ASC', 'fromtime DESC', 'fromtime ASC', 'totime DESC', 'totime ASC');
isset($fields) && isset($dfields[$fields]) or $fields = 0;
isset($order) && isset($dorder[$order]) or $order = 0;
isset($typeid) or $typeid = 0;
$level = isset($level) ? intval($level) : 0;
$type_select = type_select('vote', 1, 'typeid', '请选择分类', $typeid);
$fields_select = dselect($sfields, 'fields', '', $fields);
$order_select  = dselect($sorder, 'order', '', $order);
$level_select = level_select('level', '级别', $level);
$condition = '1';
if($_areaids) $condition .= " AND areaid IN (".$_areaids.")";//CITY
if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
if($typeid) $condition .= " AND typeid IN (".type_child($typeid, $TYPE).")";
if($level) $condition .= " AND level=$level";
if($areaid) $condition .= ($ARE['child']) ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
if($type) $condition .= " and type = ".$type;
if($id) $condition .= " and id = ".$id;
if($userid) $condition .= " and userid = ".$userid;
$lists = $do->get_list($condition, $dorder[$order]);
include tpl('vote', $module);
?>