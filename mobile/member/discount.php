<?php
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once DT_ROOT.'/module/brand/brand.class.php';
$oBrand = new brand(9);
$aCatCache = cache_read('category-13.php');

$action = isset($action)?$action:'';
switch($action){
    case 'del':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $itemid = isset($itemid)?intval($itemid):0;
        if(empty($itemid)) exit(json_encode(array('status'=>'n','info'=>'非法操作')));
        $oBrand->itemid = $itemid;
        $aBrand = $oBrand->get_one();
        if(!$aBrand || $aBrand['userid']!=$_userid) exit(json_encode(array('status'=>'n','info'=>'信息已失效')));
        if($aBrand['status']==3) exit(json_encode(array('status'=>'n','info'=>'信息不能删除')));
        $oBrand->recycle($itemid);
        exit(json_encode(array('status'=>'y','info'=>'删除成功')));
        break;
    case 'ajax':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $where = ' userid = '.$_userid;
        list($list,$totalpage) = $oBrand->brandList('thumb,title,itemid,catid,company,fromtime,totime,status',$where.' and ( status=3 or status = 2 or status = 1) ','addtime desc','10');
        $str = '';
        if($list){
            foreach($list as $k=>$v){
                $catname = isset($aCatCache[$v['catid']])?$aCatCache[$v['catid']]['catname']:'';
                $deurl = '';
                if($v['status']==1) {
                    $deurl = '<span class="check-btn orange bk-white" onclick = "location.href=\'/mobile/share/publish.php?moduleidtype=1&itemid='.$v[itemid].'\'" style = "display:block;" > 修改</span >';
                }
                $delsurl = '';
                if($v['status']!=3) {
                    $delsurl = '<span  onclick = "del('.$v['itemid'].',this)" class="check-btn orange bk-white" style = "display:block;" > 删除</span >';
                }
                $statusname = status_show($v['status']);
                $str .= '<li class="clear">
                    <a href="/mobile/discount/show.php?id='.$v['itemid'].'"  target="_blank" style="background-image: url('.$v['thumb'].');background-size:100%" class="mucollect-img db fl" title="'.$v['title'].'"></a>
                    <div class="message fl">
                        <span class="info db"><a href="/mobile/discount/show.php?id='.$v['itemid'].'"  target="_blank">'.dsubstr($v['title'],40).'</a></span><span class="money orange db">优惠：'.date('Y-m-d',$v['fromtime']).' - '.date('Y-m-d',$v['totime']).'</span>
                        分类：'.$catname.'&nbsp;&nbsp;&nbsp;状态：'.$statusname.'</div>
                    <span class="fr  tc ">'.$deurl.$delsurl.'</span>
                </li>';
            }
        }
        exit(json_encode(array('status'=>'y','info'=>$str,'totalpage'=>$totalpage)));
        break;
    default:
        if(!$_userid) dheader('/mobile/member/login.php');
        $where = ' userid = '.$_userid;
        list($list,$totalpage) = $oBrand->brandList('thumb,title,itemid,catid,company,fromtime,totime,status',$where.' and ( status=3 or status = 2 or status = 1) ','addtime desc','10');
        $seo_title = '餐饮优惠管理-会员中心-';
        $topname = '餐饮优惠管理';
        $moduleidtype = 1;
        $backurl = '/mobile/member/index.php';
        include template('discount', 'mobile/'.$module);
        break;
}
?>