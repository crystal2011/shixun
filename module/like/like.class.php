<?php
defined('IN_DESTOON') or exit('Access Denied');
class like {
    var $itemid;
    var $db;
    var $table;
    var $errmsg = errmsg;

    function like() {
        global $db, $DT_PRE;
        $this->table = $DT_PRE.'like';
        $this->db = &$db;
    }

    /**
     * 是否已经喜欢过  够期限了吗
     */
    function isLiked($id,$type){
        global $DT_IP,$DT_TIME;
        $ip2long = ip2long($DT_IP);
        $aLike = $this->get_one('ip='.$ip2long.' and type = '.$type.' and id = '.$id,'addtime');
        if($aLike && $DT_TIME-$aLike['addtime']<=3600){
            return $this->_('您刚发表喜欢，请稍后再续...');
        }
        return true;
    }

    /**
     * 添加喜欢量
     */
    function add($id,$type){
        global $DT_IP,$DT_TIME;
        $ip2long = ip2long($DT_IP);
        $this->db->query("insert into {$this->table} (id,type,ip,addtime) values ($id,$type,$ip2long,$DT_TIME)");
        return true;
    }


    function get_one($condition='1=1',$field='*'){
        return $this->db->get_one("select {$field} from {$this->table} where {$condition} order by itemid desc");
    }


    function _($e) {
        $this->errmsg = $e;
        return false;
    }
}
?>