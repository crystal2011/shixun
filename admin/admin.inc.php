<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
define('MANAGE_ADMIN', true);
$AREA or $AREA = cache_read('area.php');
require DT_ROOT.'/admin/admin.class.php';
$do = new admin;
$menus = array (
    array('添加管理员', '?moduleid='.$moduleid.'&file='.$file.'&action=add'),
    array('管理员管理', '?moduleid='.$moduleid.'&file='.$file),
);
$this_forward = '?file='.$file;
switch($action) {
	case 'add':
		if($submit) {
            $post = array(
                'username' => $username,
                'admin' => $admin,
                'role' => $role,
                'password'=>$password,
                'repassword'=>$repassword,
                'aid'=>$aid
            );

            if ($adminuid = $do->add($post)) {
                $r = $do->get_one($username);
                $adminuid = $r['adminuid'];
                if ($r['admin'] == 2) {
                    foreach ($MODULE as $m) {
                        if (isset($roles[$m['moduleid']])) {
                            $right = array();
                            $right['title'] = $m['name'] . '管理';
                            $right['url'] = '?moduleid=' . $m['moduleid'];
                            $do->addadmin($adminuid, $right, $admin);
                        }
                    }
                    if (isset($roles['database'])) {
                        $right = array();
                        $right['title'] = '数据库管理';
                        $right['url'] = '?file=database';
                        $do->addadmin($adminuid, $right, $admin);
                    }
                    if (isset($roles['template'])) {
                        $right = array();
                        $right['title'] = '模板管理';
                        $right['url'] = '?file=template';
                        $do->addadmin($adminuid, $right, $admin);
                        $right = array();
                        $right['title'] = '风格管理';
                        $right['url'] = '?file=skin';
                        $do->add($adminuid, $right, $admin);
                        $right = array();
                        $right['title'] = '标签向导';
                        $right['url'] = '?file=tag';
                        $do->addadmin($adminuid, $right, $admin);
                    }
                    $do->cache_right($adminuid);
                    $do->cache_menu($adminuid);
                }
                msg('管理员添加成功，下一步请分配权限和管理面板', '?file=' . $file . '&id=' . $adminuid . '&tm=' . ($DT_TIME + 5));

            }else{
                msg($do->errmsg);
            }
        } else {
            isset($username) or $username = '';
            include tpl('admin_add');
        }
	break;
	case 'edit':
		if($submit) {
            $post = array(
                'username' => $username,
                'admin' => $admin,
                'role' => $role,
                'password'=>$password,
                'repassword'=>$repassword,
                'aid'=>$aid,
                'adminuid'=>$adminuid,
            );

			if($do->edit($post)) {
				$r = $do->get_one($username);
				$userid = $r['userid'];
				if($r['admin'] == 2) {
					$do->cache_right($userid);
					$do->cache_menu($userid);
				}
				dmsg('修改成功', '?file='.$file);
			}
			msg($do->errmsg);
		} else {
			if(!$userid) msg();
			$user = $do->get_one($userid, 0);
			include tpl('admin_edit');
		}
	break;
    case 'username':
        $value = trim(dhtmlspecialchars($param));
        if(!$value) exit(json_encode(array('status'=>'n','info'=>'请输入用户名')));
        if(!$do->is_username($value)) exit(json_encode(array('status'=>'n','info'=>$do->errmsg)));
        exit(json_encode(array('status'=>'y')));
        break;
    case 'password':
        //验证用户密码
        $value = trim(dhtmlspecialchars($param));
        if(!$do->is_password($value, $value)){
            exit(json_encode(array('status'=>'n','info'=>$do->errmsg)));
        }
        exit(json_encode(array('status'=>'y')));
        break;
	case 'delete':
        $adminuid = intval($adminuid);
        $change_status = $status==1?0:1;
        $status = $status==1?1:0;
        $db->query("update sx_admin_user set status = $change_status where adminuid = $adminuid and status = $status");
		dmsg('操作成功', $this_forward);
	break;
	case 'right':
		if(!$userid) msg();
		$user = $do->get_one($userid, 0);

		if($submit) {

			$right[0]['action'] = isset($right[0]['action']) ? implode('|', $right[0]['action']) : '';
			$right[0]['catid'] = isset($right[0]['catid']) ? implode('|', $right[0]['catid']) : '';

			if($do->update($userid, $right, $user['admin'])) {
				dmsg('更新成功', '?file='.$file.'&action=right&userid='.$userid);
			}
			msg($do->errmsg);
		} else {
			$username = $user['username'];
			$drights = $do->get_right($userid);
			$dmenus = $do->get_menu($userid);
			include tpl('admin_right');
		}
	break;
	case 'ajax':
		@include DT_ROOT.'/'.($mid == 1 ? 'admin' : 'module/'.$MODULE[$mid]['module'].'/admin').'/config.inc.php';
		if(isset($fi)) {
			if(isset($RT) && isset($RT['action'][$fi])) {
				$action_select = '<select name="right[0][action][]" size="2" multiple  style="height:200px;width:150px;"><option value="">选择动作[按Ctrl键多选]</option>';
				foreach($RT['action'][$fi] as $k=>$v) {
					$action_select .= '<option value="'.$k.'">'.$v.'['.$k.']</option>';
				}
				$action_select .= '</select>';
				echo $action_select;
			} else {
				echo '0';
			}
		} else {
			if(isset($RT)) {
				$file_select = '<select name="right[0][file]" size="2" style="height:200px;width:150px;" onchange="get_action(this.value, '.$mid.');"><option value="">选择文件[单选]</option>';
				foreach($RT['file'] as $k=>$v) {
					$file_select .= '<option value="'.$k.'">'.$v.'['.$k.']</option>';
				}
				$file_select .= '</select>';
				echo $file_select.'|';
				if($CT) {
					$CATEGORY = cache_read('category-'.$mid.'.php');
					echo '<select name="right[0][catid][]" size="2" multiple style="height:200px;width:300px;">';
					echo '<option>选择分类多选[按Ctrl键多选]</option>';
					foreach($CATEGORY as $c) {
						if($c['parentid'] == 0) echo '<option value="'.$c['catid'].'">'.$c['catname'].'</option>';
					}
					echo '</select>';
				} else {
					echo '0';
				}
			} else {
				echo '0|0';
			}
		}
	break;
	default:
		$sfields = array('按条件', '用户名', '姓名', '角色');
		$dfields = array('username', 'username', 'truename', 'role');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$type = isset($type) ? intval($type) : 0;
		$areaid = isset($areaid) ? intval($areaid) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = '1=1';
		if($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
		if($type) $condition .= " AND admin=$type";
		if($areaid) $condition .= ($AREA[$areaid]['child']) ? " AND aid IN (".$AREA[$areaid]['arrchildid'].")" : " AND aid=$areaid";
		$lists = $do->get_list($condition);
		include tpl('admin');
	break;
}
?>