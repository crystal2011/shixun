<?php 
defined('IN_DESTOON') or exit('Access Denied');
class comment {
	var $itemid;
	var $db;
	var $table;
	var $table_stat;
	var $table_ban;
	var $errmsg = errmsg;

    function comment() {
		global $db;
		$this->table = $db->pre.'comment';
		$this->table_stat = $db->pre.'comment_stat';
		$this->table_ban = $db->pre.'comment_ban';
		$this->db = &$db;
    }

	function pass($post) {
		global $L;
		if(!is_array($post)) return false;
		if(!$post['content']) return $this->_($L['comment_pass_content']);
		return true;
	}

	function set($post) {
		global $DT_TIME, $_username;
		$post['hidden'] = isset($post['hidden']) ? 1 : 0;
		$post['status'] = $post['status'] == 3 ? 3 : 2;
		$post['star'] = intval($post['star']);
		in_array($post['star'], array(1, 2, 3)) or $post['star'] = 3;
		if($post['reply']) {
			$post['replytime'] = $DT_TIME;
			$post['reply'] = trim($post['reply']);
		}
		$post['editor'] = $_username;
		return array_map("trim", $post);
	}

	function get_one() {
        return $this->db->get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid'");
	}

	function get_list($condition = 'status=3', $order = 'itemid DESC') {
		global $MOD, $TYPE, $pages, $page, $pagesize, $offset, $items;
		$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE $condition");
		$items = $r['num'];
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();		
		$lists = array();
		$result = $this->db->query("SELECT * FROM {$this->table} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
		while($r = $this->db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 6);
			$r['replydate'] = $r['replytime'] ? timetodate($r['replytime'], 6) : '';
			$lists[] = $r;
		}
		return $lists;
	}

	function edit($post) {
		$post = $this->set($post);
		$r = $this->get_one();
		if($r['star'] != $post['star']) {
			$star = 'star'.$r['star'];
			$this->db->query("UPDATE {$this->table_stat} SET `{$star}`=`{$star}`-1 WHERE itemid=$r[item_id] AND moduleid=$r[item_mid]");
			$star = 'star'.$post['star'];
			$this->db->query("UPDATE {$this->table_stat} SET `{$star}`=`{$star}`+1 WHERE itemid=$r[item_id] AND moduleid=$r[item_mid]");
		}
		$sql = '';
		foreach($post as $k=>$v) {
			$sql .= ",$k='$v'";
		}
        $sql = substr($sql, 1);
	    $this->db->query("UPDATE {$this->table} SET $sql WHERE itemid=$this->itemid");
		return true;
	}

