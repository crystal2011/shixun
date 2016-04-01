<?php 
defined('IN_DESTOON') or exit('Access Denied');
class job {
	var $moduleid;
	var $itemid;
	var $db;
	var $table;
	var $table_data;
	var $split;
	var $errmsg = errmsg;

    function job($moduleid) {
		global $db, $table, $table_data, $MOD;
		$this->moduleid = $moduleid;
		$this->table = $db->pre.'job';
		$this->table_data = $db->pre.'job_data';
		$this->split = '';
		$this->db = &$db;
		$this->fields = array('catid','istop','areaid','userid','thumb','level','title','address','style','fee','introduce','department','total','minsalary','maxsalary','type','gender','marriage','education','minage','maxage','experience','status','hits','truename','telephone', 'mobile','address','email','msn','qq','ali','skype','sex','totime','editor','addtime','adddate','edittime','editdate','ip','template','linkurl','filepath','note','company');
    }

    function jobList($field='*',$condition='1=1',$order='hits desc',$limit=10){
        global $page;
        $offset = ($page-1)*$limit;
        $result = $this->db->query("select {$field} from {$this->table} where $condition  order by $order limit $offset,$limit");
        $list = array();
        while($r = $this->db->fetch_array($result)){
            $list[] = $this->show($r);
        }
        $count = $this->db->get_one("select count(*) as num from {$this->table} where $condition");
        $totalpage = ceil($count['num']/$limit);
        return array($list,$totalpage);
    }

    function show($r){
        return $r;
    }

	function pass($post,$isadmin=true) {
		global $DT_TIME, $MOD;
		if(!is_array($post)) return false;

        require_once DT_ROOT.'/module/member/member.class.php';
        $member_do = new member;
        if($isadmin){
            if(!$aUser = $member_do->isCompany($post['username'])) return $this->_($member_do->errmsg);
            else $post['userid'] = $aUser['userid'];
        }else{
            $post['clear_link'] = true;
            $post['save_remotepic'] = true;
        }

        if(!is_length($post['title'],2,50)) return $this->_('标题输入有误');
        $catinfo = get_cat($post['catid']);
        if(!$catinfo || $catinfo['moduleid']!=9 || $catinfo['child']) return $this->_('分类选择有误');
        $areainfo = get_area($post['areaid']);
        if(!$areainfo || count(explode(',',$areainfo['arrparentid']))<2) return $this->_('地区选择有误');
        if(!is_length($post['address'],3,50)) return $this->_('地址输入有误');
        if(DT_MAX_LEN && strlen($post['content']) > DT_MAX_LEN) return $this->_(lang('message->pass_max'));

        if(!is_number($post['total'],3)) return $this->_('招聘人数输入有误');
        if(!is_number($post['minsalary']) || !is_number($post['maxsalary']) || $post['minsalary'] > $post['maxsalary']) return $this->_('月薪输入有误');
        $aSetting = cache_read('module-9.php');
        $aSetting['education'] = explode('|',$aSetting['education']);
        $aSetting['type'] = explode('|',$aSetting['type']);
        if(!isset($aSetting['type'][$post['type']])) return $this->_('学历要求选择有误');
        if(!isset($aSetting['education'][$post['education']])) return $this->_('工作性质选择有误');
        if(!is_number($post['experience']) || $post['experience']<0 || $post['experience']>20) return $this->_('工作经验选择有误');
        return $post;
	}

	function set($post) {
		global $MOD, $DT_TIME, $DT_IP, $TYPE, $_username, $_userid;
		$post['edittime'] = $DT_TIME;
		$post['total'] = intval($post['total']);
		$post['minsalary'] = intval($post['minsalary']);
		$post['maxsalary'] = intval($post['maxsalary']);
		$post['type'] = intval($post['type']);
		$post['education'] = intval($post['education']);
		$post['experience'] = intval($post['experience']);

        $post['content'] = stripslashes($post['content']);
        $post['content'] = save_local($post['content']);
        if(isset($post['clear_link']) && $post['clear_link']) $post['content'] = clear_link($post['content']);
        if(isset($post['save_remotepic']) && $post['save_remotepic']) $post['content'] = save_remote($post['content']);
        if(strpos($post['content'], 'pagebreak') !== false) $post['content'] = str_replace(array('<hr class="de-pagebreak" /></p>', '<p><hr class="de-pagebreak" />', '<hr class="de-pagebreak" /></div>', '<div><hr class="de-pagebreak" />'), array('</p><hr class="de-pagebreak" />', '<hr class="de-pagebreak" /><p>', '</div><hr class="de-pagebreak" />', '<hr class="de-pagebreak" /><div>'), $post['content']);
        if($post['content'] && empty($post['introduce'])) $post['introduce'] = addslashes(get_intro($post['content'], isset($post['introduce_length']) && $post['introduce_length']?$post['introduce_length']:200));


        if($this->itemid) {
            $new = $post['content'];
            if($post['thumb']) $new .= '<img src="'.$post['thumb'].'"/>';
            $r = $this->get_one();
            $old = $r['content'];
            delete_diff($new, $old);

        } else {
            $post['addtime'] = $DT_TIME;
            $post['status'] = isset($post['status'])?$post['status']:3;
        }
		$content = $post['content'];
		unset($post['content']);
		$post = dhtmlspecialchars($post);
		$post['content'] = addslashes(dsafe($content));

		return array_map("trim", $post);
	}

