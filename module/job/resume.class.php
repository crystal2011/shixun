<?php 
defined('IN_DESTOON') or exit('Access Denied');
class resume {
	var $moduleid;
	var $itemid;
	var $db;
	var $table;
	var $table_data;
	var $errmsg = errmsg;

    function resume($moduleid) {
		global $db, $DT_PRE, $MOD;
		$this->moduleid = $moduleid;
		$this->table = $DT_PRE.'resume';
		$this->table_data = $DT_PRE.'resume_data';
		$this->db = &$db;
		$this->fields = array('catid','areaid','level','title','style','fee','introduce','truename','userid','gender','birthday','age','marriage','height', 'weight','education','school','major','skill','language','minsalary','maxsalary','situation','type','experience','mobile','telephone','address','email','msn','qq','ali','skype','thumb','username','addtime','editor','edittime','ip','template','status','hits','open','note');
    }

    function adddelComments($addordel){
        if($addordel=='add'){
            $this->db->query("update {$this->table} set comments=comments+1 where itemid = ".$this->itemid);
        }else{
            $this->db->query("update {$this->table} set comments=comments-1 where itemid = ".$this->itemid);
        }
        return true;
    }

    function getright($field='*',$limit,$order=''){
        global $dtcity;
        $where = ' situation = 1 and status=3 ';
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

        //一个会员只有一个简历
        $whese = 'userid = '.$post['userid'];
        if($this->itemid){
            $whese .= " and itemid != ".$this->itemid;
        }
        $hasl = $this->db->get_one("select * from {$this->table} where $whese");
        if($hasl) return $this->_('一个会员只能发布一个简历');

        $catinfo = get_cat($post['catid']);
        if(!$catinfo || $catinfo['moduleid']!=9 || $catinfo['child']) return $this->_('分类选择有误');
        if(!is_length($post['truename'],2,10)) return $this->_('真实姓名输入有误');
        $areainfo = get_area($post['areaid']);
        if(!$areainfo || count(explode(',',$areainfo['arrparentid']))<2) return $this->_('地区选择有误');
        if(intval($post['byear']) > 9999 || intval($post['byear']) < 1900 || date('Y', $DT_TIME) - intval($post['byear']) > 100) return $this->_(lang('message->pass_resume_byear'));
        if(!is_length($post['school'],2,50)) return $this->_(lang('message->pass_resume_school'));
        if(!is_mobile($post['telephone']) && !is_phone($post['telephone'])) return $this->_('电话输入有误');
        if(!is_email($post['email'])) return $this->_('邮箱输入有误');
        if(!is_length($post['address'],2,50)) return $this->_('地址输入有误');
        if(!is_number($post['minsalary']) || !is_number($post['maxsalary']) || $post['minsalary'] > $post['maxsalary']) return $this->_('月薪输入有误');
        if($post['thumb']!='' && !is_url($post['thumb'])) return $this->_('图片上传有误');
        if($post['gender']!=1 && $post['gender']!=2)  return $this->_('性别选择有误');
        if($post['marriage']!=1 && $post['marriage']!=2)  return $this->_('婚姻状况选择有误');
        if($post['height']!='' && !is_number($post['height'],2,3)) return $this->_('身高填写有误');
        if($post['weight']!='' && !is_number($post['weight'],2,3)) return $this->_('体重填写有误');
        if(DT_MAX_LEN && strlen($post['content']) > DT_MAX_LEN) return $this->_(lang('message->pass_max'));
        if($post['major']!='' && !is_length($post['major'],2,50)) return $this->_('所学专业有误');
        if($post['skill']!='' && !is_length($post['skill'],2,50)) return $this->_('所学技能有误');
        if($post['language']!='' && !is_length($post['language'],2,50)) return $this->_('语言水平有误');


        $aSetting = cache_read('module-9.php');
        $aSetting['education'] = explode('|',$aSetting['education']);
        $aSetting['type'] = explode('|',$aSetting['type']);
        $aSetting['marriage'] = explode('|',$aSetting['marriage']);
        $aSetting['situation'] = explode('|',$aSetting['situation']);

        if(!isset($aSetting['education'][$post['education']])) return $this->_('学历要求选择有误');
        if(!isset($aSetting['type'][$post['type']])) return $this->_('工作性质选择有误');
        if(!is_number($post['experience'],1,2)) return $this->_('工作经验选择有误');
        if(empty($post['situation']) || !isset($aSetting['situation'][$post['situation']])) return $this->_('求职状态有误');
		return $post;
	}

