<?php
/**
 * 手机端-分享-首页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/buy/buy.class.php';

$where = '1=1';
$oBuy = new buy(6);
list($list,$totalpage) = $oBuy->buyList('title,userid,itemid,introduce,thumb,likes,hits',$where.' and status=3 ','addtime desc','6');


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
                                <p class="introduce">简介：'.$v['introduce'].'</p>
                            </div>
                        </div>
                        <div class="handle clear hasbutton">
                            <span class="handle1"><img src="'.DT_SKIN.'image/mobile/love.png" />'.$v['likes'].'</span>
                            <span><img src="'.DT_SKIN.'image/mobile/hit.png" />'.$v['hits'].'</span>
                            <a class="fr showpng" href="/mobile/share/show.php?id='.$v['itemid'].'"><img src="'.DT_SKIN.'image/mobile/show.png" /></a>
                        </div>
                    </div>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else{
    $seo_title = '分享-';
    $topname = '分享';
    $moduleidtype = 5;
    include template('share/index','mobile');
}