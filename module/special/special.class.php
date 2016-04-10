<?php
defined('IN_DESTOON') or exit('Access Denied');
class special {
	var $moduleid;
	var $itemid;
	var $db;
	var $table;
	var $table_data;
	var $split;
	var $fields;
	var $errmsg = errmsg;

    function special($moduleid) {
		global $db, $table, $table_data, $MOD;
		$this->moduleid = $moduleid;
		$this->table = $table;
		$this->table_data = $table_data;
		$this->split = '';
		$this->db = &$db;


		$this->fields = array('catid','islong','areaid','level','sortnum','code','discount','userid','title','style','introduce','thumb','banner','cfg_photo','cfg_video','cfg_type','seo_title','seo_keywords','seo_description','tag','status','hits','username','addtime', 'editor','edittime','ip','template','islink','linkurl','filepath','domain','note');
    }

	function pass($post) {
        global $MOD;
		if(!is_array($post)) return false;
		if(!is_length($post['title'],2,10)) return $this->_('标题输入有误');
		if(!is_url($post['thumb'])) return $this->_(lang('message->pass_thumb'));
		if(DT_MAX_LEN && strlen($post['content']) > DT_MAX_LEN) return $this->_(lang('message->pass_max'));

        $arrdiscount = explode('|', trim($MOD['discount']));
        if(!isset($post['discount']) || (!in_array($post['discount'],$arrdiscount) && $post['discount']!=10)){
            return $this->_('折扣选择有误');
        }



        if($this->itemid){
            if(isset($post['userid'])) unset($post['userid']);
            if(isset($post['code'])) unset($post['code']);
        }else{
            require_once DT_ROOT.'/module/member/member.class.php';
            $member_do = new member;
            if(!$aUser = $member_do->isCompany($post['username'])) return $this->_($member_do->errmsg);
            else $post['userid'] = $aUser['userid'];
            if(!$this->isexist($aUser['userid'])) return false;
            if(!preg_match('/^[0-9]{6}$/',$post['code'])) return $this->_('内容审核号输入有误');
            $info = $this->db->get_one("select itemid from {$this->table} where code = '{$post['code']}'");
            if($info) return $this->_('内容审核号已经存在');
        }




		return $post;
	}

	function set($post) {
		global $MOD, $DT_TIME, $DT_IP, $_username, $_userid;
		$post['edittime'] = $DT_TIME;

        $post['content'] = stripslashes($post['content']);
        $post['content'] = save_local($post['content']);
        if($post['clear_link']) $post['content'] = clear_link($post['content']);
        if($post['save_remotepic']) $post['content'] = save_remote($post['content']);
        if(strpos($post['content'], 'pagebreak') !== false) $post['content'] = str_replace(array('<hr class="de-pagebreak" /></p>', '<p><hr class="de-pagebreak" />', '<hr class="de-pagebreak" /></div>', '<div><hr class="de-pagebreak" />'), array('</p><hr class="de-pagebreak" />', '<hr class="de-pagebreak" /><p>', '</div><hr class="de-pagebreak" />', '<hr class="de-pagebreak" /><div>'), $post['content']);
        if($post['content'] && empty($post['introduce'])) $post['introduce'] = addslashes(get_intro($post['content'], isset($post['introduce_length']) && $post['introduce_length']?$post['introduce_length']:200));

        $sortnum = isset($post['sortnum'])?intval($post['sortnum']):1000;
        if($sortnum<1 || $sortnum>1000) $sortnum = 1000;
        $post['sortnum'] = $sortnum;
        if($this->itemid) {
			$post['editor'] = $_username;
			$new = $post['content'];
			if($post['thumb']) $new .= '<img src="'.$post['thumb'].'"/>';
			$r = $this->get_one();
			$old = $r['content'];
			if($r['thumb']) $old .= '<img src="'.$r['thumb'].'"/>';
			delete_diff($new, $old);
		} else {
            $post['addtime'] = $DT_TIME;
		}
		$content = $post['content'];
		unset($post['content']);
		$post = dhtmlspecialchars($post);
		$post['content'] = addslashes(dsafe($content));
		return array_map("trim", $post);
	}

	function get_one() {
		$r = $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid=$this->itemid");
		if($r) {
			$content_table = content_table($this->moduleid, $this->itemid, $this->split, $this->table_data);
			$t = $this->db->get_one("SELECT content FROM {$content_table} WHERE itemid=$this->itemid");
			$r['content'] = $t ? $t['content'] : '';
			return $r;
		} else {
			return array();
		}
	}

