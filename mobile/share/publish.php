<?php
/**
 * 发布
 */
define('DT_REWRITE', true);
$moduleid = 6;
require '../../common.inc.php';
require DT_ROOT.'/include/post.func.php';
require DT_ROOT.'/include/module.func.php';
$moduleidtype = isset($moduleidtype)?$moduleidtype:0;
$isajax = isset($isajax)?true:false;
if(!$_userid){
    $isajax?exit(json_encode(array('status'=>'n','info'=>'请先登录'))):dalert('请先登录','/mobile/member/login.php');
}
$sitetitle = '';
switch($moduleidtype) {
    case 0: //餐饮供应
        require_once DT_ROOT.'/module/food/food.class.php';
        $obj = new food(23);
        $checkName = 'checkFood';
        $sitetitle .= '餐饮供应';
        break;
    case 1: //商家优惠
        require_once DT_ROOT.'/module/brand/brand.class.php';
        $obj = new brand(13);
        $checkName = 'checkJob';
        $sitetitle .= '商家优惠';
        break;
    case 2: //招聘信息
        require_once DT_ROOT.'/module/job/job.class.php';
        $obj = new job(9);
        $checkName = 'checkJob';
        $sitetitle .= '招聘信息';
        break;
    case 3: //店铺转让
        require_once DT_ROOT.'/module/sell/sell.class.php';
        $obj = new sell(5);
        $checkName = 'checkSell';
        $sitetitle .= '店铺转让';
        break;
    case 4: //名厨学堂 - 菜系
        require_once DT_ROOT.'/module/know/know.class.php';
        $obj = new know();
        $checkName = 'checkKnow';
        $sitetitle .= '名厨学堂-菜系';
        break;
    case 5: //分享
        require_once DT_ROOT.'/module/buy/buy.class.php';
        $obj = new buy(6);
        $checkName = 'checkBuy';
        $sitetitle .= '分享';
        break;
    case 6: //文章
        require_once DT_ROOT.'/module/article/article.class.php';
        $obj = new article();
        $checkName = 'checkUserEdit';
        $sitetitle .= '文章';
        break;
    default:
        exit;
        break;
}
$info = array();
$itemid = isset($itemid)?intval($itemid):0;
if($itemid){
    $obj->itemid=$itemid;
    $info = $obj->get_one();
    $check = is_can_edit($info);
    if($check!==true) $isajax?exit(json_encode(array('status'=>'n','info'=>$check))):dalert($check,$forward);
    $sitetitle = '信息编辑—'.$sitetitle;
}else{
    $sitetitle = '信息发布—'.$sitetitle;
}

if($submit){

    //如果开启了价格  就提示功能未实现
    /*if($DT['publishfee']!==0 || $DT['stickfee']!==0){
        exit(json_encode(array('status'=>'n','info'=>'功能未实现')));
    }*/

    require_once DT_ROOT.'/module/special/special.class.php';
    $oSpecial = new special(11);
    $oSpecial->table = $db->pre.'special';
    $oSpecial->table_data = $db->pre.'special_data';
    if(!check_token()) exit(json_encode(array('status'=>'n','info'=>'操作失效，请重试')));


    $arr = array(
        'title' => isset($title)?$title:'',
        'catid' => isset($catid)?$catid:'',
        'thumb' => isset($thumb)?$thumb:'',
        'telephone' => isset($mobile)?$mobile:'',
        'areaid' => isset($areaid)?$areaid:'',
        'address' => isset($address)?$address:'',
        'content' => isset($content)?$content:'',
        'userid'=>$_userid,
        'istop' => isset($istop) && $istop?1:0,  //置顶
        'status'=>2
    );
    $codeinfo = array();
    if(empty($itemid)){
        $arr['code'] = isset($code)?$code:'';
        if(!$codeinfo = $oSpecial->codeCheck($arr['code'])){
            exit(json_encode(array('status'=>'n','info'=>$oSpecial->errmsg)));
        }
    }



    switch($moduleidtype){
        case 0:
            //餐饮供应
            $arr['company'] = isset($company)?$company:'';
            $arr['price'] = isset($price)?$price:'';
            $arr['unit'] = isset($unit)?$unit:'';
            $arr['phone'] = $arr['telephone'];
            unset($arr['telephone']);
            break;
        case 1:
            //商家优惠
            $arr['company'] = isset($company)?$company:'';
            $arr['fromtime'] = isset($fromtime)?$fromtime:'';
            $arr['totime'] = isset($totime)?$totime:'';
            break;
        case 2: //招聘信息

            $arr['company'] = isset($company)?$company:'';
            $arr['total'] = isset($total)?$total:'';
            $arr['minsalary'] = isset($minsalary)?$minsalary:'';
            $arr['maxsalary'] = isset($maxsalary)?$maxsalary:'';
            $arr['type'] = isset($type)?$type:'';
            $arr['experience'] = isset($experience)?$experience:'';
            $arr['education'] = isset($education)?$education:'';
            $arr['experience'] = isset($experience)?$experience:'';
            break;
        case 3: //店铺转让

            $arr['price'] = isset($price)?$price:'';
            $arr['seat'] = isset($seat)?$seat:'';
            $arr['acreage'] = isset($acreage)?$acreage:'';
            break;
        case 4://名厨学堂
            break;
        case 5://分享
            unset($arr['mobile']);
            break;
        case 6:
            break;
        default:
            exit();
            break;
    }
    if(!$arr = $obj->pass($arr,false)) exit(json_encode(array('status'=>'n','info'=>$obj->errmsg)));
    if($itemid){
        $handle = $obj->edit($arr);
        $url = $beforeurl;
        $note = '修改成功';
    }else{
        $handle = $obj->add($arr);
        $url = 'aa';
        $itemid = $handle;
        $note = '提交成功，待审核...';
    }

    if($handle){
        if($codeinfo) $oSpecial->money($info,$codeinfo,$moduleidtype,$itemid);
        exit(json_encode(array('status'=>'y','info'=>$note,'url'=>$url)));
    }else{
        exit(json_encode(array('status'=>'n','info'=>'提交失败')));
    }
}else{
    $topname = $seo_title = $sitetitle;
    $aSetting9 = cache_read('module-9.php');
    $aSetting9['education'] = explode('|',$aSetting9['education']);
    $aSetting9['type'] = explode('|',$aSetting9['type']);
    $nav_selected = 'share';
    include template('publish'.$moduleidtype, 'mobile/share');
}

?>