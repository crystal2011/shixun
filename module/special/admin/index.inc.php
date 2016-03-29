<?php
defined('DT_ADMIN') or exit('Access Denied');
require MD_ROOT.'/special.class.php';
$do = new special($moduleid);
$menus = array (
    array('添加'.$MOD['name'], '?moduleid='.$moduleid.'&action=add'),
    array($MOD['name'].'列表', '?moduleid='.$moduleid),
    array('审核'.$MOD['name'], '?moduleid='.$moduleid.'&action=check'),
    array('未通过'.$MOD['name'], '?moduleid='.$moduleid.'&action=reject'),
    array('回收站', '?moduleid='.$moduleid.'&action=recycle'),
    array('优惠码使用记录', '?moduleid='.$moduleid.'&action=userds'),
    array('结算记录', '?moduleid='.$moduleid.'&action=apply'),
);

if(in_array($action, array('', 'check', 'reject', 'recycle'))) {
	$sfields = array('模糊', '名字');
	$dfields = array('keyword', 'title');
	$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '更新时间降序', '更新时间升序', '浏览次数降序', '浏览次数升序', '信息ID降序', '信息ID升序','排序号降序','排序号升序');
	$dorder  = array($MOD['order'], 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC', 'hits DESC', 'hits ASC', 'itemid DESC', 'itemid ASC','sortnum DESC','sortnum asc');

	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($order) && isset($dorder[$order]) or $order = 0;
	$level = isset($level) ? intval($level) : 0;

	isset($datetype) && in_array($datetype, array('edittime', 'addtime', 'totime')) or $datetype = 'addtime';
	$fromdate = isset($fromdate) && is_date($fromdate) ? $fromdate : '';
	$fromtime = $fromdate ? strtotime($fromdate.' 0:0:0') : 0;
	$todate = isset($todate) && is_date($todate) ? $todate : '';
	$totime = $todate ? strtotime($todate.' 23:59:59') : 0;

	$thumb = isset($thumb) ? intval($thumb) : 0;
	$itemid or $itemid = '';
    isset($code) or $code = '';
    isset($userid) or $userid = '';

	$fields_select = dselect($sfields, 'fields', '', $fields);
	$level_select = level_select('level', '级别', $level, 'all');
	$order_select  = dselect($sorder, 'order', '', $order);

	$condition = '';
	if($_childs) $condition .= " AND catid IN (".$_childs.")";//CATE
	if($_areaids) $condition .= " AND areaid IN (".$_areaids.")";//CITY
	if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
	if($catid) $condition .= ($CAT['child']) ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
	if($areaid) $condition .= ($ARE['child']) ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
	if($level) $condition .= $level > 9 ? " AND level>0" : " AND level=$level";
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($thumb) $condition .= " AND thumb<>''";
	if($itemid) $condition .= " AND itemid=$itemid";
    if($code) $condition .= " AND code='$code'";
    if($userid) $condition .= " AND userid=".$userid;

	$timetype = strpos($dorder[$order], 'edit') === false ? 'add' : '';
}
switch($action) {
	case 'add':
		if($submit) {
			if($post = $do->pass($post)) {
				$do->add($post);

                dmsg('添加成功', '?moduleid='.$moduleid.'&action='.$action);
			} else {
				msg($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				isset($$v) or $$v = '';
			}
			$content = '';
			$status = 3;
			$cfg_photo = 6;
			$cfg_video = 0;
			$cfg_type = 10;
			$addtime = timetodate($DT_TIME);
			$item = array();
            $username= '';
			$menuid = 0;
            $islong = 1;
			isset($url) or $url = '';
			if($url) {
				$tmp = fetch_url($url);
				if($tmp) extract($tmp);
			}
            $code = '';
            $discount = '';
            $arrdiscount = explode('|', trim($MOD['discount']));
			include tpl('edit', $module);
		}
	break;
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($post = $do->pass($post)) {
				$do->edit($post);
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			$item = $do->get_one();
			extract($item);
			$addtime = timetodate($addtime);
			$menuon = array('4', '3', '2', '1');
			$menuid = $menuon[$status];
            require_once DT_ROOT.'/module/member/member.class.php';
            $member_do = new member;
            if(!$aUser = $member_do->isCompany($userid,'userid')){
                msg($member_do->errmsg);
            }
            $username = $aUser['username'];
            $arrdiscount = explode('|', trim($MOD['discount']));
			include tpl($action, $module);
		}

	break;
	case 'move':
		if($submit) {
			$fromids or msg('请填写来源ID');
			if($tocatid) {
				$db->query("UPDATE {$table} SET catid=$tocatid WHERE `{$fromtype}` IN ($fromids)");
				dmsg('移动成功', $forward);
			} else {
				msg('请选择目标分类');
			}
		} else {
			$itemid = $itemid ? implode(',', $itemid) : '';
			$menuid = 5;
			include tpl($action);
		}
	break;
	case 'update':
		is_array($itemid) or msg('请选择'.$MOD['name']);
		foreach($itemid as $v) {
			$do->update($v);
		}
		dmsg('更新成功', $forward);
	break;
	case 'tohtml':
		is_array($itemid) or msg('请选择'.$MOD['name']);
		$html_itemids = $itemid;
		foreach($html_itemids as $itemid) {
			tohtml('show', $module);
		}
		dmsg('生成成功', $forward);
	break;
	case 'delete':
		$itemid or msg('请选择'.$MOD['name']);
		isset($recycle) ? $do->recycle($itemid) : $do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'restore':
		$itemid or msg('请选择'.$MOD['name']);
		$do->restore($itemid);
		dmsg('还原成功', $forward);
	break;
	case 'clear':
		$do->clear();
		dmsg('清空成功', $forward);
	break;
	case 'level':
		$itemid or msg('请选择'.$MOD['name']);
		$level = intval($level);
		$do->level($itemid, $level);
		dmsg('级别设置成功', $forward);
	break;
	case 'recycle':
		$lists = $do->get_list('status=0'.$condition, $dorder[$order]);
		$menuid = 4;
        require_once DT_ROOT.'/module/member/member.class.php';
        $member_do = new member;
        $getListUser = $member_do->getListUser($lists,'username');
		include tpl('index', $module);
	break;
	case 'reject':
		if($itemid && !$psize) {
			$do->reject($itemid);
			dmsg('拒绝成功', $forward);
		} else {
			$lists = $do->get_list('status=1'.$condition, $dorder[$order]);
			$menuid = 3;
            require_once DT_ROOT.'/module/member/member.class.php';
            $member_do = new member;
            $getListUser = $member_do->getListUser($lists,'username');
			include tpl('index', $module);
		}
	break;
	case 'check':
		if($itemid && !$psize) {
			$do->check($itemid);
			dmsg('审核成功', $forward);
		} else {
			$lists = $do->get_list('status=2'.$condition, $dorder[$order]);
			$menuid = 2;
            require_once DT_ROOT.'/module/member/member.class.php';
            $member_do = new member;
            $getListUser = $member_do->getListUser($lists,'username');
			include tpl('index', $module);
		}
	break;
    case 'userds':
        //使用记录
        $code = isset($code)?$code:'';
        $userid = isset($userid)?$userid:'';
        $type = isset($type)?$type:-1;
        $where = ' 1=1 ';
        if($code){
            $where .= " and code='{$code}' ";
        }
        if($userid){
            $where .= " and userid='{$userid}' ";
        }
        if($type!=-1){
            $where .= " and type='{$type}' ";
        }
        $lists = $do->get_userlist($where,'addtime desc');
        $menuid = 5;
        require_once DT_ROOT.'/module/member/member.class.php';
        $member_do = new member;
        $getListUser = $member_do->getListUser($lists,'username');

        $arr = array('餐饮供应','优惠价格','招聘信息','店铺转让','名厨学堂','分享','文章');

        include tpl('userds', $module);

        break;
    case 'apply':
        $condition = '';
        isset($datetype) && in_array($datetype, array('edittime', 'addtime', 'totime')) or $datetype = 'addtime';
        $fromdate = isset($fromdate) && is_date($fromdate) ? $fromdate : '';
        $fromtime = $fromdate ? strtotime($fromdate.' 0:0:0') : 0;
        $todate = isset($todate) && is_date($todate) ? $todate : '';
        $totime = $todate ? strtotime($todate.' 23:59:59') : 0;
        $itemid or $itemid = '';
        $code = isset($code)?$code:'';
        $userid = isset($userid)?intval($userid):'';
        if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
        if($totime) $condition .= " AND `$datetype`<=$totime";
        if($itemid) $condition .= " AND itemid=$itemid";
        if($code) $condition .= " AND codeid='$code'";
        if($userid) $condition .= " AND userid=".$userid;
        $lists = $do->get_applylist('1=1'.$condition,'addtime desc');
        $menuid = 6;
        require_once DT_ROOT.'/module/member/member.class.php';
        $member_do = new member;
        $getListUser = $member_do->getListUser($lists,'username');
        include tpl('apply', $module);
        break;
	default:
		$lists = $do->get_list('status=3'.$condition, $dorder[$order]);
		$menuid = 1;
        require_once DT_ROOT.'/module/member/member.class.php';
        $member_do = new member;
        $getListUser = $member_do->getListUser($lists,'username');
		include tpl('index', $module);
	break;
}
?>