    function editHits(){
        $this->db->query("update {$this->table} set hits = hits + 1 where itemid = $this->itemid");
    }

    function getright($field='*',$limit,$order=''){
        $where = ' status=3 ';
        $list = array();
        $result = $this->db->query("select {$field} from {$this->table} where {$where} order by {$order} limit $limit");
        while($r=$this->db->fetch_array($result)){
            $list[] = $r;
        }
        return $list;
    }

    function checkSpecial($aInfo){
        if(!$aInfo){
            return $this->_('羊角会成员不存在');
        }
        if($aInfo['status']!=3){
            return $this->_('羊角会成员已下架');
        }
        return true;
    }

    function hornList($field='*',$condition='1=1',$order='addtime desc',$limit=20){
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

	function get_list($condition = 'status=3', $order = 'addtime DESC', $cache = '') {
		global $MOD, $pages, $page, $pagesize, $offset, $items, $sum,$mygetcount;
		if($page > 1 && $sum) {
			$items = $sum;
		} else if($mygetcount!==false){
			$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE $condition", $cache);
			$items = $r['num'];
		}

		$pages = defined('CATID') ? listpages(1, CATID, $items, $page, $pagesize, 10, $MOD['linkurl']) : pages($items, $page, $pagesize);
		if($items < 1 && $mygetcount!==false) return array();
		$lists = $catids = $CATS = array();
		$result = $this->db->query("SELECT * FROM {$this->table} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize", $cache);
		while($r = $this->db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['alt'] = $r['title'];
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

    function get_applylist($condition = 'status=3', $order = 'addtime DESC', $cache = '') {
        global $MOD, $pages, $page, $pagesize, $offset, $items, $sum;
        if($page > 1 && $sum) {
            $items = $sum;
        } else{
            $r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->db->pre}apply WHERE $condition", $cache);
            $items = $r['num'];
        }

        $pages = defined('CATID') ? listpages(1, CATID, $items, $page, $pagesize, 10, $MOD['linkurl']) : pages($items, $page, $pagesize);
        if($items < 1) return array();
        $lists = $catids = $CATS = array();
        $result = $this->db->query("SELECT * FROM {$this->db->pre}apply WHERE $condition ORDER BY $order LIMIT $offset,$pagesize", $cache);
        while($r = $this->db->fetch_array($result)) {
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
		$content_table = content_table($this->moduleid, $this->itemid, $this->split, $this->table_data);
		$this->db->query("REPLACE INTO {$content_table} (itemid,content) VALUES ('$this->itemid', '$post[content]')");
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
		$content_table = content_table($this->moduleid, $this->itemid, $this->split, $this->table_data);
		$this->db->query("REPLACE INTO {$content_table} (itemid,content) VALUES ('$this->itemid', '$post[content]')");
		clear_upload($post['content'].$post['thumb'], $this->itemid);
		return true;
	}

	function tohtml($itemid = 0, $catid = 0) {
		global $module, $MOD;
		if($MOD['show_html'] && $itemid) tohtml('show', $module, "itemid=$itemid");
	}

	function update($itemid) {
		$item = $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid=$itemid");
		$update = '';
		$keyword = $item['title'].','.strip_tags(cat_pos(get_cat($item['catid']), ','));
		if($keyword != $item['keyword']) {
			$keyword = str_replace("//", '', addslashes($keyword));
			$update .= ",keyword='$keyword'";
		}
		$item['itemid'] = $itemid;
		$linkurl = (isset($item['domain']) && $item['domain']) ? $item['domain'] : ($item['islink'] ? $item['linkurl'] : itemurl($item));
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
		global $MOD, $DT_PRE;
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
				$this->db->query("DELETE FROM {$DT_PRE}special_item WHERE specialid=$itemid");
				$this->db->query("DELETE FROM {$DT_PRE}type WHERE item='special-".$itemid."'");
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

	function _($e) {
		$this->errmsg = $e;
		return false;
	}

    /**
     *  收费
     */
    function money($info,$codeinfo,$type,$itemid){
        global $DT,$_userid,$DT_TIME;


        $note = '发布';
        $fee = $this->fee($type);

        /*if($istop){  //置顶
            $fee +=  doubleval($DT['stickfee'])*100;
            $note .= ',置顶';
        }*/

        list($allmoney,$discountfee,$feedfs) = $this->moneyji($fee,$codeinfo['discount']);

        //保存记录
        $this->db->query("insert into {$this->db->pre}code (code,type,userid,id,addtime,codeid,discount,allmoney,money,discountfee,note,status) values ('{$codeinfo['code']}',$type,$_userid,$itemid,$DT_TIME,{$codeinfo['itemid']},".doubleval($codeinfo['discount']).",$allmoney,$feedfs,$discountfee,'$note',2)");


        $fee = round($fee/100,2);                                //付费的钱
        return array($fee,$discountfee);

    }

    function addCodeEdit($codeid){
        //使用量+1
        $this->db->query("update {$this->db->pre}special set userds = userds+1 where itemid = ".$codeid);
    }

    function codeStatus($itemid,$type){
        global $DT_TIME;
        $info = $this->db->get_one("select itemid,codeid from {$this->db->pre}code where id = ".$itemid." and type = ".$type." and status = 2");
        if($info){
            $this->db->query("update {$this->db->pre}code set errormsg='审核通过',status = 3,checktime=$DT_TIME,ym=".date('Ym',$DT_TIME)." where itemid=".$info['itemid']);
            $this->addCodeEdit($info['codeid']);
            $this->db->query("insert into {$this->db->pre}checkcode (codeid,status,note,addtime) values ({$info['itemid']},1,'审核通过',$DT_TIME)");
        }
    }

    function getShowCode($itemid,$type){
        global $DT_TIME;
        $info = $this->db->get_one("select code from {$this->db->pre}code where id = ".$itemid." and type = ".$type."");
        return $info?$info['code']:'无';
    }



    function fee($type){
        $modulearr = array(23,13,9,5,10,6,21);
        $sn = cache_read('module-'.$modulearr[$type].'.php');
        $fee =  doubleval($sn['publishfee'])*100;
        return $fee;
    }

    function moneyji($fee,$discount){
        $discount = doubleval($discount);
        $oldfee = $fee;
        $fee = ($fee*($discount*100))/1000;
        $feedfs = round($fee/100,2);
        $discountfee = ($oldfee/100)-$feedfs;  //折扣的钱
        $allmoney = $oldfee/100;
        return array($allmoney,$discountfee,$feedfs);   //原价,折扣的钱,优惠价
    }

    /**
     * 检测编号
     */
    function codeCheck($code){
        if(!preg_match('/^[0-9]{6}$/',$code)) return $this->_('内容审核号输入有误');
        $info = $this->db->get_one("select * from {$this->db->pre}special where code = '{$code}' and status = 3");
        if(!$info) return $this->_('内容审核号不存在');
        return $info;
    }

    function get_userlist($condition = '1=1', $order = 'addtime DESC', $cache = '') {
        global $MOD, $pages, $page, $pagesize, $offset, $items, $sum,$mygetcount;
        if($page > 1 && $sum) {
            $items = $sum;
        } else if($mygetcount!==false){
            $r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->db->pre}code WHERE $condition", $cache);
            $items = $r['num'];
        }
        $pages = defined('CATID') ? listpages(1, CATID, $items, $page, $pagesize, 10, $MOD['linkurl']) : pages($items, $page, $pagesize);
        if($items < 1 && $mygetcount!==false) return array();
        $lists = $catids = $CATS = array();
        $result = $this->db->query("SELECT * FROM {$this->db->pre}code WHERE $condition ORDER BY $order LIMIT $offset,$pagesize", $cache);
        while($r = $this->db->fetch_array($result)) {
            $lists[] = $r;
        }
        return $lists;
    }

    function codeAll($where){
        $info = $this->db->get_one("select sum(allmoney) as allmoney,sum(money) as money,sum(discountfee) as discountfee from {$this->db->pre}code where {$where}");
        return $info;
    }

    function codeList($condition='1=1',$limit=10){
        global $page;
        $offset = ($page-1)*$limit;
        $result = $this->db->query("select c.*,cc.status as cc_status,cc.addtime as cc_addtime,cc.note as cc_note from {$this->db->pre}checkcode cc inner join {$this->db->pre}code c on c.itemid = cc.codeid where $condition order by cc.itemid desc limit $offset,$limit");
        $list = array();
        while($r = $this->db->fetch_array($result)){
            $r['typename'] = $this->typearr($r['type']);
            $list[] = $r;
        }
        $count = $this->db->get_one("select count(*) as num from {$this->db->pre}checkcode cc inner join {$this->db->pre}code c on c.itemid = cc.codeid where $condition");
        $totalpage = ceil($count['num']/$limit);
        return array($list,$totalpage);
    }

    function codenum($codeid){
        global $page;
        $condition = ' c.codeid = '.$codeid;
        $count = $this->db->get_one("select count(*) as num from {$this->db->pre}checkcode cc inner join {$this->db->pre}code c on c.itemid = cc.codeid where $condition");
        return $count['num']?$count['num']:0;
    }

    function codeListApply($condition='1=1',$limit=10){
        global $page;
        $offset = ($page-1)*$limit;
        $result = $this->db->query("select * from {$this->db->pre}code  where $condition order by itemid desc limit $offset,$limit");
        $list = array();
        while($r = $this->db->fetch_array($result)){
            $r['typename'] = $this->typearr($r['type']);
            $list[] = $r;
        }
        $count = $this->db->get_one("select count(*) as num from {$this->db->pre}code where $condition");
        $totalpage = ceil($count['num']/$limit);
        return array($list,$totalpage);
    }

    function codeListApplyList($condition='1=1',$limit=10){
        global $page;
        $offset = ($page-1)*$limit;
        $result = $this->db->query("select * from {$this->db->pre}apply where $condition order by itemid desc limit $offset,$limit");
        $list = array();
        while($r = $this->db->fetch_array($result)){
            $list[] = $r;
        }
        $count = $this->db->get_one("select count(*) as num from {$this->db->pre}apply where $condition");
        $totalpage = ceil($count['num']/$limit);
        return array($list,$totalpage);
    }

    function codeListGroup($condition='1=1',$limit=10){
        global $page;
        $offset = ($page-1)*$limit;
        $result = $this->db->query("select sum(allmoney) as allmoney,sum(money) as money,sum(discountfee) as discountfee,ym,checktime from {$this->db->pre}code where $condition  group by ym order by checktime desc limit $offset,$limit");
        $list = array();
        while($r = $this->db->fetch_array($result)){
            $list[] = $r;
        }
        $count = $this->db->get_one("select count(*) as num from (select itemid from {$this->db->pre}code as m  where $condition group by ym ) {$this->db->pre}code");
        $totalpage = ceil($count['num']/$limit);
        return array($list,$totalpage);
    }



    function codetable($checktype){
        $table_arr = array('food_23','brand_13','job','sell_5','know','buy_6','article_21');
        $table = isset($table_arr[$checktype])?$table_arr[$checktype]:$table_arr[0];
        return $table;
    }

    function canCheck($checktype,$codeid,$itemid,$status,$errormsg){
        global $DT_TIME;
        $table = $this->codetable($checktype);
        if($status==0 && !is_length($errormsg,1,50)){
            return $this->_('请填写不通过原因');
        }
        $info = $this->db->get_one("select itemid,userid from {$this->db->pre}{$table} where itemid = ".$itemid." and status = 2");

        $code = $this->db->get_one("select itemid from {$this->db->pre}code where codeid = ".$codeid." and status = 2 and id = ".$itemid);
        if($info && $code) {
            $checkcodestatus = $status;
            $status = $status ? 3 : 1;
            $codestatus = $status==3 ? 3 : 2;
            if($checkcodestatus==1) $errormsg = '审核通过';
            $this->db->query("update {$this->db->pre}{$table} set status = {$status} where itemid = " . $itemid);
            $this->db->query("update {$this->db->pre}code set errormsg='".$errormsg."',status={$codestatus},checktime = {$DT_TIME},ym=".date('Ym',$DT_TIME)." where codeid = " . $codeid . "  and id = " . $itemid);
            if($status==3) $this->addCodeEdit($codeid);
            if($checktype==4 && $status==3){
                 //将会员变为厨师
                require_once DT_ROOT.'/module/know/know.class.php';
                $oCai = new know();
                $oCai->setChu($info['userid']);
            }

            //加入审核日志
            $this->db->query("insert into {$this->db->pre}checkcode (codeid,status,note,addtime) values ({$code['itemid']},$checkcodestatus,'$errormsg',$DT_TIME)");
            return true;
        }else{
            return $this->_('该信息不能审核，请刷新页面重试');
        }
    }

    function codeListTablenum($checktype,$codeid){
        global $page;
        $table = $this->codetable($checktype);
        $where = " c.codeid = {$codeid} and c.type = {$checktype} and c.status = 2 and t.status = 2 ";
        $count = $this->db->get_one("select count(*) as num from {$this->db->pre}code c inner join {$this->db->pre}{$table} t on t.itemid = c.id where {$where}");
        return $count['num']?$count['num']:0;
    }

    function codenewno($codeid){
        global $num0,$num1,$num2,$num3,$num4,$num5,$num6;
        $num0 = $this->codeListTablenum(0,$codeid);
        $num1 = $this->codeListTablenum(1,$codeid);
        $num2 = $this->codeListTablenum(2,$codeid);
        $num3 = $this->codeListTablenum(3,$codeid);
        $num4 = $this->codeListTablenum(4,$codeid);
        $num5 = $this->codeListTablenum(5,$codeid);
        $num6 = $this->codeListTablenum(6,$codeid);
        return ($num0+$num1+$num2+$num3+$num4+$num5+$num6);
    }

    function applynum($codeid){
        global $page;
        $count = $this->db->get_one("select sum(discountfee) as discountfee from {$this->db->pre}code where codeid = $codeid and status = 3 and is_apply = 0");
        return $count['discountfee']?$count['discountfee']:0;
    }

    function applymoney($codeid,$userid){
        global $DT_TIME;
        $count = $this->db->get_one("select sum(discountfee) as discountfee,GROUP_CONCAT(itemid) AS codeidstr from {$this->db->pre}code where codeid = $codeid and status = 3 and is_apply = 0");
        $countfee = $count['discountfee'];
        $codeidstr = $count['codeidstr'];
        $this->db->query("insert into {$this->db->pre}apply (money,codeid,addtime,status,codeidstr,userid) values ($countfee,$codeid,$DT_TIME,0,'{$codeidstr}',$userid)");
        $this->db->query("update {$this->db->pre}code set is_apply = 1 where codeid = $codeid and status = 3 and is_apply = 0");
        return true;
    }

    function codeListTable($checktype,$codeid,$type='pc',$limit=10){
        global $page;
        $table = $this->codetable($checktype);
        $offset = ($page-1)*$limit;
        $where = " c.codeid = {$codeid} and c.type = {$checktype} and c.status = 2 and t.status = 2 ";
        $result = $this->db->query("select c.*,t.itemid as t_itemid,t.title as t_title from {$this->db->pre}code c inner join {$this->db->pre}{$table} t on t.itemid = c.id where {$where} order by c.addtime desc limit $offset,$limit");
        $list = array();
        while($r = $this->db->fetch_array($result)){
            $r['typename'] = $this->typearr($r['type']);
            $r['url'] = $this->urlarr($r['type'],$r['t_itemid'],$type);
            $list[] = $r;
        }
        $count = $this->db->get_one("select count(*) as num from {$this->db->pre}code c inner join {$this->db->pre}{$table} t on t.itemid = c.id where {$where}");
        $totalpage = ceil($count['num']/$limit);
        return array($list,$totalpage);
    }

    function typearr($type){
        $arr = array('餐饮供应','优惠价格','招聘信息','店铺转让','名厨学堂','分享','菜系');
        return $arr[$type];
    }

    function urlarr($type,$id,$typ1e='pc'){
        if($typ1e=='pc'){
            $arr = array(
                '/food/show.php?id='.$id.'&showtype=preview',
                '/discount/show.php?id='.$id.'&showtype=preview',
                '/job/show.php?id='.$id.'&showtype=preview',
                '/cession/show.php?id='.$id.'&showtype=preview',
                '/school/food.php?id='.$id.'&showtype=preview',
                '/share/show.php?id='.$id.'&showtype=preview',
                '/article/show.php?id='.$id.'&showtype=preview',
            );
        }else if($typ1e=='wap'){
            $arr = array(
                '/mobile/food/show.php?id='.$id.'&showtype=preview',
                '/mobile/discount/show.php?id='.$id.'&showtype=preview',
                '/mobile/job/show.php?id='.$id.'&showtype=preview',
                '/mobile/cession/show.php?id='.$id.'&showtype=preview',
                '/mobile/school/foodshow.php?id='.$id.'&showtype=preview',
                '/mobile/share/show.php?id='.$id.'&showtype=preview',
                '/mobile/article/show.php?id='.$id.'&showtype=preview',
            );
        }

        return $arr[$type];
    }

    function checkHasUser(){
        global $_userid;
        if(!$_userid) return false;
        $result = $this->db->get_one("select * from {$this->table} where userid={$_userid} and status = 3");
        if(!$result) return $this->_('您不是羊角会成员');
        return $result;
    }

    //是否已经存在  无论是什么状态都不能添加
    function isexist($userid){
        $result = $this->db->get_one("select * from {$this->table} where userid={$userid}");
        if($result) return $this->_('该会员已经是羊角会会员，不能再添加');
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