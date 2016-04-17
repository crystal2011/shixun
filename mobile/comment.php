<?php
/**
 * 手机端-评论
 */
define('DT_REWRITE', true);
require '../common.inc.php';

//评论
require_once '../module/extend/comment.class.php';
$oComment = new comment;
$typeid = isset($typeid)?intval($typeid):0;
$id = isset($id)?intval($id):0;

list($list,$HasNextPage) = $oComment->commentList($id,$typeid,$page,15);
if($action=='ajax'){
    $info = '';
    if($list){
        foreach($list as $k=>$v){
            $info .= '<article class="comlist ">
                        <div class="comlistp clearfix">
                            <img class="comimage fl" src="'.($v['thumb']?$v['thumb']:$DT['memberlogo']).'"  >
                            <div class="cominfo">
                                <p class="comname">'.($v['truename']?$v['truename']:$v['username']).'</p>
                                <p  class="comtime">'.date('Y-m-d',$v['addtime']).'</p>
                            </div>
                        </div>
                        <div class="commentcon">'.$v['content'].'</div>
                    </article>';
        }
    }
    exit(json_encode(array('info'=>$info,'HasNextPage'=>$HasNextPage)));
}else if($action=='publish'){
    //发布评论显示
    $typeid = isset($typeid)?$typeid:0;
    $id = isset($id)?$id:0;
    if(empty($id)) dalert('操作失误',$forward);
    $url = '';
    switch($typeid){
        case 1: //餐饮供应
            require_once DT_ROOT.'/module/food/food.class.php';
            $obj = new food(23);
            $checkName = 'checkFood';
            $url = '/mobile/food/show.php?id=';
            break;
        case 2: //餐饮优惠
            require_once DT_ROOT.'/module/brand/brand.class.php';
            $obj = new brand(13);
            $checkName = 'checkJob';
            $url = '/mobile/discount/show.php?id=';
            break;
        case 3: //餐饮招聘
            require_once DT_ROOT.'/module/job/job.class.php';
            $obj = new job(9);
            $checkName = 'checkJob';
            $url = '/mobile/job/show.php?id=';
            break;
        case 4: //店铺转让
            require_once DT_ROOT.'/module/sell/sell.class.php';
            $obj = new sell(5);
            $checkName = 'checkSell';
            $url = '/mobile/cession/show.php?id=';
            break;
        case 5: //名厨学堂 - 菜系
            require_once DT_ROOT.'/module/know/know.class.php';
            $obj = new know;
            $checkName = 'checkKnow';
            $url = '/mobile/school/foodshow.php?id=';
            break;
        case 6: //分享
            require_once DT_ROOT.'/module/buy/buy.class.php';
            $obj = new buy(6);
            $checkName = 'checkBuy';
            $url = '/mobile/share/show.php?id=';
            break;
        case 7: //羊角会
            require_once DT_ROOT.'/module/info/info.class.php';
            $obj = new info(24);
            $obj->table = $db->pre.'info_24';
            $obj->table_data = $db->pre.'info_data_24';
            $checkName = 'checkInfo';
            $url = '/mobile/horn/show.php?id=';
            break;
        case 8: //羊角会成员
            require_once DT_ROOT.'/module/special/special.class.php';
            $obj = new special(11);
            $obj->table = $db->pre.'special';
            $obj->table_data = $db->pre.'special_data';
            $checkName = 'checkSpecial';
            $url = '/mobile/horn/hornshow.php?id=';
            break;
        case 9: //文章
            require_once DT_ROOT.'/module/article/article.class.php';
            $obj = new article(21);
            $checkName = 'checkArticle';
            $url = '/mobile/article/hornshow.php?id=';
            break;
        case 10: //求职信息
            require_once DT_ROOT.'/module/job/resume.class.php';
            $obj = new resume(9);
            $checkName = 'checkBuy';
            $url = '/mobile/job/resumeshow.php?id=';
            break;
        case 11: //名厨
            require_once DT_ROOT.'/module/member/member.class.php';
            $obj = new member();
            $checkName = 'checkKnow';
            $url = '/mobile/school/show.php?id=';
            break;
        default:
            dalert('操作失误',$forward);
            break;
    }
    if($typeid==11){
        $obj->userid = $id;
        $info = $obj->get_one();
        $info['content'] = $info['introduce'];
        $info['title'] = $info['truename'];
    }else{
        $obj->itemid = $id;
        $info = $obj->get_one();
    }


    $check = $obj->$checkName($info);
    if(!$check) dalert($obj->errmsg,$forward);

    $topname = '发表评论';
    $seo_title = $info['title'].'-发表评论-';
    include template('commentpublish','mobile');
}else {
    $topname = '评论';
    $seo_title = '评论-';
    include template('comment','mobile');
}

