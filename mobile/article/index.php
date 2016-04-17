<?php
/**
 * 手机端-文章列表-首页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';


$pagesize = 6;
$offset = ($page-1)*$pagesize;
require_once '../../module/article/article.class.php';
$oArticle = new article(21);
$where = ' 1 = 1 ';
if($catid && $catinfo = get_cat($catid)){
    $where = " catid in (".$catinfo['arrchildid'].") ";
}
$list = $oArticle->get_list($where .' and status = 3');
$totalpage = ceil($items/$pagesize);$action=isset($action)?$action:'';

if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<div class="content">
                        <div class="clear info">
                            <a class="thumb" href="/mobile/article/show.php?id='.$v['itemid'].'"><img src="'.$v['thumb'].'" /></a>
                            <div class="text">
                                <a class="title" href="/mobile/article/show.php?id='.$v['itemid'].'">'.$v['title'].'</a>
                                <p class="introduce">简介：'.$v['introduce'].'</p>
                                <p class="numviesshow clear">
                                    <span class="fl numtimes">'.date('Y-m-d',$v['addtime']).'</span>
                                    <span class="numsviews">
                                        <img src="'.DT_SKIN.'image/public/icon-liulan.png" />浏览('.$v['hits'].')&nbsp;
                                        <img src="'.DT_SKIN.'image/public/icon-pinlun.png" />评论('.$v['comments'].')
                                    </span>
                                </p>
                            </div>
                        </div>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else{
    $seo_title = '文章发布-';
    $topname = '文章发布';
    $moduleidtype = 6;
    $backurl = '/mobile/index.php';
    include template('article/index','mobile');
}
