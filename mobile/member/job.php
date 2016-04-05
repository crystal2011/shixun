<?php
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once DT_ROOT.'/module/job/job.class.php';
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
        $oJob->recycle($itemid);
        exit(json_encode(array('status'=>'y','info'=>'删除成功')));
        break;
    case 'ajax':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $where = ' userid = '.$_userid;
        list($list,$totalpage) = $oJob->jobList('thumb,title,itemid,catid,company,status',$where.' and (status=3 or status = 2 or status = 1)','addtime desc','4');
        $str = '';
        if($list){
            foreach($list as $k=>$v){
                $catname = isset($aCatCache[$v['catid']])?$aCatCache[$v['catid']]['catname']:'';
                $deurl = '';
                if($v['status']==1) {
                    $deurl = '<span class="check-btn orange bk-white" onclick = "location.href=\'/mobile/share/publish.php?moduleidtype=2&itemid='.$v[itemid].'\'" style = "display:block;" > 修改</span >';
                }
                $statusname = status_show($v['status']);
                $str .= '<li class="clear">
                    <div class="message fl">
                        <span class="info db"><a href="/mobile/job/show.php?id='.$v['itemid'].'"  target="_blank">'.dsubstr($v['title'],40).'</a></span><span class="money db">分类：'.$catname.'&nbsp;&nbsp;&nbsp;状态：'.$statusname.'</span>
                        </div>
                    <span class="fr  tc ">'.$deurl.'<span  onclick="del('.$v['itemid'].',this)" class="check-btn orange bk-white" style="display:block;">删除</span></span>
                </li>';
            }
        }
        exit(json_encode(array('status'=>'y','info'=>$str,'totalpage'=>$totalpage)));
        break;
    default:
        if(!$_userid) dheader('/mobile/member/login.php');
        $where = ' userid = '.$_userid;
        list($list,$totalpage) = $oJob->jobList('thumb,title,itemid,catid,company,status',$where.' and (status=3 or status = 2 or status = 1)','addtime desc','10');
        $seo_title = '招聘信息管理-会员中心-';
        $moduleidtype = 2;
        include template('job', 'mobile/'.$module);
        break;
}
?>