	function set($post) {
		global $MOD, $DT_TIME, $DT_IP, $TYPE, $_username, $_userid, $GENDER, $MARRIAGE, $EDUCATION;
		$post['addtime'] = (isset($post['addtime']) && $post['addtime']) ? strtotime($post['addtime']) : $DT_TIME;
		$post['edittime'] = $DT_TIME;
		$post['birthday'] = intval($post['byear']).'-'.intval($post['bmonth']).'-'.intval($post['bday']);
		$post['age'] = date('Y', $DT_TIME) - intval($post['byear']);
		$post['minsalary'] = intval($post['minsalary']);
		$post['maxsalary'] = intval($post['maxsalary']);
		$post['type'] = intval($post['type']);
		$post['marriage'] = intval($post['marriage']);
        $post['status'] = 3;
		$post['height'] = intval($post['height']);
		$post['height'] = intval($post['height']);
		$post['gender'] = intval($post['gender']);
		$post['education'] = intval($post['education']);
		$post['experience'] = intval($post['experience']);
		$post['situation'] = intval($post['situation']);

        $post['content'] = stripslashes($post['content']);
        $post['content'] = save_local($post['content']);
        if($post['clear_link']) $post['content'] = clear_link($post['content']);
        if($post['save_remotepic']) $post['content'] = save_remote($post['content']);
        if(strpos($post['content'], 'pagebreak') !== false) $post['content'] = str_replace(array('<hr class="de-pagebreak" /></p>', '<p><hr class="de-pagebreak" />', '<hr class="de-pagebreak" /></div>', '<div><hr class="de-pagebreak" />'), array('</p><hr class="de-pagebreak" />', '<hr class="de-pagebreak" /><p>', '</div><hr class="de-pagebreak" />', '<hr class="de-pagebreak" /><div>'), $post['content']);
        if($post['content'] && empty($post['introduce'])) $post['introduce'] = addslashes(get_intro($post['content'], isset($post['introduce_length']) && $post['introduce_length']?$post['introduce_length']:200));


        if($this->itemid) {
			$new = $post['content'];
			if($post['thumb']) $new .= '<img src="'.$post['thumb'].'"/>';
			$r = $this->get_one();
			$old = $r['content'];
			if($r['thumb']) $old .= '<img src="'.$r['thumb'].'"/>';
			delete_diff($new, $old);
		} else {			
			$post['ip'] = $DT_IP;
		}
		$content = $post['content'];
		unset($post['content']);
		$post = dhtmlspecialchars($post);
		$post['content'] = addslashes(dsafe($content));
		return array_map("trim", $post);
	}

	function get_one($where='') {
        if(empty($where)){
            $where = " itemid= ".$this->itemid;
        }
		$r = $this->db->get_one("SELECT * FROM {$this->table} WHERE ".$where);
		if($r) {
			$t = $this->db->get_one("SELECT content FROM {$this->table_data} WHERE itemid=".$r['itemid']);
			$r['content'] = $t ? $t['content'] : '';
			return $r;
		} else {
			return array();
		}
	}

    function jobList($field='*',$condition='1=1',$order='hits desc',$limit=10){
        global $page;
        $offset = ($page-1)*$limit;
        $result = $this->db->query("select {$field} from {$this->table} where $condition  order by $order limit $offset,$limit");
        $list = array();
        while($r = $this->db->fetch_array($result)){
            $list[] = $r;
        }
        $count = $this->db->get_one("select count(*) as num from {$this->table} where $condition");
        $totalpage = ceil($count['num']/$limit);
        return array($list,$totalpage);
    }

    function editHits(){
        $this->db->query("update {$this->table} set hits = hits + 1 where itemid = $this->itemid");
    }

    function checkBuy($aBuy){
        if(!$aBuy){
            return $this->_('简历信息不存在');
        }
        if($aBuy['status']!=3){
            return $this->_('简历信息不存在');
        }
        if($aBuy['situation']!=1){
            return $this->_('简历信息不能访问');
        }
        return true;
    }

	function get_list($condition = 'status=3', $order = 'edittime DESC', $cache = '') {
		global $MOD, $pages, $page, $pagesize, $offset, $CATEGORY, $items, $sum;
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
			if(strpos($r['linkurl'], '://') === false) $r['linkurl'] = $MOD['linkurl'].$r['linkurl'];
			$r['parentid'] = $CATEGORY[$r['catid']]['parentid'] ? $CATEGORY[$r['catid']]['parentid'] : $r['catid'];
			$lists[] = $r;
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
		$this->db->query("INSERT INTO {$this->table_data} (itemid,content) VALUES ('$this->itemid', '$post[content]')");
		clear_upload($post['content'].$post['thumb'], $this->itemid);
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
	    $this->db->query("UPDATE {$this->table_data} SET content='$post[content]' WHERE itemid=$this->itemid");
		clear_upload($post['content'].$post['thumb'], $this->itemid);
		return true;
	}

	function update($itemid) {
		global $GENDER, $MARRIAGE, $EDUCATION;
		$item = $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid=$itemid");
		$update = '';
		$keyword = $item['title'].','.$item['truename'].','.$item['major'].','.strip_tags(cat_pos(get_cat($item['catid']), ',')).strip_tags(area_pos($item['areaid'], ',')).','.$item['skill'].','.$item['language'].','.$item['school'].','.$GENDER[$item['gender']].','.$MARRIAGE[$item['marriage']].','.$EDUCATION[$item['education']];
		if($keyword != $item['keyword']) {
			$keyword = str_replace("//", '', addslashes($keyword));
			$update .= ",keyword='$keyword'";
		}
		$linkurl = rewrite('resume.php?itemid='.$itemid);
		if($linkurl != $item['linkurl']) $update .= ",linkurl='$linkurl'";
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
				if($r['thumb']) delete_upload($r['thumb'], $userid);
				if($r['content']) delete_local($r['content'], $userid);
				$this->db->query("DELETE FROM {$this->table} WHERE itemid=$itemid");
				$this->db->query("DELETE FROM {$this->table_data} WHERE itemid=$itemid");
				if($r['username'] && $MOD['credit_del_resume']) {
					credit_add($r['username'], -$MOD['credit_del_resume']);
					credit_record($r['username'], -$MOD['credit_del_resume'], 'system', lang('my->credit_record_resume_del'), 'ID:'.$this->itemid);
				}
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
			if($MOD['credit_add'] && $item['username'] && $item['hits'] < 1) {
				credit_add($item['username'], $MOD['credit_add']);
				credit_record($item['username'], $MOD['credit_add'], 'system', lang('my->credit_record_resume_add'), 'ID:'.$this->itemid);
			}
			$this->db->query("UPDATE {$this->table} SET status=3,hits=hits+1,editor='$_username',edittime=$DT_TIME WHERE itemid=$itemid");
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
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$this->db->query("UPDATE {$this->table} SET edittime='$DT_TIME' WHERE itemid IN ($itemids)");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>