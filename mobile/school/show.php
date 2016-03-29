<?php
/**
 * 手机端-名厨学堂-名厨详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/member/member.class.php';
$oMember = new member();

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/school/list.php');
}

$oMember->userid = $id;
$info = $oMember->get_one();
if(!$oMember->checkKnow($info)){
    dalert($oMember->errmsg,'/mobile/school/index.php');
}
$info['content'] = $info['introduce'];

require_once '../../module/know/know.class.php';
$oCai = new know(10);
list($list,$totalpage) = $oCai->schoolList('*','userid = '.$id.' and status=3','addtime desc','6');
if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<div class="content">
                        <div class="clear info">
                            <a class="thumb" href="/mobile/school/foodshow.php?id='.$v['itemid'].'"><img src="'.$v['thumb'].'" /></a>
                            <div class="text">
                                <a class="title" href="/mobile/school/foodshow.php?id='.$v['itemid'].'">'.$v['title'].'</a>
                                <p class="introduce">'.$v['introduce'].'</p>
                            </div>
                        </div>
                        <div class="handle clear hasbutton">
                            <span class="handle1"><img src="'.DT_SKIN.'image/mobile/love.png" />'.$v['likes'].'</span>
                            <span><img src="'.DT_SKIN.'image/mobile/hit.png" />'.$v['hits'].'</span>
                            <a class="fr showpng" href="/mobile/school/foodshow.php?id='.$v['itemid'].'"><img src="'.DT_SKIN.'image/mobile/show.png" /></a>
                        </div>
                    </div>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else {
//更新浏览量
    $oMember->editHits();

    $seo_title = $info['truename'] . '-名厨学堂-';
    $topname = '名厨学堂';
    include template('school/show', 'mobile');
}