	function delete($itemid) {
		global $MOD, $L;
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->delete($v); 
			}
		} else {
			$this->itemid = $itemid;
			$r = $this->get_one();
			if($r) {
				$star = 'star'.$r['star'];
				$this->db->query("UPDATE {$this->table_stat} SET comment=comment-1,`{$star}`=`{$star}`-1 WHERE itemid=$r[item_id] AND moduleid=$r[item_mid]");
				$this->db->query("DELETE FROM {$this->table} WHERE itemid=$itemid");
				if($r['username'] && $MOD['credit_del_comment']) {
					credit_add($r['username'], -$MOD['credit_del_comment']);
					credit_record($r['username'], -$MOD['credit_del_comment'], 'system', $L['comment_record_del'], 'ID:'.$r['itemid']);
				}
			}
		}
	}

	function check($itemid, $status = 3) {
		global $MOD, $L;
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->check($v, $status); 
			}
		} else {
            $this->itemid = $itemid;
            $info = $this->get_one();
            switch($info['type']) {
                case 1: //餐饮供应
                    require_once DT_ROOT.'/module/food/food.class.php';
                    $obj = new food(23);
                    break;
                case 2: //商家优惠
                    require_once DT_ROOT.'/module/brand/brand.class.php';
                    $obj = new brand(13);
                    break;
                case 3: //招聘信息
                    require_once DT_ROOT.'/module/job/job.class.php';
                    $obj = new job(9);
                    break;
                case 4: //招聘信息
                    require_once DT_ROOT.'/module/sell/sell.class.php';
                    $obj = new sell(5);
                    break;
                case 5: //名厨学堂 - 菜系
                    require_once DT_ROOT.'/module/know/konw.class.php';
                    $obj = new konw;
                    break;
                case 6: //分享
                    require_once DT_ROOT.'/module/buy/buy.class.php';
                    $obj = new buy(6);
                case 7: //羊角会
                    require_once DT_ROOT.'/module/info/info.class.php';
                    $obj = new info(24);
                    $obj->table = $this->db->pre.'info_24';
                    $obj->table_data = $this->db->pre.'info_data_24';
                    break;
                case 8: //羊角会
                    require_once DT_ROOT.'/module/special/special.class.php';
                    $obj = new special(11);
                    $obj->table = $this->db->pre.'special';
                    $obj->table_data = $this->db->pre.'special_data';
                    break;
                case 9://文章
                    require_once DT_ROOT.'/module/article/article.class.php';
                    $obj = new article(21);
                    break;
                default:
                    exit(json_encode(array('status'=>'n','info'=>'操作失误')));
                    break;
            }
            $obj->itemid = $info['id'];
            $obj->adddelComments($status==3?'add':'del');
			$this->db->query("UPDATE {$this->table} SET status=$status WHERE itemid=$itemid");
		}
	}

	function get_ban_list($condition = '1') {
		global $pages, $page, $pagesize, $offset, $pagesize, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table_ban} WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $this->db->query("SELECT * FROM {$this->table_ban} WHERE $condition ORDER BY bid DESC LIMIT $offset,$pagesize");
		while($r = $this->db->fetch_array($result)) {
			$r['edittime'] = timetodate($r['edittime'], 6);
			$lists[] = $r;
		}
		return $lists;
	}

	function ban_update($post) {
		$this->_add($post[0]);
		unset($post[0]);
		foreach($post as $k=>$v) {
			if(isset($v['delete'])) {
				$this->_delete($k);
				unset($post[$k]);
			}
		}
		$this->_edit($post);
		cache_bancomment();
		return true;
	}

	function _add($post) {
		global $DT_TIME, $_username;
		$post['moduleid'] = intval($post['moduleid']);
		$post['itemid'] = intval($post['itemid']);
		if(!$post['moduleid'] || !$post['itemid']) return false;
		$this->db->query("INSERT INTO {$this->table_ban} (moduleid,itemid,editor,edittime) VALUES('$post[moduleid]','$post[itemid]','$_username','$DT_TIME')");
	}

	function _edit($post) {
		foreach($post as $k=>$v) {
			$v['moduleid'] = intval($v['moduleid']);
			$v['itemid'] = intval($v['itemid']);
			if(!$v['moduleid'] || !$v['itemid']) return false;
			$this->db->query("UPDATE {$this->table_ban} SET moduleid='$v[moduleid]',itemid='$v[itemid]' WHERE bid='$k'");
		}
	}

	function _delete($bid) {
		$this->db->query("DELETE FROM {$this->table_ban} WHERE bid=$bid");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}

    /**
     * 是否已经评论  够期限了吗
     */
    function isCommentd($id,$type){
        global $DT_IP,$DT_TIME,$_userid;
        $aComment = $this->db->get_one("select addtime from {$this->table} where userid=".$_userid." and type = ".$type." and id = ".$id);
        if($aComment && $DT_TIME-$aComment['addtime']<=3600){
            return $this->_('您刚发表评论，请稍后再续...');
        }
        return true;
    }

    function addComment($id,$type,$content){
        global $_userid,$DT_IP,$DT_TIME,$DT;
        $this->db->query("insert into {$this->table} (id,type,userid,ip,addtime,status,content) values ($id,$type,$_userid,'$DT_IP',$DT_TIME,".($DT['commentscheck']?3:2).",'$content')");
        return true;
    }

    function commentList($id,$type,$page=1){
        $limit = 10;
        $slimit = $limit+1;
        $offset = ($page-1)*$limit;
        $result = $this->db->query("select c.content,m.thumb from {$this->table} c inner join {$this->db->pre}member m on m.userid = c.userid and m.groupid = 5 where c.type = ".$type." and c.id = ".$id." and c.status = 3 order by addtime desc limit $offset,$slimit");
        $arr = array();
        while($r = $this->db->fetch_array($result)){
            $arr[] = $r;
        }
        $HasNextPage = false;
        if(count($arr)==$slimit){
            unset($arr[$slimit-1]);
            $HasNextPage = true;
        }
        return array($arr,$HasNextPage);
    }
}
?>