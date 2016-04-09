<?php
/**
 * 手机端-分享-首页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/buy/buy.class.php';

$where = '1=1';
$oBuy = new buy(6);
list($list,$totalpage) = $oBuy->buyList('title,userid,itemid,thumb,likes,hits,areaid,address,addtime,comments',$where.' and status=3 ','addtime desc','10');


$action=isset($action)?$action:'';

if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<div class="content">
                        <div class="clear info">
                            <a class="thumb" href="/mobile/share/show.php?id='.$v['itemid'].'"><img src="'.$v['thumb'].'" /></a>
                            <div class="text">
                                <a class="title" href="/mobile/share/show.php?id='.$v['itemid'].'">'.$v['title'].'</a>
                                <p class="introduce">'.area_pos($v['areaid'],'').$v['address'].'</p>
                                <p class="numviesshow clear">
                                    <span class="fl numtimes">'.date('Y-m-d',$v['addtime']).'</span>
                                    <span class="numsviews">
                                        <img src="'.DT_SKIN.'image/public/icon-liulan.png" />浏览('.$v['hits'].')&nbsp;
                                        <img src="'.DT_SKIN.'image/public/icon-pinlun.png" />评论('.$v['comments'].')
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else{
    $seo_title = '美食分享-';
    $topname = '美食分享';
    $moduleidtype = 5;
    include template('share/index','mobile');
}