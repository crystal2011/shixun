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
$info['title'] = $info['truename'];
$info['company'] = '';
$info['telephone'] = $info['mobile'];
//获取该名称的餐饮列表
require_once '../../module/know/know.class.php';
$oCai = new know(10);
list($list,$totalpage) = $oCai->schoolList('*','userid = '.$id.' and status=3','addtime desc','5');
if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $coofeestr = '';
            if(doubleval($v['coofee'])){
                $coofeestr = '<div class="handle clear hasbutton" style="border-bottom-width: 0px;padding-top:10px;padding-bottom:0px;text-align:right;">
                        <span class="handle1 price1 " style="font-size:1.4em; color: #ff8125;">
                            <img src="'.DT_SKIN.'image/mobile/yes.png" style="width:18px;height:auto;margin-right:0px;vertical-align: middle;" />
                            技术合作费：'.doubleval($v['coofee']).'元
            </span>
                    </div>';
            }

            $info .= '<li class="clear" style="background-color:#fff;margin-bottom:10px;border-top:1px solid #e6e6e6;border-bottom:1px solid #e6e6e6;padding:0px 20px 10px 20px;">
                <p style="font-size:1.8em;line-height: 1.8em;height:1.8em;overflow:hidden;margin-bottom:10px;"><a style="width:100%;display:block;color:#000;font-size:1em;" href="/mobile/school/foodshow.php?id='.$v['itemid'].'">'.$v['title'].'</a></p>
                <span class="numsviews" style="float:none;text-align:right;display:block;margin-bottom:5px;">
                    <img src="'.DT_SKIN.'image/public/icon-liulan.png" />浏览('.$v['hits'].')&nbsp;&nbsp;&nbsp;
                    <img src="'.DT_SKIN.'image/public/icon-pinlun.png" />评论('.$v['comments'].')
                </span>
                <a style="width:100%;display:block;" href="/mobile/school/foodshow.php?id='.$v['itemid'].'"><img style="width:100%;height:auto;" src="'.$v['thumb'].'" /></a>
                <div class="lidiv">'.$coofeestr.'<div class="handle clear hasbutton" style="border-bottom-width: 0px;padding-top:0px;padding-bottom:10px;text-align:right;">
                        <span class="handle1 price1 " style="width:100%;display:block;font-size:1.4em; color: #ff8125;">
                         <img onclick="vote('.$v['itemid'].',2,this)" src="'.DT_SKIN.'image/mobile/bigvote.png" style="width: 20%;max-width: 100px;min-width:60px;vertical-align: middle;margin-right:5px;" class="bigvote" /><i class="num">票数：<i>'.$v['votes'].'</i></i>
                        </span>
                    </div>
                </div>
            </li>';
        }
    }
    exit(json_encode(array('info'=>$info,'totalpage'=>$totalpage)));
}else {
    $commenttypeid = 11;
    $moduleidtype = 4;
    $nocode = true;
    //更新浏览量
    $oMember->editHits();
    $seo_title = $info['truename'] . '-名厨学堂-名厨风采-';
    $topname = '名厨学堂-名厨风采';
    $backurl = '/mobile/school/index.php';
    include template('school/show', 'mobile');
}