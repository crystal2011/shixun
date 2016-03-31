<?php
if(php_sapi_name() !== 'cli') return false;
//喜欢量  点击量  每分钟+1
require_once substr(str_replace("\\", '/', dirname(__FILE__)),0,-6).'/common.inc.php';

set_time_limit(0);
ini_set("max_execution_time", "18000000");


addcom('food_23',true);//餐饮供应
addcom('brand_13',true); //商家优惠
addcom('job',true); //招聘信息
addcom('sell_5',true); //店铺转让
addcom('know',true); //名厨学堂 - 菜系
addcom('buy_6',false); //分享
addcom('info_24',false,false); //羊角会
addcom('special',false); //羊角会成员
addcom('article_21',false); //文章

function addcom($table,$haslike,$userhits=true){
    //商家优惠
    global $db,$DT_PRE;
    if($userhits){
        $info = $db->get_one("select itemid from {$DT_PRE}{$table} where status = 3 order by itemid desc limit 100,1");
    }else{
        $info = $db->get_one("select itemid from {$DT_PRE}{$table} where status = 3 order by itemid desc limit 100,1");
    }

    $where = ' status = 3';
    if($info){
        $where .= ' and itemid > '.$info['itemid'];
    }
    $s = $haslike?",likes=likes+1":'';

    if($userhits){
        $result = $db->query("select userid from {$DT_PRE}{$table} where ".$where);
        while($r = $db->fetch_array($result)){
            addHits($r['userid']);
        }
    }
    $db->query("update {$DT_PRE}{$table} set hits=hits+1{$s} where ".$where);

}



