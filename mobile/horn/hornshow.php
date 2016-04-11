<?php
/**
 * 手机端-羊角会-成员详情页
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/special/special.class.php';


$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/mobile/horn/index.php');
}


$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';
$oSpecial->itemid = $id;
$info = $oSpecial->get_one();
if(!$oSpecial->checkSpecial($info)){
    dalert($oSpecial->errmsg,'/mobile/horn/index.php');
}


$aRecommendFood = $oSpecial->getright('title,itemid,introduce,hits,thumb,code,userds',10,'addtime desc');  //推荐

//更新浏览量
$oSpecial->editHits();
$seo_title = $info['title'].'-羊角会-';
$topname = '羊角会成员';
$islikenum = true;
$commenttypeid = 8;
$nocode = true;
include template('horn/hornshow','mobile');