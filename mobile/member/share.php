<?php
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
require_once DT_ROOT.'/module/buy/buy.class.php';
$oBuy = new buy(6);

$action = isset($action)?$action:'';
switch($action){
    case 'del':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $itemid = isset($itemid)?intval($itemid):0;
        if(empty($itemid)) exit(json_encode(array('status'=>'n','info'=>'非法操作')));
        $oBuy->itemid = $itemid;
        $aBuy = $oBuy->get_one();
        if(!$aBuy || $aBuy['userid']!=$_userid) exit(json_encode(array('status'=>'n','info'=>'信息已失效')));
        if($aBuy['status']==3) exit(json_encode(array('status'=>'n','info'=>'信息不能删除')));
        $oBuy->recycle($itemid);
        exit(json_encode(array('status'=>'y','info'=>'删除成功')));
        break;
    case 'ajax':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $where = ' userid = '.$_userid;
        list($list,$totalpage) = $oBuy->buyList('thumb,title,itemid,status',$where.' and ( status=3 or status=2 or status=1) ','addtime desc','10');
        $str = '';
        if($list){
            foreach($list as $k=>$v){

                $deurl = '';
                if($v['status']==1) {
                    $deurl = '<span class="check-btn orange bk-white" onclick = "location.href=\'/mobile/share/publish.php?moduleidtype=5&itemid='.$v[itemid].'\'" style = "display:block;" > 修改</span >';
                }
                $delsurl = '';
                if($v['status']!=3) {
                    $delsurl = '<span  onclick = "del('.$v['itemid'].',this)" class="check-btn orange bk-white" style = "display:block;" > 删除</span >';
                }
                $statusname = status_show($v['status']);
                $str .= '<li class="clear">
                    <a href="/mobile/share/show.php?id='.$v['itemid'].'"  target="_blank" style="background-image: url('.$v['thumb'].');background-size:100%" class="mucollect-img db fl" title="'.$v['title'].'"></a>
                    <div class="message fl">
                        <span class="info db"><a href="/mobile/share/show.php?id='.$v['itemid'].'"  target="_blank">'.dsubstr($v['title'],40).'</a></span><span class="money db">状态：'.$statusname.'</span>
                        </div>
                    <span class="fr  tc ">'.$deurl.$delsurl.'</span>
                </li>';
            }
        }
        exit(json_encode(array('status'=>'y','info'=>$str,'totalpage'=>$totalpage)));
        break;
    default:
        if(!$_userid) dheader('/mobile/member/login.php');
        $where = ' userid = '.$_userid;
        list($list,$totalpage) = $oBuy->buyList('thumb,title,itemid,status',$where.' and ( status=3 or status=2 or status=1) ','addtime desc','10');
        $seo_title = '美食分享管理-会员中心-';
        $topname = '美食分享';
        $moduleidtype = 5;
        $backurl = '/mobile/member/index.php';
        include template('share', 'mobile/'.$module);
        break;
}
?>