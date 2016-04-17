<?php
/**
 * 手机端-名厨学堂-菜系详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/know/know.class.php';
$oCai = new know;
$isajax = isset($isajax)?true:false;
if($isajax){  //用户发布、编辑预览
    $_POST = strip_sql(daddslashes($_POST));
    strip_key($_POST);
    $content = $_POST['content'];
    unset($_POST['content']);
    $_POST = dhtmlspecialchars($_POST);
    $_POST['content'] = addslashes(dsafe($content));
    $info = $_POST;
    $codetishicode = $info['code'];
    $id = $info['likes'] = $info['hits'] = $info['votes'] = $info['comments'] = 0;
    $seo_title = $info['title'].'-名厨学堂-名菜展示-预览-';
    $topname = '名厨学堂-名菜展示-预览';
    $info['userid'] = $_userid;
    $backurl = 'javascript:history.back();';
}else{
    $id = isset($id)?intval($id):0;
    if(empty($id)){
        dalert('非法操作','/mobile/school/food.php');
    }


    $oCai->itemid = $id;
    $info = $oCai->get_one();
    if(isset($showtype)){   //预览
        $s = chcekHorn($info,4);
        if($s!==true) dalert($s,$forward);
        $backurl = $forward;
    }else {
        if (!$oCai->checkKnow($info)) {
            dalert($oCai->errmsg, '/mobile/school/food.php');
        }
        //更新浏览量
        $oCai->editHits();
        addHits($info['userid']);
        $backurl = '/mobile/school/show.php?id='.$aKnow['userid'];
    }
    $seo_title = $info['title'].'-名厨学堂-名菜展示-';
    $topname = '名厨学堂-名菜展示';
    //当时发布的code
    require_once '../../module/special/special.class.php';
    $oSpecial = new special(11);
    $codetishicode = $oSpecial->getShowCode($id,4);
}

require_once '../../module/member/member.class.php';
$oMember = new member();
$oMember->userid = $info['userid'];
$aKnow = $oMember->get_one();

$aRecommendFood = $oCai->getright('title,itemid,introduce,likes,hits,userid,thumb,votes',10,'addtime desc');  //推荐
$memberlistRec = $oMember->getListUser($aRecommendFood);

$likenum = 5;
$commenttypeid = 5;
$moduleidtype = 4;
include template('school/foodshow','mobile');