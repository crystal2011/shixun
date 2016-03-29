<?php
defined('DT_ADMIN') or exit('Access Denied');
require MD_ROOT.'/comment.class.php';
$do = new comment();
$menus = array (
    array('评论列表', '?moduleid='.$moduleid.'&file='.$file),
    array('评论审核', '?moduleid='.$moduleid.'&file='.$file.'&action=check'),
);
$cat_type = array('全部','餐饮供应','商家优惠','招聘信息','店铺转让','名厨学堂-菜系','分享','羊角会','羊角会成员');

$this_forward = '?moduleid='.$moduleid.'&file='.$file;
if(in_array($action, array('', 'check'))) {
	$sfields = array('内容', '原文标题', '会员名', '昵称', 'IP', '原文ID', '评论ID');
	$dfields = array('content', 'item_title', 'username', 'passport', 'ip', 'item_id', 'itemid');
	$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序');
	$dorder  = array('itemid desc', 'addtime DESC', 'addtime ASC');
	$sstar = $L['star_type'];

	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($order) && isset($dorder[$order]) or $order = 0;
	isset($star) && isset($sstar[$star]) or $star = 0;
	isset($ip) or $ip = '';

	$fields_select = dselect($sfields, 'fields', '', $fields);
	$module_select = module_select('mid', '模块', $mid);
	$order_select  = dselect($sorder, 'order', '', $order);
	$star_select  = dselect($sstar, 'star', '', $star);

    $type = isset($type)?intval($type):0;
    $id = isset($id)?intval($id):0;
    $userid = isset($userid)?intval($userid):0;

	$condition = '';
	if($keyword) $condition .= in_array($dfields[$fields], array('item_id', 'itemid', 'ip')) ? " AND $dfields[$fields]='$kw'" : " AND $dfields[$fields] LIKE '%$keyword%'";
	if($mid) $condition .= " AND item_mid='$mid'";
	if($ip) $condition .= " AND ip='$ip'";
	if($star) $condition .= " AND star='$star'";
    if($userid) $condition .= " AND userid=".$userid;
    if($id) $condition .= " AND id=".$id;
    if($type) $condition .= " AND type=".$type;
}
switch($action) {
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($do->pass($post)) {
				$do->edit($post);
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($do->get_one());
			$addtime = timetodate($addtime);
			$replytime = $replytime ? timetodate($replytime, 6) : '';
			include tpl('comment_edit', $module);
		}
	break;
	case 'ban':
		if($submit) {
			$do->ban_update($post);
			dmsg('更新成功', '?moduleid='.$moduleid.'&file='.$file.'&action=ban&page='.$page);
		} else {
			$condition = 1;
			if($mid) $condition = "moduleid=$mid";
			$lists = $do->get_ban_list($condition);
			include tpl('comment_ban', $module);
		}
	break;
	case 'delete':
		$itemid or msg('请选择评论');
		$do->delete($itemid);
		dmsg('删除成功', $this_forward);
	break;
	case 'check':
		if($itemid) {
			$status = $status == 3 ? 3 : 2;
			$do->check($itemid, $status);
			dmsg($status == 3 ? '审核成功' : '取消成功', $forward);
		} else {
			$lists = $do->get_list('status=2'.$condition, $dorder[$order]);
			$menuid = 1;
			include tpl('comment', $module);
		}
	break;
	default:
		$lists = $do->get_list('status=3'.$condition, $dorder[$order]);
		$menuid = 0;
		include tpl('comment', $module);
	break;
}
?>