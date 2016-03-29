<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
class admin {
	var $userid;
	var $username;
	var $founderid;
	var $db;
	var $pre;
	var $errmsg = errmsg;

	function admin() {
		global $db, $admin, $CFG;
		$this->founderid = $CFG['founderid'];
		$this->db = &$db;
		$this->pre = $this->db->pre;
	}

	function is_member($username) {
		return $this->db->get_one("SELECT adminuid FROM {$this->pre}admin_user WHERE username='$username'");
	}

	function count_admin() {
		$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->pre}admin_user");
		return $r['num'];
	}

	function set_admin($username) {
		$username = trim($username);
		$r = $this->is_member($username);
		if($r) return $this->_('管理员已存在');
		return true;
	}

	function get_one($user, $type = 1) {
		$fields = $type ? 'username' : 'adminuid';
        return $this->db->get_one("SELECT * FROM {$this->pre}admin_user WHERE `$fields`='$user'");
	}

	function get_list($condition) {
		global $pages, $page, $pagesize, $offset, $pagesize, $CFG, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->pre}admin_user WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$admins = array();
		$result = $this->db->query("SELECT * FROM {$this->pre}admin_user WHERE $condition ORDER BY adminuid ASC LIMIT $offset,$pagesize");
        while($r = $this->db->fetch_array($result)) {
            $r['addtime'] = timetodate($r['addtime'], 5);
            $r['adminname'] = $r['admin'] == 1 ? ($CFG['founderid'] == $r['adminuid'] ? '<span class="f_red">网站创始人</span>' : '<span class="f_blue">超级管理员</span>') : '普通管理员';
            $admins[] = $r;
        }
		return $admins;
	}

	function get_right($userid) {
		global $MODULE;
		$rights = array();
		$result = $this->db->query("SELECT * FROM {$this->pre}admin WHERE userid=$userid AND url='' ORDER BY moduleid DESC,file DESC,adminid DESC ");
		while($r = $this->db->fetch_array($result)) {
			@include DT_ROOT.'/'.($r['moduleid'] == 1 ? 'admin' : 'module/'.$MODULE[$r['moduleid']]['module'].'/admin').'/config.inc.php';
			$r['name'] = isset($RT['file'][$r['file']]) ? '('.$RT['file'][$r['file']].')' : '';
			$r['module'] = '('.$MODULE[$r['moduleid']]['name'].')';
			$rights[] = $r;
		}
		return $rights;
	}

	function get_menu($userid) {
		$menus = array();
		$result = $this->db->query("SELECT * FROM {$this->pre}admin WHERE userid=$userid AND url!='' ORDER BY listorder ASC,adminid ASC ");
		while($r = $this->db->fetch_array($result)) {
			$menus[] = $r;
		}
		return $menus;
	}

	function update($userid, $right, $admin) {
		if(isset($right[-1])) {
			$this->addadmin($userid, $right[-1], $admin);
			unset($right[-1]);
			$type = 1;//right
		} else {
			$type = 0;//menu
		}
		$this->addadmin($userid, $right[0], $admin);
		unset($right[0]);
		foreach($right as $k=>$v) {
			if(isset($v['delete'])) {
				$this->delete($k);
				unset($right[$k]);
			}
		}
		if($admin == 1) $this->db->query("DELETE FROM {$this->pre}admin WHERE userid=$userid AND url=''");
		$this->cache_right($userid);
		$this->cache_menu($userid);
		return true;
	}

    function pass($post){
        global $_adminuid,$_adminadmin;
        if($post['adminuid'] && ($post['adminuid']==$_adminuid || $_adminadmin==1) && $post['password']){
            if(!$this->is_password($post['password'],$post['repassword'])) return false;
        }else if(empty($post['adminuid'])){
            if(!$this->is_username($post['username'])) return false;
            if(!$this->is_password($post['password'],$post['repassword'])) return false;
        }


    }

    function set($post){
        $post['admin'] = $post['admin']==1?1:2;
        global $_adminuid,$_adminadmin;
        if($post['adminuid'] && ($post['adminuid']==$_adminuid || $_adminadmin==1)){
            if($post['password'] && !$this->is_password($post['password'],$post['repassword'])) return false;
            $post['password'] = md5(md5($post['password']));
            unset($post['repassword']);
        }else if(empty($post['adminuid'])){
            $post['status'] = 1;
            $post['password'] = md5(md5($post['password']));
            unset($post['repassword']);
        }

        $post = dhtmlspecialchars($post);
        return array_map('trim',$post);
    }

    function addadmin($userid, $right, $admin){

        if(isset($right['url'])) {
            if(!$right['title'] || !$right['url']) return false;
            $r = $this->db->get_one("SELECT * FROM {$this->pre}admin WHERE userid=$userid AND url='".$right['url']."'");
            if($r) return false;
            if($admin == 2 && defined('MANAGE_ADMIN')) {
                $r = $this->url_right($right['url']);
                if($r) $this->addadmin($userid, $r, $admin);
            }
        } else {
            $right['moduleid'] = intval($right['moduleid']);
            if(!$right['moduleid']) return false;
            $_right = $this->get_right($userid);
            foreach($_right as $v) {//module admin
                if($v['file'] == '' && $v['moduleid'] == $right['moduleid']) return false;
            }
            if($right['file']) {//file exists
                foreach($_right as $v) {
                    if($v['file'] == $right['file'] && $v['moduleid'] == $right['moduleid']) return false;
                }
            } else {
                unset($right['action'], $right['catid']);
            }
        }
        $right['userid'] = $userid;
        $sql1 = $sql2 = '';
        foreach($right as $k=>$v) {
            $sql1 .= ','.$k;
            $sql2 .= ",'$v'";
        }
        $sql1 = substr($sql1, 1);
        $sql2 = substr($sql2, 1);
        $this->db->query("INSERT INTO {$this->pre}admin ($sql1) VALUES($sql2)");
    }

    /**
     * 密码检测
     * @param $password
     * @param $cpassword
     * @return bool
     */
    function is_password($password, $cpassword) {
        global $MOD, $L;
        if(!$password) return $this->_('请输入密码');
        if($password != $cpassword) return $this->_('密码不一致');
        $minpassword = 6;
        $maxpassword = 20;
        if(!preg_match('/^[a-zA-Z0-9]{'.$minpassword.','.$maxpassword.'}$/',$password)) return $this->_('请输入6-20个字（数字、字母）');
        return true;
    }


    function is_username($username) {
        global $MOD, $L;
        if(!check_name($username)) return $this->_('格式不正确');
        $MOD['minusername'] = 4;
        $MOD['maxusername'] = 20;
        if(mb_strlen($username,'utf-8') < $MOD['minusername'] || mb_strlen($username,'utf-8') > $MOD['maxusername']) return $this->_('请输入4-20个字');
        if($this->is_member($username)) return $this->_('用户名已存在');
        return true;
    }

	function add($post) {
		$this->pass($post);
        $post = $this->set($post);
        $admin_sqlk = $admin_sqlv = '';
        foreach($post as $k=>$v) {
            $admin_sqlk .= ','.$k; $admin_sqlv .= ",'$v'";
        }
        $admin_sqlk = substr($admin_sqlk,1);
        $admin_sqlv = substr($admin_sqlv,1);
        $this->db->query("INSERT INTO {$this->pre}admin_user ($admin_sqlk) VALUES($admin_sqlv)");
        return $this->db->insert_id();



	}

    function userinfo($username, $cache = 1) {
        global $db, $dc, $CFG;
        if(!check_name($username)) return array();
        $user = array();
        if($cache && $CFG['db_expires']) {
            $user = $dc->get('user-'.$username);
            if($user) return $user;
        }
        $user = $db->get_one("SELECT * FROM {$db->pre}admin_user WHERE username='$username'");
        if($cache && $CFG['db_expires'] && $user) $dc->set('user-'.$username, $user, $CFG['db_expires']);
        return $user;
    }

    function lock($login_lock, $LOCK, $DT_IP, $DT_TIME) {
        if($login_lock && $DT_IP) {
            $LOCK['time'] = $DT_TIME;
            $LOCK['times'] = isset($LOCK['times']) ? $LOCK['times'] + 1 : 1;
            cache_write($DT_IP.'.php', $LOCK, 'ban');
        }
    }

    function login($login_username, $login_password, $login_cookietime = 0) {
        global $DT_TIME, $DT_IP, $MOD, $MODULE, $L;
        if(!check_name($login_username)) return $this->_($L['member_login_username_bad']);
        if(!$MOD || !isset($MOD['login_times'])) $MOD = cache_read('module-2.php');
        $login_lock = ($MOD['login_times'] && $MOD['lock_hour']) ? true : false;
        $LOCK = array();
        if($login_lock) {
            $LOCK = cache_read($DT_IP.'.php', 'ban');
            if($LOCK) {
                if($DT_TIME - $LOCK['time'] < $MOD['lock_hour']*3600) {
                    if($LOCK['times'] >= $MOD['login_times']) return $this->_(lang($L['member_login_ban'], array($MOD['login_times'], $MOD['lock_hour'])));
                } else {
                    $LOCK = array();
                    cache_delete($DT_IP.'.php', 'ban');
                }
            }
        }
        $user = $this->userinfo($login_username, 0);
        if($user['status']!=1) return $this->_('用户禁止登录');
        if(!$user) {
            $this->lock($login_lock, $LOCK, $DT_IP, $DT_TIME);
            return $this->_($L['member_login_not_member']);
        }
        if($user['password'] != (is_md5($login_password) ? md5($login_password) : md5(md5($login_password)))) {
            $this->lock($login_lock, $LOCK, $DT_IP, $DT_TIME);
            return $this->_($L['member_login_password_bad']);
        }
        $userid = $user['adminuid'];
        $cookietime = $DT_TIME + ($login_cookietime ? intval($login_cookietime) : 86400*7);
        $auth = encrypt($user['adminuid'].'|'.$user['password'], DT_KEY.'USER');
        set_cookie('adminauth', $auth, $cookietime);
        set_cookie('adminusername', $user['username'], $DT_TIME + 30*86400);
        $this->db->query("UPDATE sx_admin_user SET loginip='$DT_IP',logintime=$DT_TIME,logintimes=logintimes+1 WHERE adminuid=$userid");
        $this->bind($user['username'], $DT_TIME);
        return $user;
    }

    function bind($username, $addtime) {
        $auth = get_cookie('bind');
        if($auth) {
            set_cookie('bind', '');
            $auth = decrypt($auth, DT_KEY.'BIND');
            if(strpos($auth, '|') !== false) {
                $t = explode('|', $auth);
                $itemid = intval($t[0]);
                $this->db->query("UPDATE {$this->db->pre}oauth SET username='$username',addtime='$addtime' WHERE itemid=$itemid");
            }
        }
    }

	function edit($post) {
		$this->pass($post);
        $post = $this->set($post);
        $member_sql  = '';

        foreach($post as $k=>$v) {
            $member_sql .= ",$k='$v'";
        }
        $member_sql = substr($member_sql, 1);
        $this->db->query("UPDATE sx_admin_user SET $member_sql WHERE adminuid=".$post['adminuid']);
        return true;
	}

	function url_right($url) {
		if(substr($url, 0, 1) == '?') $url = substr($url, 1);
		$arr = array();
		parse_str($url);
		$arr['moduleid'] = isset($moduleid) ? $moduleid : 1;
		$arr['file'] = isset($file) ? $file : 'index';
		$arr['action'] = isset($action) ? $action : '';
		return $arr;
	}

	function cache_right($userid) {
		$rights = $this->get_right($userid);
		$right = $moduleids = array();
		foreach($rights as $v) {//get moduleids
			isset($moduleids[$v['moduleid']]) or $moduleids[$v['moduleid']] = $v['moduleid'];
		}
		foreach($moduleids as $m) {//get rights
			foreach($rights as $r) {
				if($r['moduleid'] == $m) {
					$r['file'] = $r['file'] ? $r['file'] : 'NA';
					$right[$m][$r['file']]['action'] = $r['action'] ? explode('|', $r['action']) : '';
					$right[$m][$r['file']]['catid'] = $r['catid'] ? explode('|', $r['catid']) : '';
				}
			}
		}
		foreach($right as $k=>$v) {
			if(isset($v['NA'])) $right[$k] = '';
		}
		foreach($right as $k=>$v) {
			if($v) {
				foreach($v as $i=>$j) {
					if(!$j['action'] && !$j['catid']) $right[$k][$i] = '';
				}
			}
		}
		cache_write('right-'.$userid.'.php', $right);		
	}

	function cache_menu($userid) {
		$menus = $this->get_menu($userid);
		$menu = $r = array();
		foreach($menus as $k=>$v) {
			$r['title'] = $v['title'];
			$r['style'] = $v['style'];
			$r['url'] = $v['url'];
			$menu[] = $r;
		}
		cache_write('menu-'.$userid.'.php', $menu);
	}

	function delete($adminid) {
		$this->db->query("DELETE FROM {$this->pre}admin WHERE adminid=$adminid");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>