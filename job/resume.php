<?php
define('DT_REWRITE', true);
require 'config.inc.php';
require '../common.inc.php';
require_once '../module/job/resume.class.php';


$id = isset($id)?intval($id):0;
if(empty($id)){
    dalert('非法操作','/share/index.php');
}

$oResume = new resume(9);
$oResume->itemid = $id;
$aJob = $oResume->get_one();
if(!$oResume->checkBuy($aJob)) {
    dalert($oResume->errmsg, '/share/index.php');
}
$aCacheCat9 = cache_read('category-9.php');
//更新浏览量
$oResume->editHits();

$aSetting = cache_read('module-9.php');
$aSetting['education'] = explode('|',$aSetting['education']);
$aSetting['type'] = explode('|',$aSetting['type']);

$nav_selected = 'job';
$seo_title = $aJob['truename'].'-简历-招聘信息-';
include template('resume', 'job');
?>