	function get_one() {
		$content_table = content_table($this->moduleid, $this->itemid, $this->split, $this->table_data);
        return $this->db->get_one("SELECT * FROM {$this->table} a,{$content_table} c WHERE a.itemid=c.itemid and a.itemid=$this->itemid");
	}

	function get_list($condition = 'status=3', $order = 'edittime DESC', $cache = '') {
		global $MOD, $pages, $page, $pagesize, $offset, $CATEGORY, $items, $sum;
        $CATEGORY = cache_read('category-23.php');
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE $condition", $cache);
			$items = $r['num'];
		}
		$pages = defined('CATID') ? listpages(1, CATID, $items, $page, $pagesize, 10, $MOD['linkurl']) : pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = $this->db->query("SELECT * FROM {$this->table} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize", $cache);
		while($r = $this->db->fetch_array($result)) {
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
            $catids[$r['catid']] = $r['catid'];
			$lists[] = $r;
		}
        if($catids) {
            $result = $this->db->query("SELECT catid,catname,linkurl FROM {$this->db->pre}category WHERE catid IN (".implode(',', $catids).")");
            while($r = $this->db->fetch_array($result)) {
                $CATS[$r['catid']] = $r;
            }
            if($CATS) {
                foreach($lists as $k=>$v) {
                    $lists[$k]['catname'] = $v['catid'] ? $CATS[$v['catid']]['catname'] : '';
                    $lists[$k]['caturl'] = $v['catid'] ? $MOD['linkurl'].$CATS[$v['catid']]['linkurl'] : '';
                }
            }
        }
		return $lists;
	}

	function add($post) {
		global $MOD;
		$post = $this->set($post);
		$sqlk = $sqlv = '';
		foreach($post as $k=>$v) {
			if(in_array($k, $this->fields)) { $sqlk .= ','.$k; $sqlv .= ",'$v'"; }
		}
        $sqlk = substr($sqlk, 1);
        $sqlv = substr($sqlv, 1);
		$this->db->query("INSERT INTO {$this->table} ($sqlk) VALUES ($sqlv)");
		$this->itemid = $this->db->insert_id();
		$content_table = content_table($this->moduleid, $this->itemid, $this->split, $this->table_data);
		$this->db->query("REPLACE INTO {$content_table} (itemid,content) VALUES ('$this->itemid', '$post[content]')");
		clear_upload($post['content'].$post['thumb'], $this->itemid);
        addPublishs($post['userid']);
		return $this->itemid;
	}

	function edit($post) {
		$this->delete($this->itemid, false);
		$post = $this->set($post);
		$sql = '';
		foreach($post as $k=>$v) {
			if(in_array($k, $this->fields)) $sql .= ",$k='$v'";
		}
        $sql = substr($sql, 1);
	    $this->db->query("UPDATE {$this->table} SET $sql WHERE itemid=$this->itemid");
		$content_table = content_table($this->moduleid, $this->itemid, $this->split, $this->table_data);
		$this->db->query("REPLACE INTO {$content_table} (itemid,content) VALUES ('$this->itemid', '$post[content]')");
		clear_upload($post['content'].$post['thumb'], $this->itemid);
        if(isset($post['status']) && $post['status']==3){
            horncode(2,$this->itemid);
        }
		return true;
	}

	function tohtml($itemid = 0, $catid = 0) {
		global $module, $MOD;

	}

	function update($itemid) {
		$item = $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid=$itemid");
		$update = '';
		$keyword = $item['title'].','.$item['department'].','.strip_tags(cat_pos(get_cat($item['catid']), ',')).strip_tags(area_pos($item['areaid'], ','));
		if($keyword != $item['keyword']) {
			$keyword = str_replace("//", '', addslashes($keyword));
			$update .= ",keyword='$keyword'";
		}
		$item['itemid'] = $itemid;
		$linkurl = itemurl($item);
		if($linkurl != $item['linkurl']) $update .= ",linkurl='$linkurl'";
		$member = $item['username'] ? userinfo($item['username']) : array();
		if($member) {
			foreach(array('groupid','vip','validated','company') as $v) {
				if($item[$v] != $member[$v]) $update .= ",$v='".addslashes($member[$v])."'";
			}
			if($item['email'] != $member['mail']) $update .= ",email='".addslashes($member['mail'])."'";
		}
		if($update) $this->db->query("UPDATE {$this->table} SET ".(substr($update, 1))." WHERE itemid=$itemid");
	}

	function recycle($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->recycle($v); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=0 WHERE itemid=$itemid");
			$this->delete($itemid, false);
			return true;
		}		
	}

	function restore($itemid) {
		global $module, $MOD;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->restore($v); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=3 WHERE itemid=$itemid");
            horncode(2,$itemid);
			return true;
		}		
	}

	function delete($itemid, $all = true) {
		global $MOD;
		if(is_array($itemid)) {
			foreach($itemid as $v) {
				$this->delete($v, $all);
			}
		} else {
			$this->itemid = $itemid;
			$r = $this->get_one();

			if($all) {
				$userid = get_user($r['username']);
				if($r['content']) delete_local($r['content'], $userid);
				$this->db->query("DELETE FROM {$this->table} WHERE itemid=$itemid");
				$content_table = content_table($this->moduleid, $this->itemid, $this->split, $this->table_data);
				$this->db->query("DELETE FROM {$content_table} WHERE itemid=$itemid");
				if($MOD['cat_property']) $this->db->query("DELETE FROM {$this->db->pre}category_value WHERE moduleid=$this->moduleid AND itemid=$itemid");
			}
		}
	}

	function check($itemid) {
		global $_username, $DT_TIME, $MOD;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->check($v); }
		} else {
			$this->itemid = $itemid;
			$item = $this->get_one();
			$editdate = timetodate($DT_TIME, 3);
			$this->db->query("UPDATE {$this->table} SET status=3,hits=hits+1,editor='$_username',edittime=$DT_TIME,editdate='$editdate' WHERE itemid=$itemid");
            horncode(2,$itemid);
			return true;
		}
	}

	function reject($itemid) {
		global $_username, $DT_TIME;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->reject($v); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=1,editor='$_username' WHERE itemid=$itemid");
			return true;
		}
	}

	function expire($condition = '') {
		global $DT_TIME;
		$this->db->query("UPDATE {$this->table} SET status=4 WHERE status=3 AND totime>0 AND totime<$DT_TIME $condition");
	}

	function clear($condition = 'status=0') {		
		$result = $this->db->query("SELECT itemid FROM {$this->table} WHERE $condition");
		while($r = $this->db->fetch_array($result)) {
			$this->delete($r['itemid']);
		}
	}

	function level($itemid, $level) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$this->db->query("UPDATE {$this->table} SET level=$level WHERE itemid IN ($itemids)");
	}

	function refresh($itemid) {
		global $DT_TIME;
		$editdate = timetodate($DT_TIME, 3);
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$this->db->query("UPDATE {$this->table} SET edittime='$DT_TIME',editdate='$editdate' WHERE itemid IN ($itemids)");
	}

	function _update($username) {
		global $DT_TIME;
		$this->db->query("UPDATE {$this->table} SET status=4 WHERE status=3 AND totime>0 AND totime<$DT_TIME AND username='$username'");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}

    function checkJob($aJob){
        if(!$aJob){
            return $this->_('招聘信息不存在');
        }
        if($aJob['status']!=3){
            return $this->_('招聘信息已下架');
        }
        return true;
    }
    function editHits(){
        $this->db->query("update {$this->table} set hits = hits + 1 where itemid = $this->itemid");
    }

    function getright($field='*',$limit,$order=''){
        global $dtcity;
        $where = ' status=3 ';
        if($dtcity){
            $info = get_area($dtcity['areaid']);
            $arrchildid = $info['arrchildid'];
            $where .= " and areaid in ($arrchildid)";
        }
        $list = array();
        $result = $this->db->query("select {$field} from {$this->table} where {$where} order by {$order} limit $limit");
        while($r=$this->db->fetch_array($result)){
            $list[] = $r;
        }
        return $list;
    }

    function getLevelJob($field='*',$limit,$catid=0,$level=0){
        global $dtcity;
        $where = ' 1=1 ';
        if($catid){
            $info = get_cat($catid);
            $arrchildid = $info['arrchildid'];
            $where .= " and catid in ($arrchildid)";
        }
        if($dtcity){
            $info = get_area($dtcity['areaid']);
            $arrchildid = $info['arrchildid'];
            $where .= " and areaid in ($arrchildid)";
        }
        $aHotJob= array();
        $result = $this->db->query("select {$field} from {$this->table} where {$where} and level={$level} and status = 3 order by itemid desc limit $limit");
        while($r=$this->db->fetch_array($result)){
            $aHotJob[] = $r;
        }
        return $aHotJob;
    }

    function addLike(){
        $this->db->query("update {$this->table} set likes=likes+1 where itemid = ".$this->itemid);
        return true;
    }

    function adddelComments($addordel){
        if($addordel=='add'){
            $this->db->query("update {$this->table} set comments=comments+1 where itemid = ".$this->itemid);
        }else{
            $this->db->query("update {$this->table} set comments=comments-1 where itemid = ".$this->itemid);
        }
        return true;
    }
}
?>