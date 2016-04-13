<?php
defined('IN_DESTOON') or exit('Access Denied');
$at = isset($at)?$at:'';
switch($at){
    case 'gettoken':
        //获取token
        set_token();
        break;
    case 'islogin':
        exit(json_encode(array('status'=>$_userid?'y':'n')));
        break;
    case 'morecomment':
        //获取更多评论
        require_once DT_ROOT.'/module/extend/comment.class.php';
        $oComment = new comment;
        $typeid = isset($type)?intval($type):0;
        $id = isset($id)?intval($id):0;
        $page = isset($page)?intval($page):1;
        list($aComment,$HasNextPage) = $oComment->commentList($id,$typeid,$page);
        exit(json_encode(array('info'=>$aComment,'HasNextPage'=>$HasNextPage)));
        break;
    case 'vote':
        //投票
        $type = isset($type)?$type:0;
        $id = isset($id)?$id:0;
        if(empty($id)) exit(json_encode(array('status'=>'n','info'=>'操作失误')));
        if(check_token()===false) exit(json_encode(array('status'=>'n','info'=>'操作失误,请重试')));
        if(empty($_userid)) exit(json_encode(array('status'=>'n','info'=>'请登录后再投票')));

        //检测是否该会员在24小时内是否有点击
        require_once DT_ROOT.'/module/extend/vote.class.php';
        $oVote = new vote();
        if(!$oVote->isVoted($id,$type)) exit(json_encode(array('status'=>'n','info'=>$oVote->errmsg)));

        switch($type){
            case 1: //名厨
                /*require_once DT_ROOT.'/module/know/know.class.php';
                $obj = new know(10);
                $checkName = 'checkKnow';*/
                exit;
                break;
            case 2: //菜系
                require_once DT_ROOT.'/module/know/know.class.php';
                $obj = new know;
                $checkName = 'checkKnow';
                break;
            default:
                exit(json_encode(array('status'=>'n','info'=>'操作失误')));
                break;
        }
        $obj->itemid = $id;
        $info = $obj->get_one();
        $check = $obj->$checkName($info);
        if(!$check) exit(json_encode(array('status'=>'n','info'=>$obj->errmsg)));
        $obj->addVote();
        $oVote->addVote($id,$type,$info['userid']);
        exit(json_encode(array('status'=>'y')));
        break;
    case 'like':
        //点击喜欢量
        $type = isset($type)?$type:0;
        $id = isset($id)?$id:0;
        if(empty($id)) exit(json_encode(array('status'=>'n','info'=>'操作失误')));
        if(check_token()===false) exit(json_encode(array('status'=>'n','info'=>'操作失误,请重试')));
            //检测是否该IP在一个小时内是否有点击

        require_once DT_ROOT.'/module/like/like.class.php';
        $oLike = new like();
        if(!$oLike->isLiked($id,$type)) exit(json_encode(array('status'=>'n','info'=>$oLike->errmsg)));
        switch($type){
            case 1: //餐饮供应
                require_once DT_ROOT.'/module/food/food.class.php';
                $obj = new food(23);
                $checkName = 'checkFood';
                break;
            case 2: //商家优惠
                require_once DT_ROOT.'/module/brand/brand.class.php';
                $obj = new brand(13);
                $checkName = 'checkJob';
                break;
            case 3: //招聘信息
                require_once DT_ROOT.'/module/job/job.class.php';
                $obj = new job(9);
                $checkName = 'checkJob';
                break;
            case 4: //店铺转让
                require_once DT_ROOT.'/module/sell/sell.class.php';
                $obj = new sell(5);
                $checkName = 'checkSell';
                break;
            case 5: //名厨学堂 - 菜系
                require_once DT_ROOT.'/module/know/know.class.php';
                $obj = new know;
                $checkName = 'checkKnow';
                break;
            case 6: //分享
                require_once DT_ROOT.'/module/buy/buy.class.php';
                $obj = new buy(6);
                $checkName = 'checkBuy';
                break;
            default:
                exit(json_encode(array('status'=>'n','info'=>'操作失误')));
                break;
        }
        $obj->itemid = $id;
        $check = $obj->$checkName($obj->get_one());
        if(!$check) exit(json_encode(array('status'=>'n','info'=>$obj->errmsg)));
        $obj->addLike();
        $oLike->add($id,$type);
        exit(json_encode(array('status'=>'y','info'=>'发表成功')));
        break;
    case 'comment':
        //评论
        $type = isset($type)?$type:0;
        $id = isset($id)?$id:0;
        $content = isset($content)?dhtmlspecialchars($content):'';
        if(mb_strlen($content)>200 || mb_strlen($content)<5) exit(json_encode(array('status'=>'n','info'=>'请输入5-200个字')));

        if(empty($id)) exit(json_encode(array('status'=>'n','info'=>'操作失误')));
        if(empty($_userid)) exit(json_encode(array('status'=>'n','info'=>'请登录后再评论')));
        if(check_token()===false) exit(json_encode(array('status'=>'n','info'=>'操作失误,请重试')));
        //检测是否该会员是否在一个小时内是否有评论
        require_once DT_ROOT.'/module/extend/comment.class.php';
        $oComment = new comment();
        if(!$oComment->isCommentd($id,$type)) exit(json_encode(array('status'=>'n','info'=>$oComment->errmsg)));
        switch($type) {
            case 1: //餐饮供应
                require_once DT_ROOT.'/module/food/food.class.php';
                $obj = new food(23);
                $checkName = 'checkFood';
                break;
            case 2: //商家优惠
                require_once DT_ROOT.'/module/brand/brand.class.php';
                $obj = new brand(13);
                $checkName = 'checkJob';
                break;
            case 3: //招聘信息
                require_once DT_ROOT.'/module/job/job.class.php';
                $obj = new job(9);
                $checkName = 'checkJob';
                break;
            case 4: //店铺转让
                require_once DT_ROOT.'/module/sell/sell.class.php';
                $obj = new sell(5);
                $checkName = 'checkSell';
                break;
            case 5: //名厨学堂 - 菜系
                require_once DT_ROOT.'/module/know/know.class.php';
                $obj = new know;
                $checkName = 'checkKnow';
                break;
            case 6: //分享
                require_once DT_ROOT.'/module/buy/buy.class.php';
                $obj = new buy(6);
                $checkName = 'checkBuy';
                break;
            case 7: //羊角会
                require_once DT_ROOT.'/module/info/info.class.php';
                $obj = new info(24);
                $obj->table = $db->pre.'info_24';
                $obj->table_data = $db->pre.'info_data_24';
                $checkName = 'checkInfo';
                break;
            case 8: //羊角会成员
                require_once DT_ROOT.'/module/special/special.class.php';
                $obj = new special(11);
                $obj->table = $db->pre.'special';
                $obj->table_data = $db->pre.'special_data';
                $checkName = 'checkSpecial';
                break;
            case 9: //文章
                require_once DT_ROOT.'/module/article/article.class.php';
                $obj = new article(21);
                $checkName = 'checkArticle';
                break;
            default:
                exit(json_encode(array('status'=>'n','info'=>'操作失误')));
                break;
        }
        $obj->itemid = $id;
        $check = $obj->$checkName($obj->get_one());
        if(!$check) exit(json_encode(array('status'=>'n','info'=>$obj->errmsg)));
        $oComment->addComment($id,$type,$content);
        if(!$DT['commentscheck']){
            exit(json_encode(array('status'=>'y','info'=>'评论成功，待审核')));
        }else{
            $obj->adddelComments('add');
            exit(json_encode(array('status'=>'y','info'=>'评论成功')));
        }

        break;
    case 'horncode':
        //羊角会编号检测
        $code = isset($param)?$param:'';
        $type = isset($type)?$type:'';
        require_once DT_ROOT.'/module/special/special.class.php';
        $oSpecial = new special(11);
        $oSpecial->table = $db->pre.'special';
        $oSpecial->table_data = $db->pre.'special_data';
        if(!$codeinfo = $oSpecial->codeCheck($code)){
            exit(json_encode(array('status'=>'n','info'=>$oSpecial->errmsg)));
        }
        if(!in_array($type,array(0,1,2,3,4,5,6))) exit(json_encode(array('status'=>'n','info'=>'编号有误')));
        $fee = $oSpecial->fee($type);
        list($allmoney,$discountfee,$feedfs) = $oSpecial->moneyji($fee,$codeinfo['discount']);
        $info = $allmoney?'发布价格：'.$allmoney.'元':'';
        $info .= $allmoney != $feedfs?'，发布优惠价：'.$feedfs.'元':'';
        exit(json_encode(array('status'=>'y','info'=>$info)));
        break;
    case 'category':
        $moduleid = isset($moduleid)?$moduleid:0;
        $catid = isset($catid)?$catid:0;
        $url = isset($url)?$url:'';
        $type = isset($type)?$type:'';
        if($moduleid==21){
            $list = array(
                array('catid'=>22,'catname'=>'饮食推荐','child'=>0),
            array('catid'=>32,'catname'=>'美食资讯','child'=>0),
            );
        }else{
            $list = get_maincat($catid,$moduleid);
        }


        $info = '<header style="border-bottom:1px solid #e6e6e6;">
                    <article id="header1" class="clear" style="background-color:#e9544e;text-align:center;color:#fff;font-size:1.6em;">
                        信息发布—选择目录
                    </article>
                </header>';
        if($list){
            $info .= '<div style="margin-top:20px;background-color:#fff;height:50px;">&nbsp;</div><table id="catmontable" style="width:100%;border-top:1px solid #d5d5d5;background-color:#fff;">';
            foreach($list as $k=>$v){
                $str = '';
                if($v['child']){
                    $lists = get_maincat($v['catid'],$moduleid);
                    $str .= '<select onchange="catonchange(this)" style="position:absolute;width:100%;height:100%;filter: alpha(opacity=0);-khtml-opacity: 0;opacity: 0;top:0px;left:0px;"><option value="'.$v['catid'].'" data-name="'.$v['catname'].'">'.$v['catname'].'</option>';
                    foreach($lists as $key=>$val){
                        $str .= '<option value="'.$val['catid'].'">'.$val['catname'].'</option>';
                    }
                    $str .= '</select><img src="'.DT_SKIN.'image/smalljiao.png" style="position:absolute;bottom:2px;right:2px;" />';
                }
                if(($k+1)%2!=0) $info .= '<tr><td style="border-right:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;">&nbsp;</td>';
                $info .= '<td  style="border-right:1px solid #d5d5d5;text-align:center;width:150px;font-size:1.4em;line-height:2.2em;border-bottom:1px solid #d5d5d5;position:relative;" onclick="lfdtd(this)"><span style="display:block;" data-id="'.$v['catid'].'" olddata-id="'.$v['catid'].'">'.$v['catname'].'</span>'.$str.'</td>';
                if(($k+1)%2==0) $info .= '<td style="border-bottom:1px solid #d5d5d5;">&nbsp;</td></tr>';
            }
            if(count($list)%2!=0){
                $info .= '<td data-id="'.$v['catid'].'" style="border-right:1px solid #d5d5d5;width:150px;border-bottom:1px solid #d5d5d5;">&nbsp;</td><td style="border-bottom:1px solid #d5d5d5;">&nbsp;</td></tr>';
            }
            $info .= '</table><div style="display:none;" id="selectcatnow"></div><div style="background-color:#fff;height:50px;">&nbsp;</div>';
            $info .= '<p style="text-align:center;margin-top:100px;"><span onclick="publishsuresdfs('.$type.',\''.$url.'\')" style="padding:8px 20px;font-size:1.4em;background-color:#ea554f;color:#fff;margin-right:10px;">确认</span> <span onclick="publishallsdfs('.$type.',\''.$url.'\')" style="padding:8px 20px;font-size:1.4em;background-color:#000;color:#fff;margin-right:10px;">全部</span> <span onclick="publishquitsdfs('.$type.',\''.$url.'\')" style="padding:8px 20px;font-size:1.4em;background-color:#03b887;color:#fff;">取消</span> </p>';
        }
        $info .= '<div style="height:150px;">&nbsp;</div>';
        exit(json_encode(array('status'=>'y','info'=>$info)));
        break;
}