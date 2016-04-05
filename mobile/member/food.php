<?php
require '../../member/config.inc.php';
require '../../common.inc.php';
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require_once DT_ROOT.'/module/food/food.class.php';
$oFood = new food(23);
$aCatCache = cache_read('category-23.php');

$action = isset($action)?$action:'';
switch($action){
    case 'del':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $itemid = isset($itemid)?intval($itemid):0;
        if(empty($itemid)) exit(json_encode(array('status'=>'n','info'=>'非法操作')));
        $oFood->itemid = $itemid;
        $aFood = $oFood->get_one();
        if(!$aFood || $aFood['userid']!=$_userid) exit(json_encode(array('status'=>'n','info'=>'信息已失效')));
        $oFood->recycle($itemid);
        exit(json_encode(array('status'=>'y','info'=>'删除成功')));
        break;
    case 'ajax':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $where = ' userid = '.$_userid;
        list($list,$totalpage) = $oFood->foodList('thumb,title,price,unit,itemid,catid,status',$where.' and (status=3 or status = 2 or status = 1)','addtime desc','10');
        $str = '';
        if($list){
            foreach($list as $k=>$v){
                $catname = isset($aCatCache[$v['catid']])?$aCatCache[$v['catid']]['catname']:'';
                $deurl = '';
                if($v['status']==1) {
                    $deurl = '<span class="check-btn orange bk-white" onclick = "location.href=\'/mobile/share/publish.php?moduleidtype=0&itemid='.$v[itemid].'\'" style = "display:block;" > 修改</span >';
                }
                $statusname = status_show($v['status']);
                $str .= '<li class="clear">
                    <a href="/mobile/food/show.php?id='.$v['itemid'].'"  target="_blank" style="background-image: url('.$v['thumb'].');background-size:100%" class="mucollect-img db fl" title="'.$v['title'].'"></a>
                    <div class="message fl">
                        <span class="info db"><a href="/mobile/food/show.php?id='.$v['itemid'].'"  target="_blank">'.dsubstr($v['title'],40).'</a></span><span class="money orange db">价格：'.$v['price'].'元/'.$v['unit'].'</span>
                        分类：'.$catname.'&nbsp;&nbsp;&nbsp;状态：'.$statusname.'</div>
                    <span class="fr  tc ">'.$deurl.'<span  onclick="del('.$v['itemid'].',this)" class="check-btn orange bk-white" style="display:block;">删除</span></span>
                </li>';
            }
        }
        exit(json_encode(array('status'=>'y','info'=>$str,'totalpage'=>$totalpage)));
        break;
    default:
        if(!$_userid) dheader('/mobile/member/login.php');
        $where = ' userid = '.$_userid;
        list($list,$totalpage) = $oFood->foodList('thumb,title,price,unit,itemid,catid,status',$where.' and (status=3 or status = 2 or status = 1)','addtime desc','10');
        $seo_title = '餐饮供应管理-会员中心-';
        $topname = '餐饮供应管理';
        $moduleidtype = 0;
        include template('food', 'mobile/'.$module);
        break;
}
?>