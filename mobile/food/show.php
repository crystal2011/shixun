<?php
/**
 * 手机端-食品供应-详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/food/food.class.php';
$oFood = new food(23);
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
    $info['telephone'] = $info['mobile'];
    $seo_title = $info['title'].'-餐饮供应-预览-';
    $topname = '餐饮供应-预览';
    $backurl = 'javascript:history.back();';
}else{
    $id = isset($id)?intval($id):0;
    if(empty($id)){
        dalert('非法操作','/mobile/food/index.php');
    }


    $oFood->itemid = $id;
    $info = $oFood->get_one();
    if(isset($showtype)){   //羊角会预览
        $s = chcekHorn($info,0);
        if($s!==true) dalert($s,$forward);
        $backurl = $forward;
    }else {
        if (!$checkFood = $oFood->checkFood($info)) {
            dalert($oFood->errmsg, '/mobile/food/index.php');
        }
        $oFood->editHits();  //更新浏览量
        addHits($info['userid']);
        $backurl = '/mobile/food/index.php';
    }
    //当时发布的code
    require_once '../../module/special/special.class.php';
    $oSpecial = new special(11);
    $codetishicode = $oSpecial->getShowCode($id,0);
    $seo_title = $info['title'].'-餐饮供应-';
    $topname = '餐饮供应';

}

$aRecommendFood = $oFood->getright('title,itemid,price,introduce,likes,hits,unit,thumb',4,'addtime desc');     //推荐

$likenum = 1;
$commenttypeid = 1;
$moduleidtype = 0;

include template('food/show','mobile');