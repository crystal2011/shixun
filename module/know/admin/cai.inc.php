<?php
defined('DT_ADMIN') or exit('Access Denied');
require MD_ROOT.'/cai.class.php';
$do = new cai;
$MOD['name'] = '菜式';
$menus = array (
    array($MOD['name'].'列表', '?moduleid='.$moduleid.'&file=cai'),
    array('审核'.$MOD['name'], '?moduleid='.$moduleid.'&action=check&file=cai'),
    array('未通过'.$MOD['name'], '?moduleid='.$moduleid.'&action=reject&file=cai'),
    array('回收站', '?moduleid='.$moduleid.'&action=recycle&file=cai'),

);
$MOD['levelcai'] = explode('|',$MOD['levelcai']);
if(in_array($action,array('add'))){
    $knowid = isset($knowid)?intval($knowid):0;
    require_once MD_ROOT.'/know.class.php';
    $knowdo = new know(10);
    $knowdo->itemid = $knowid;
    $know = $knowdo->get_one();
    if(!$know || $know['status']!=3) dmsg('不能添加菜系','?moduleid='.$moduleid);
    $userid = $post['userid'] = $know['userid'];
    $menus[] = array('添加'.$MOD['name'], '?moduleid='.$moduleid.'&action=add&file=cai&knowid='.$knowid);
}


if($_catids || $_areaids) require DT_ROOT.'/admin/admin_check.inc.php';

if(in_array($action, array('', 'check', 'reject', 'recycle'))) {
    $sfields = array('模糊', '名字');
    $dfields = array('keyword', 'title', 'addition', 'comment', 'username', 'passport', 'expert', 'ask', 'editor', 'ip', 'filepath', 'template');
    $sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '更新时间降序', '更新时间升序', '浏览次数降序', '浏览次数升序', '答案数量降序', '答案数量升序', '悬赏'.$DT['credit_name'].'降序', '悬赏'.$DT['credit_name'].'升序', '信息ID降序', '信息ID升序');
    $dorder  = array($MOD['order'], 'addtime DESC', 'addtime ASC', 'updatetime DESC', 'updatetime ASC', 'hits DESC', 'hits ASC', 'answer DESC', 'answer ASC', 'credit DESC', 'credit ASC', 'itemid DESC', 'itemid ASC');

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
            if($do->pass($post)) {
                $do->add($post);
                dmsg('添加成功', '?moduleid='.$moduleid.'&action='.$action.'&catid='.$post['catid'].'&file=cai&knowid='.$knowid);
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
            $username = $_username;
            $item = array();
            $levelcai = 0;
            $menuid = 4;
            isset($url) or $url = '';
            if($url) {
                $tmp = fetch_url($url);
                if($tmp) extract($tmp);
            }
            include tpl('caiedit', $module);
        }
        break;
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
            $item = $do->get_one();
            extract($item);
            $addtime = timetodate($addtime);
            $menuon = array('4', '3', '2', '1');
            $menuid = $menuon[$status];
            include tpl('caiedit', $module);
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
            $menuid = 4;
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
        $menuid = 3;
        require_once DT_ROOT.'/module/member/member.class.php';
        $member_do = new member;
        $getListUser = $member_do->getListUser($lists,'username');

        $getSchoolUser = $do->getSchoolUser($lists);
        include tpl('cai', $module);
        break;
    case 'reject':
        if($itemid && !$psize) {
            $do->reject($itemid);
            dmsg('拒绝成功', $forward);
        } else {
            $lists = $do->get_list('status=1'.$condition, $dorder[$order]);
            $menuid = 2;
            require_once DT_ROOT.'/module/member/member.class.php';
            $member_do = new member;
            $getListUser = $member_do->getListUser($lists,'username');
            $getSchoolUser = $do->getSchoolUser($lists);
            include tpl('cai', $module);
        }
        break;
    case 'check':
        if($itemid && !$psize) {
            $do->check($itemid);
            dmsg('审核成功', $forward);
        } else {
            $lists = $do->get_list('status=2'.$condition, $dorder[$order]);
            $menuid = 1;
            require_once DT_ROOT.'/module/member/member.class.php';
            $member_do = new member;
            $getListUser = $member_do->getListUser($lists,'username');
            $getSchoolUser = $do->getSchoolUser($lists);
            include tpl('cai', $module);
        }
        break;
    default:
        $lists = $do->get_list('status=3'.$condition, $dorder[$order]);
        $menuid = 0;
        require_once DT_ROOT.'/module/member/member.class.php';
        $member_do = new member;
        $getListUser = $member_do->getListUser($lists,'username');
        $getSchoolUser = $do->getSchoolUser($lists);
        include tpl('cai', $module);
        break;
}
?>