<?php
defined('DT_ADMIN') or exit('Access Denied');
require MD_ROOT.'/know.class.php';
$do = new know($moduleid);
$menus = array (
    array('添加'.$MOD['name'], '?moduleid='.$moduleid.'&action=add'),
    array($MOD['name'].'列表', '?moduleid='.$moduleid),
    array('审核'.$MOD['name'], '?moduleid='.$moduleid.'&action=check'),
    array('未通过'.$MOD['name'], '?moduleid='.$moduleid.'&action=reject'),
    array('回收站', '?moduleid='.$moduleid.'&action=recycle'),
);



if($_catids || $_areaids) require DT_ROOT.'/admin/admin_check.inc.php';

if(in_array($action, array('', 'check', 'reject', 'recycle'))) {
	$sfields = array('模糊', '名字');
	$dfields = array('keyword', 'title');
	$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '更新时间降序', '更新时间升序', '浏览次数降序', '浏览次数升序', '信息ID降序', '信息ID升序');
	$dorder  = array($MOD['order'], 'addtime DESC', 'addtime ASC', 'updatetime DESC', 'updatetime ASC', 'hits DESC', 'hits ASC', 'itemid DESC', 'itemid ASC');

	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($order) && isset($dorder[$order]) or $order = 0;
	$level = isset($level) ? intval($level) : 0;
	$process = isset($process) ? intval($process) : 99;

	isset($datetype) && in_array($datetype, array('edittime', 'addtime', 'updatetime')) or $datetype = 'addtime';
	$fromdate = isset($fromdate) && is_date($fromdate) ? $fromdate : '';
	$fromtime = $fromdate ? strtotime($fromdate.' 0:0:0') : 0;
	$todate = isset($todate) && is_date($todate) ? $todate : '';
	$totime = $todate ? strtotime($todate.' 23:59:59') : 0;

	$thumb = isset($thumb) ? intval($thumb) : 0;
	$guest = isset($guest) ? intval($guest) : 0;
	$hidden = isset($hidden) ? intval($hidden) : 0;
	$expert = isset($expert) ? intval($expert) : 0;
	$itemid or $itemid = '';

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
	if($guest) $condition .= " AND username=''";
	if($hidden) $condition .= " AND hidden=1";
	if($expert) $condition .= " AND expert<>''";
	if($itemid) $condition .= " AND itemid=$itemid";
	if($process != 99) $condition .= " AND process=$process";

	$timetype = strpos($dorder[$order], 'edit') === false ? 'add' : '';
}
switch($action) {
	case 'add':
		if($submit) {
			if($post = $do->pass($post)) {
				$do->add($post);
				dmsg('添加成功', '?moduleid='.$moduleid.'&action='.$action.'&catid='.$post['catid']);
			} else {
				msg($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				isset($$v) or $$v = '';
			}
			$content = '';
			$status = 3;
			$addtime = timetodate($DT_TIME);
			$username = '';
			$item = array();
			$menuid = 0;
			isset($url) or $url = '';
			if($url) {
				$tmp = fetch_url($url);
				if($tmp) extract($tmp);
			}
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
	case 'active':
		is_array($itemid) or msg('请选择'.$MOD['name']);
		foreach($itemid as $v) {
			$do->active($v);
		}
		dmsg('激活成功', $forward);
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
        require_once DT_ROOT.'/module/member/member.class.php';
        $member_do = new member;
        $getListUser = $member_do->getListUser($lists,'username');
		$menuid = 4;
		include tpl('index', $module);
	break;
	case 'reject':
		if($itemid && !$psize) {
			$do->reject($itemid);
			dmsg('拒绝成功', $forward);
		} else {
			$lists = $do->get_list('status=1'.$condition, $dorder[$order]);
            require_once DT_ROOT.'/module/member/member.class.php';
            $member_do = new member;
            $getListUser = $member_do->getListUser($lists,'username');
			$menuid = 3;
			include tpl('index', $module);
		}
	break;
	case 'check':
		if($itemid && !$psize) {
			$do->check($itemid);
			dmsg('审核成功', $forward);
		} else {
			$lists = $do->get_list('status=2'.$condition, $dorder[$order]);
            require_once DT_ROOT.'/module/member/member.class.php';
            $member_do = new member;
            $getListUser = $member_do->getListUser($lists,'username');
			$menuid = 2;
			include tpl('index', $module);
		}
	break;
    case 'fenglist':
        //名厨风采列表
        $list = $do->get_fenglist();
        $menuid = 1;
        $MOD['name'] = '名厨风采';
        $menus = array (
            array('添加'.$MOD['name'], '?moduleid='.$moduleid.'&action=fengadd'),
            array($MOD['name'].'列表', '?moduleid='.$moduleid.'&action=fenglist'),
        );

        include tpl('fenglist', $module);
    break;
    case 'fengadd':
        //名厨风采列表
        if($submit){
            $areaid = isset($post['areaid'])?$post['areaid']:0;
            $title = isset($post['title'])?$post['title']:0;
            if(!is_length($title,2,50)) dmsg('标题填写有误',$forward);
            $areainfo = get_area($areaid);
            if(!$areainfo || count(explode(',',$areainfo['arrparentid']))<2) dmsg('地区选择有误',$forward);
            $db->query("insert into {$db->pre}know_feng (areaid,title,addtime,edittime) values ($areaid,'$title',$DT_TIME,$DT_TIME)");
            dmsg('添加成功',$forward);
        }else{
            $menus = array (
                array('添加'.$MOD['name'], '?moduleid='.$moduleid.'&action=fengadd'),
                array($MOD['name'].'列表', '?moduleid='.$moduleid.'&action=fenglist'),
            );
            $title = '';
            $menuid = 0;
            include tpl('fengedit', $module);
        }

        break;
    case 'fengedit':
        //名厨风采列表
        $itemid = isset($itemid)?intval($itemid):0;
        $info = $db->get_one("select * from {$db->pre}know_feng where itemid = ".$itemid);
        if(!$info){
            dmsg('不存在',$forward);
        }
        if($submit){
            $areaid = isset($post['areaid'])?$post['areaid']:0;
            $title = isset($post['title'])?$post['title']:0;
            if(!is_length($title,2,50)) dmsg('标题输入有误',$forward);
            $areainfo = get_area($areaid);
            if(!$areainfo || count(explode(',',$areainfo['arrparentid']))<2) dmsg('地区选择有误',$forward);
            $db->query("update {$db->pre}know_feng set areaid = $areaid,title='$title',edittime=$DT_TIME where itemid = ".$itemid);
            dmsg('编辑成功',$forward);
        }else{
            extract($info);
            $menus = array (
                array('添加'.$MOD['name'], '?moduleid='.$moduleid.'&action=fengadd'),
                array($MOD['name'].'列表', '?moduleid='.$moduleid.'&action=fenglist'),
            );

            $menuid = 0;
            include tpl('fengedit', $module);
        }

    break;
    case 'fengdel':
        //名厨风采列表
        $itemid = isset($itemid)?intval($itemid):0;
        $db->query("delete from {$db->pre}know_feng where itemid = ".$itemid);
        dmsg('删除成功',$forward);
        break;
	default:
		$lists = $do->get_list('status=3'.$condition, $dorder[$order]);
        require_once DT_ROOT.'/module/member/member.class.php';
        $member_do = new member;
        $getListUser = $member_do->getListUser($lists,'username');
		$menuid = 1;
		include tpl('index', $module);
	break;
}
?>