<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once '../module/job/job.class.php';
$oJob = new job(9);
$aCatCache = cache_read('category-9.php');

$action = isset($action)?$action:'';
switch($action){
    case 'del':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $itemid = isset($itemid)?intval($itemid):0;
        if(empty($itemid)) exit(json_encode(array('status'=>'n','info'=>'非法操作')));
        $oJob->itemid = $itemid;
        $aJob = $oJob->get_one();
        if(!$aJob || $aJob['userid']!=$_userid) exit(json_encode(array('status'=>'n','info'=>'信息已失效')));
        if($aJob['status']==3) exit(json_encode(array('status'=>'n','info'=>'信息不能删除')));
        $oJob->recycle($itemid);
        exit(json_encode(array('status'=>'y','info'=>'删除成功')));
        break;
    default:
        if(!$_userid) dheader('/member/login.php');
        $where = ' userid = '.$_userid;
        list($aJob,$totalpage) = $oJob->jobList('thumb,title,itemid,catid,company,status',$where.' and (status=3 or status = 2 or status = 1)','hits desc','10');
        $active = 'job';
        $nav_selected='member';
        $seo_title = '招聘信息管理-会员中心-';
        include template('job', $module);
        break;
}
?>