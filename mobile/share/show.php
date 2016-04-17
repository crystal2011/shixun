<?php
/**
 * 手机端-分享-详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/buy/buy.class.php';
$oBuy = new buy(6);
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
    $id = $info['likes'] = $info['hits'] = $info['comments'] = 0;
    $info['addtime'] = $DT_TIME;
    $seo_title = $info['title'].'-美食分享-预览-';
    $topname = '美食分享详情-预览';
    $backurl = 'javascript:history.back();';
}else{
    $id = isset($id)?intval($id):0;
    if(empty($id)){
        dalert('非法操作','/mobile/share/index.php');
    }


    $oBuy->itemid = $id;
    $info = $oBuy->get_one();
    if(isset($showtype)){   //预览
        $s = chcekHorn($info,5);
        if($s!==true) dalert($s,$forward);
        $backurl = $forward;
    }else {
        if(!$oBuy->checkBuy($info)) {
            dalert($oBuy->errmsg, '/mobile/share/index.php');
        }
        //更新浏览量
        $oBuy->editHits();
        addHits($info['userid']);
        $backurl = '/mobile/share/index.php';
    }

//当时发布的code
    require_once '../../module/special/special.class.php';
    $oSpecial = new special(11);
    $codetishicode = $oSpecial->getShowCode($id,5);
    $seo_title = $info['title'].'-美食分享-';
    $topname = '美食分享详情';

}


$aRecommendFood = $oBuy->getright('title,itemid,hits,likes,thumb,comments,address,areaid,address,addtime',4,'addtime desc');  //推荐

$commenttypeid = 6;
$moduleidtype = 5;
$islikenum = true;

include template('share/show','mobile');