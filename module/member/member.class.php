<?php
defined('IN_DESTOON') or exit('Access Denied');
class member {
    var $userid;
    var $username;
    var $db;
    var $table_member;
    var $table_member_check;
    var $table_company;
    var $table_company_data;
    var $errmsg = errmsg;
    var $falt = '';
    function member() {
        global $db;
        $this->table_member = $db->pre.'member';
        $this->table_member_check = $db->pre.'member_check';
        $this->table_company = $db->pre.'company';
        $this->table_company_data = $db->pre.'company_data';
        $this->db = &$db;
        $this->falt = 'shixun';
    }

    function editHits(){
        $this->db->query("update {$this->table_member} set hits = hits + 1 where userid = $this->userid");
    }

    function checkKnow($aKnow){
        if(!$aKnow){
            return $this->_('名厨不存在');
        }
        if($aKnow['groupid']!=5){
            return $this->_('名厨不存在');
        }
        if($aKnow['ischu']!=1){
            return $this->_('会员未成为名厨');
        }
        return true;
    }

    function getright($field='*',$limit,$order=''){
        global $dtcity;
        $where = ' ischu=1 and groupid=5 ';
        if($dtcity){
            $info = get_area($dtcity['areaid']);
            $arrchildid = $info['arrchildid'];
            $where .= " and areaid in ($arrchildid)";
        }
        $list = array();
        $result = $this->db->query("select {$field} from {$this->table_member} where {$where} order by {$order} limit $limit");
        while($r=$this->db->fetch_array($result)){
            $list[] = $r;
        }
        return $list;
    }

    function memberList($field='*',$condition='1=1',$order='hits desc',$limit=10){
        global $page;
        $offset = ($page-1)*$limit;
        $result = $this->db->query("select {$field} from {$this->table_member} where $condition  order by $order limit $offset,$limit");
        $list = array();
        while($r = $this->db->fetch_array($result)){
            $list[] = $r;
        }
        $count = $this->db->get_one("select count(*) as num from {$this->table_member} where $condition");
        $totalpage = ceil($count['num']/$limit);
        return array($list,$totalpage);
    }

    function is_email($email) {
        global $MOD, $L;
        $email = trim($email);
        if(!is_email($email)) return $this->_($L['member_email_null']);
        if($MOD['banemail']) {
            $domain = substr(strstr($email, '@'), 1);
            $tmp = explode('|', $MOD['banemail']);
            foreach($tmp as $v) {
                if($domain == $v) return $this->_($L['member_email_ban']);
            }
        }
        return true;
    }

    /**
     * 密码检测
     * @param $password
     * @param $cpassword
     * @return bool
     */
    function is_password($password, $cpassword) {
        global $MOD, $L;
        if(!$password) return $this->_($L['member_password_null']);
        if($password != $cpassword) return $this->_($L['member_password_match']);
        if(!$MOD['minpassword']) $MOD['minpassword'] = 6;
        if(!$MOD['maxpassword']) $MOD['maxpassword'] = 20;
        $minpassword = $MOD['minpassword'];
        $maxpassword = $MOD['maxpassword'];
        if(!preg_match('/^[a-zA-Z0-9]{'.$minpassword.','.$maxpassword.'}$/',$password)) return $this->_(lang($L['member_password_len'], array($MOD['minpassword'], $MOD['maxpassword'])));
        return true;
    }

    function is_payword($password, $cpassword) {
        global $MOD, $L;
        if(!$password) return $this->_($L['member_payword_null']);
        if($password != $cpassword) return $this->_($L['member_payword_match']);
        if(!$MOD['minpassword']) $MOD['minpassword'] = 6;
        if(!$MOD['maxpassword']) $MOD['maxpassword'] = 20;
        if(strlen($password) < $MOD['minpassword'] || strlen($password) > $MOD['maxpassword']) return $this->_(lang($L['member_payword_len'], array($MOD['minpassword'], $MOD['maxpassword'])));
        return true;
    }

    function is_clean($string) {
        $chars = array("\\", "'",'"','/','<','>',"\r","\t","\n");
        foreach($chars as $v) {
            if(strpos($string, $v) !== false) return false;
        }
        return true;
    }

