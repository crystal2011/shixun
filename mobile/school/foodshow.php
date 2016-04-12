<?php
/**
 * 手机端-名厨学堂-菜系详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';

$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/school/food.php');
}

require_once '../../module/know/know.class.php';
$oCai = new know;
$oCai->itemid = $id;
$info = $oCai->get_one();
if(isset($showtype)){   //预览
    $s = chcekHorn($info,4);
    if($s!==true) dalert($s,$forward);
}else {
    if (!$oCai->checkKnow($info)) {
        dalert($oCai->errmsg, '/mobile/school/food.php');
    }
    //更新浏览量
    $oCai->editHits();
    addHits($info['userid']);
}
require_once '../../module/member/member.class.php';
$oMember = new member();
$oMember->userid = $info['userid'];
$aKnow = $oMember->get_one();



$aRecommendFood = $oCai->getright('title,itemid,introduce,likes,hits,userid,thumb,votes',10,'addtime desc');  //推荐

$memberlistRec = $oMember->getListUser($aRecommendFood);

$seo_title = $info['title'].'-名厨学堂-名菜展示-';
$topname = '名厨学堂-名菜展示';
$likenum = 5;
$commenttypeid = 5;
$moduleidtype = 4;
//当时发布的code
require_once '../../module/special/special.class.php';
$oSpecial = new special(11);
$codetishicode = $oSpecial->getShowCode($id,4);
include template('school/foodshow','mobile');