    function is_member($member) {
        global $L, $AREA;
        if(!is_array($member)) return false;
        if($this->userid) {
            if($member['password'] && !$this->is_password($member['password'], $member['cpassword'])) return false;
        } else {
            if(!$this->is_password($member['password'], $member['cpassword'])) return $this->_('密码输入有误');
            if(!$this->is_username($member['username'])) return false;
            if($this->mobile_exists($member['mobile'])) return $this->_('手机号已存在');
        }
        return true;
    }

    function set_member($member) {
        global $MOD,$DT_TIME,$DT_IP;
        if(isset($member['password'])) md5(md5($member['password']).$this->falt);
        if($this->userid) {
            $member['edittime'] = $DT_TIME;
        } else{
            $member['groupid'] = $member['regid'] = 5;
            $member['regip'] = $DT_IP;
            $member['regtime'] = $DT_TIME;
            $member['password'] = md5(md5($member['password']));
        }
        return $member;
    }

    function email_exists($email) {
        $condition = "email='$email'";
        if($this->userid) $condition .= " AND userid!=$this->userid";
        return $this->db->get_one("SELECT userid FROM {$this->table_member} WHERE {$condition}");
    }

    /**
     * 检测手机是否存在
     * @param $mobile
     * @return mixed
     */
    function mobile_exists($mobile) {
        $condition = "mobile='$mobile'";
        if($this->userid) $condition .= " AND userid!=$this->userid";
        return $this->db->get_one("SELECT userid FROM {$this->table_member} WHERE {$condition}");
    }

    function company_exists($company) {
        $condition = "company='$company'";
        if($this->userid) $condition .= " AND userid!=$this->userid";
        return $this->db->get_one("SELECT userid FROM {$this->table_company} WHERE {$condition}");
    }

    function add($member) {
        global $DT, $DT_TIME, $DT_IP, $MOD, $L;
        if(!$this->is_member($member)) return false;
        $member = $this->set_member($member);
        $member_fields = array('username','password','mobile','groupid','regid','regip','regtime');
        $member_sqlk = $member_sqlv = '';
        foreach($member as $k=>$v) {
            if(in_array($k, $member_fields)) {$member_sqlk .= ','.$k; $member_sqlv .= ",'$v'";}
        }
        $member_sqlk = substr($member_sqlk, 1);
        $member_sqlv = substr($member_sqlv, 1);
        $this->db->query("INSERT INTO {$this->table_member} ($member_sqlk,email)  VALUES ($member_sqlv,NULL)");
        $this->userid = $this->db->insert_id();
        if(!$this->userid) return 0;
        return $this->userid;
    }

    function findpw($member)
    {
        global $DT, $DT_TIME, $DT_IP, $MOD, $L;
        if(!$this->is_password($member['password'], $member['cpassword'])) return $this->_('密码输入有误');
        if(!$userinfo = $this->mobile_exists($member['mobile'])) return $this->_('手机号不存在');
        $member['edittime'] = $DT_TIME;
        $member['password'] = md5(md5($member['password']));
        $member_fields = array('password', 'edittime');
        $member_sql = '';
        foreach($member as $k=>$v) {
            if(in_array($k, $member_fields)) $member_sql .= ",$k='$v'";
        }
        $member_sql = substr($member_sql, 1);
        $this->db->query("UPDATE {$this->table_member} SET $member_sql WHERE userid=".$userinfo['userid']);
        return true;
    }

    function edit($member)	{
        if(!$this->is_member($member)) return false;
        $member = $this->set_member($member);
        $r = $this->get_one();
        $member_fields = array('company','passport','sound','email','msn','qq','ali','skype','gender','truename','mobile','department','career','groupid','areaid', 'edittime','black','bank','banktype','branch','account','vemail','vmobile','vbank','vtruename','vcompany','vtrade','trade','support','inviter');
        $company_fields = array('company','type','areaid', 'catid','catids','business','mode','regyear','regunit','capital','size','address','postcode','telephone','fax','mail','homepage','sell','buy','introduce','thumb','keyword','linkurl','groupid','domain','icp','validated','validator','validtime','skin','template');
        $member_sql = $company_sql = '';
        foreach($member as $k=>$v) {
            if(in_array($k, $member_fields)) $member_sql .= ",$k='$v'";
            if(in_array($k, $company_fields)) $company_sql .= ",$k='$v'";
        }
        if($member['password']) {
            $passsalt = random(8);
            $password = dpassword($member['password'], $passsalt);
            $member_sql .= ",password='$password',passsalt='$passsalt'";
        }
        if($member['payword']) {
            $paysalt = random(8);
            $payword = dpassword($member['payword'], $paysalt);
            $member_sql .= ",payword='$payword',paysalt='$paysalt'";
        }
        $member_sql = substr($member_sql, 1);
        $company_sql = substr($company_sql, 1);
        $this->db->query("UPDATE {$this->table_member} SET $member_sql WHERE userid=$this->userid");
        $this->db->query("UPDATE {$this->table_company} SET $company_sql WHERE userid=$this->userid");
        $content_table = content_table(4, $this->userid, is_file(DT_CACHE.'/4.part'), $this->table_company_data);
        $this->db->query("UPDATE {$content_table} SET content='$member[content]' WHERE userid=$this->userid");
        $member['userid'] = $this->userid;
        $member['vip'] = $r['vip'];
        userclean($member['username']);
        return true;
    }

    function get_one($condition='',$field='*') {
        $condition = $condition?$condition:"userid='$this->userid'";
        return $this->db->get_one("SELECT $field FROM {$this->table_member} where $condition");
    }

    function get_list($condition, $order = 'userid DESC') {
        global $pages, $page, $pagesize, $offset, $sum,$items,$mygetcount;
        if($page > 1 && $sum) {
            $items = $sum;
        } else if($mygetcount!==false){
            $r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table_member} WHERE $condition");
            $items = $r['num'];
        }
        $pages = pages($items, $page, $pagesize);
        if($items < 1 && $mygetcount!==false) return array();
        $members = array();
        $result = $this->db->query("SELECT * FROM {$this->table_member} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize");
        while($r = $this->db->fetch_array($result)) {
            $r['logindate'] = timetodate($r['logintime'], 5);
            $r['regdate'] = timetodate($r['regtime'], 5);
            $members[] = $r;
        }
        return $members;
    }

    function login($login_username, $login_password, $login_cookietime = 0, $admin = false) {
        global $DT_TIME, $DT_IP, $MOD, $MODULE, $L,$DT_PRE,$session;

        $session or $session = new dsession();
        if(!$MOD || !isset($MOD['login_times'])) $MOD = cache_read('module-2.php');  //登录次数限制
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


        if(strlen($login_username)<6) return $this->_('用户名输入有误');
        if(!$this->is_password($login_password,$login_password)) return false;
        $option = is_mobile($login_username)?'mobile':'username';
        $user = $this->db->get_one("SELECT * FROM {$DT_PRE}member WHERE `$option`='$login_username' and groupid = 5");
        if(!$user) {
            $this->lock($login_lock, $LOCK, $DT_IP, $DT_TIME);
            return $this->_('用户不存在');
        }

        if($user['password'] != md5(md5($login_password))) {
            $this->lock($login_lock, $LOCK, $DT_IP, $DT_TIME);
            return $this->_($L['member_login_password_bad']);
        }


        $auth = encrypt($user['userid'].'|'.$user['password'], DT_KEY.'USER');
        set_cookie('auth', $auth, $login_cookietime?$DT_TIME+86400*14:0);
        $_SESSION['auth'] = $auth;

        $this->db->query("UPDATE {$this->table_member} SET loginip='$DT_IP',logintime=$DT_TIME,logintimes=logintimes+1 WHERE userid=".$user['userid']);
        $this->bind($user['username'], $DT_TIME);
        return $user;
    }

    function cart($userid) {//SYNC
        global $DT_TIME;
        $r = $this->db->get_one("SELECT data FROM {$this->db->pre}mall_cart WHERE userid=$userid");
        if($r && $r['data']) {
            $cart = unserialize($r['data']);
            set_cookie('cart', count($cart), $DT_TIME + 30*86400);
        }
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

    function lock($login_lock, $LOCK, $DT_IP, $DT_TIME) {
        if($login_lock && $DT_IP) {
            $LOCK['time'] = $DT_TIME;
            $LOCK['times'] = isset($LOCK['times']) ? $LOCK['times'] + 1 : 1;
            cache_write($DT_IP.'.php', $LOCK, 'ban');
        }
    }

    function logout() {
        global $_userid;
        $this->db->query("DELETE FROM {$this->db->pre}online WHERE userid=$_userid");
        set_cookie('auth', '');
        set_cookie('userid', '');
        return true;
    }

    /**
     * 会员删除  假删除   将会员旗下的信息都改为无效   若会员恢复后将会员旗下的信息都改为原来状态
     * @param $userid
     * @return bool
     *
     */
    function delete($userid) {
        global $dc, $CFG, $MODULE, $L;
        if(!$userid) return false;
        if(is_array($userid)) {
            $userids = implode(',', $userid);
        } else {
            $userids = intval($userid);
        }
        $result = $this->db->query("SELECT username,userid,groupid FROM {$this->table_member} WHERE userid IN ($userids) and groupid !=3 ");
        while($r = $this->db->fetch_array($result)) {
            $userid = $r['userid'];
            if(!$userid) continue;
            $this->db->query("update {$this->table_member} set groupid = 3,oldgroupid=".$r['groupid']." WHERE userid = $userid ");
            $this->offStatus($userid);
        }
        return true;
    }

    function offStatus($userid){
        $arr = $this->dnkl();
        foreach($arr as $k=>$v){
            $this->db->query("update {$this->db->pre}{$v} set oldstatus=status,status = 4 WHERE userid = $userid ");
        }
    }
    function dnkl(){
        return array('food_23','job','know','know_cai','brand_13','sell_5','know','know_cai','buy_6','special','article_21','resume');
    }

    function onStatus($userid){
        $arr = $this->dnkl();
        foreach($arr as $k=>$v){
            $this->db->query("update {$this->db->pre}{$v} set status=oldstatus,oldstatus = 0 WHERE userid = $userid ");
        }
    }

    function is_username($username) {
        global $MOD, $L;
        if(!preg_match('/^.{2,12}$/',$username)) return $this->_('请输入2-12个字');
        if($this->username_exists($username)) return $this->_($L['member_username_reg']);
        return true;
    }

    function huifu($userid){
        global $dc, $CFG, $MODULE, $L;
        if(!$userid) return false;
        if(is_array($userid)) {
            $userids = implode(',', $userid);
        } else {
            $userids = intval($userid);
        }
        $result = $this->db->query("SELECT username,userid,oldgroupid FROM {$this->table_member} WHERE userid IN ($userids) and groupid =3 ");
        while($r = $this->db->fetch_array($result)) {
            $userid = $r['userid'];
            if(!$userid) continue;
            $result = $this->db->query("update {$this->table_member} set groupid = {$r['oldgroupid']},oldgroupid=0 WHERE userid = $userid ");
            $this->onStatus($userid);
        }
        return true;
    }

    function deluser($table, $user, $name = true, $data = false, $moduleid = 0) {
        global $MODULE;
        if(!$user) return;
        $fields = $name ? 'username' : 'userid';
        if($data) {
            $result = $this->db->query("SELECT * FROM {$this->db->pre}{$table} WHERE `$fields`='$user'");
            while($r = $this->db->fetch_array($result)) {
                $itemid = $r['itemid'];
                $this->db->query("DELETE FROM {$this->db->pre}{$table} WHERE itemid='$itemid'");
                $table_data = strpos($table, '_') === false ? $table.'_data' : str_replace('_', '_data_', $table);
                $table_data = $this->db->pre.$table_data;
                if($moduleid) $table_data = content_table($moduleid, $itemid, is_file(DT_CACHE.'/'.$moduleid.'.part'), $table_data);
                $this->db->query("DELETE FROM {$table_data} WHERE itemid='$itemid'");
                if($MODULE[$moduleid]['module'] == 'sell') $this->db->query("DELETE FROM {$this->db->pre}sell_search_{$moduleid} WHERE itemid=$itemid");
                if($moduleid && $r['linkurl'] && strpos($r['linkurl'], '://') === false && strpos($r['linkurl'], '.php') === false && strpos($r['linkurl'], 'show-') === false) {
                    $html = DT_ROOT.'/'.$MODULE[$moduleid]['moduledir'].'/'.$r['linkurl'];
                    if(is_file($html)) file_del($html);
                }
            }
        } else {
            $this->db->query("DELETE FROM {$this->db->pre}{$table} WHERE `$fields`='$user'");
        }
    }

    function delupload($username, $userid) {
        if(!$userid || !$username) return;
        $result = $this->db->query("SELECT fileurl FROM {$this->db->pre}upload_".($userid%10)." WHERE username='$username'");
        while($r = $this->db->fetch_array($result)) {
            delete_upload($r['fileurl'], $userid);
        }
    }

    function rename($cusername, $nusername) {
        global $MODULE, $L;
        $cusername = trim($cusername);
        $nusername = trim($nusername);
        if(!$this->username_exists($cusername)) return $this->_($L['member_rename_not_member']);
        if(!$this->is_username($nusername)) return false;
        $tables = array('alert', 'ask', 'comment', 'club_fans', 'club_group', 'club_manage', 'club_reply', 'finance_card', 'finance_cash', 'finance_charge', 'finance_pay', 'finance_deposit', 'finance_record', 'finance_sms', 'form_answer', 'form_record', 'guestbook', 'honor', 'job_talent', 'link', 'admin_log', 'login', 'mail_list', 'spread', 'news', 'resume', 'upgrade', 'know_answer', 'know_vote', 'news', 'page', 'address', 'oauth', 'vote_record', 'gift_order', 'poll_record', 'weixin_bind', 'weixin_user', 'member', 'member_check', 'company');
        foreach($MODULE as $m) {
            if($m['islink'] || $m['moduleid'] < 5) continue;
            $tables[] = in_array($m['module'], array('article', 'brand', 'buy', 'down', 'info', 'photo', 'sell', 'video')) ? $m['module'].'_'.$m['moduleid'] : $m['module'];
        }
        foreach($tables as $table) {
            $this->db->query("UPDATE {$this->db->pre}{$table} SET username='$nusername' WHERE username='$cusername'");
        }
        $this->db->query("UPDATE {$this->db->pre}mall_order SET buyer='$nusername' WHERE buyer='$cusername'");
        $this->db->query("UPDATE {$this->db->pre}mall_order SET seller='$nusername' WHERE seller='$cusername'");
        $this->db->query("UPDATE {$this->db->pre}group_order SET buyer='$nusername' WHERE buyer='$cusername'");
        $this->db->query("UPDATE {$this->db->pre}group_order SET seller='$nusername' WHERE seller='$cusername'");
        $this->db->query("UPDATE {$this->db->pre}job_apply SET apply_username='$nusername' WHERE apply_username='$cusername'");
        $this->db->query("UPDATE {$this->db->pre}message SET fromuser='$nusername' WHERE fromuser='$cusername'");
        $this->db->query("UPDATE {$this->db->pre}message SET touser='$nusername' WHERE touser='$cusername'");
        userclean($cusername);
        return true;
    }

    function edit_passport($cpassport, $npassport, $username) {
        if(!$this->is_passport($npassport)) return false;
        $this->db->query("UPDATE {$this->db->pre}member SET passport='$npassport' WHERE passport='$cpassport'");
        $this->db->query("UPDATE {$this->db->pre}comment SET passport='$npassport' WHERE passport='$cpassport'");
        $this->db->query("UPDATE {$this->db->pre}know SET passport='$npassport' WHERE passport='$cpassport'");
        $this->db->query("UPDATE {$this->db->pre}know_answer SET passport='$npassport' WHERE passport='$cpassport'");
        $this->db->query("UPDATE {$this->db->pre}know_expert SET passport='$npassport' WHERE passport='$cpassport'");
        $this->db->query("UPDATE {$this->db->pre}know_vote SET passport='$npassport' WHERE passport='$cpassport'");
        $this->db->query("UPDATE {$this->db->pre}club SET passport='$npassport' WHERE passport='$cpassport'");
        $this->db->query("UPDATE {$this->db->pre}club_fans SET passport='$npassport' WHERE passport='$cpassport'");
        $this->db->query("UPDATE {$this->db->pre}club_group SET passport='$npassport' WHERE passport='$cpassport'");
        $this->db->query("UPDATE {$this->db->pre}club_reply SET passport='$npassport' WHERE passport='$cpassport'");
        $this->db->query("UPDATE {$this->db->pre}club SET replyer='$npassport' WHERE replyer='$cpassport'");
        userclean($username);
        return true;
    }

    function move($userid, $groupid) {
        global $CFG, $L;
        if(is_array($userid)) {
            foreach($userid as $v) { $this->move($v, $groupid); }
        } else {
            $userid = intval($userid);
            if($userid == 1 || $userid == $CFG['founderid']) return $this->_($L['member_founder_move']);
            $this->userid = $userid;
            $user = $this->get_one();
            if($user) {
                $this->db->query("UPDATE {$this->table_member} SET groupid='$groupid' WHERE userid=$userid");
                $this->db->query("UPDATE {$this->table_company} SET groupid='$groupid' WHERE userid=$userid");
                userclean($user['username']);
            }

        }
        return true;
    }

    function check($userid) {
        if(is_array($userid)) {
            foreach($userid as $v) { $this->check($v); }
        } else {
            $this->userid = $userid;
            $user = $this->get_one();
            if($user) {
                $groupid = $user['regid'] ? $user['regid'] : 6;
                $this->db->query("UPDATE {$this->table_member} SET groupid=$groupid WHERE userid=$userid");
                $this->db->query("UPDATE {$this->table_company} SET groupid=$groupid WHERE userid=$userid");
                userclean($user['username']);
            }
            return true;
        }
    }

    function login_log($username, $password, $salt, $admin = 0, $message = '') {
        global $DT_TIME, $DT_IP, $L;
        $password = dpassword($password, $salt);
        $agent = addslashes(dhtmlspecialchars(strip_sql($_SERVER['HTTP_USER_AGENT'])));
        $message or $message = $L['member_login_ok'];
        if($message == $L['member_login_ok']) cache_delete($DT_IP.'.php', 'ban');
        $this->db->query("INSERT INTO {$this->db->pre}login (username,password,passsalt,admin,loginip,logintime,message,agent) VALUES ('$username','$password','$salt','$admin','$DT_IP','$DT_TIME','$message','$agent')");
    }

    function check_get() {
        $r = $this->db->get_one("SELECT content FROM {$this->table_member_check} WHERE userid=$this->userid");
        return $r['content'] ? dstripslashes(unserialize($r['content'])) : array();
    }

    function check_add($post) {
        global $DT_TIME;
        if(isset($post['content'])) {
            $content = dsafe($post['content']);
            unset($post['content']);
            $post = dhtmlspecialchars($post);
            $post['content'] = $content;
        } else {
            $post = dhtmlspecialchars($post);
        }
        $content = addslashes(serialize($post));
        $this->db->query("REPLACE INTO {$this->table_member_check} (userid,username,content,addtime) VALUES ('$this->userid','$this->username','$content','$DT_TIME')");
    }

    function _($e) {
        $this->errmsg = $e;
        return false;
    }

    /**
     * 循环获取唯一用户名
     */
    function get_only_user(){
        $username = 'shixun'.(substr(md5(time().rand(1000000000,9000000000)),rand(1,8),rand(3,6)));
        if($this->username_exists($username)){
            $this->get_only_user();
        }else{
            return  $username;
        }
    }

    /**
     * 用户名是否存在
     * @param $username
     * @return mixed
     */
    function username_exists($username) {
        return $this->get_one("username='$username'",'userid');
    }

    /**
     * 是否为正常用户
     */
    function isCompany($val,$dn='username'){
        if(mb_strlen($val,'utf-8')==0){
            return $this->_('用户不能为空');
        }
        $aUser = $this->get_one("{$dn}='".$val."'",'*');
        if(!$aUser) return $this->_('用户不存在');
        if($aUser['groupid']!=5) return $this->_('用户禁止访问');
        return $aUser;
    }

    function getListUser($arr,$field='*'){
        if(!$arr) return array();
        $sUserid = '';
        foreach($arr as $k=>$v){
            $sUserid .= $v['userid'].',';
        }
        $sUserid = substr($sUserid,0,-1);
        $result = $this->db->query("select {$field},userid from {$this->db->pre}member where userid in ($sUserid)");
        $list = array();
        while($r = $this->db->fetch_array($result)){
            $list[$r['userid']] = $r;
        }
        return $list;
    }



}
?>