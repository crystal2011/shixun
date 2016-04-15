<?php
/**
 * 发布
 */
define('DT_REWRITE', true);
$moduleid = 6;
require '../../common.inc.php';
require DT_ROOT.'/include/post.func.php';
require DT_ROOT.'/include/module.func.php';
$moduleidtype = isset($moduleidtype)?$moduleidtype:'';
require_once DT_ROOT.'/module/special/special.class.php';
$oSpecial = new special(11);
$oSpecial->table = $db->pre.'special';
$oSpecial->table_data = $db->pre.'special_data';
$isajax = isset($isajax)?true:false;
if(!$_userid){
    $isajax?exit(json_encode(array('status'=>'n','info'=>'请先登录'))):dalert('请先登录','/mobile/member/login.php');
}

$sitetitle = '';
switch($moduleidtype) {
    case '0': //餐饮供应
        require_once DT_ROOT.'/module/food/food.class.php';
        $obj = new food(23);
        $checkName = 'checkFood';
        $sitetitle .= '餐饮供应';
        $moduleidcat = 23;
        break;
    case 1: //商家优惠
        require_once DT_ROOT.'/module/brand/brand.class.php';
        $obj = new brand(13);
        $checkName = 'checkJob';
        $sitetitle .= '商家优惠';
        $moduleidcat = 13;
        break;
    case 2: //招聘信息
        require_once DT_ROOT.'/module/job/job.class.php';
        $obj = new job(9);
        $checkName = 'checkJob';
        $sitetitle .= '招聘信息';
        $moduleidcat = 9;
        $isjob = true;
        break;
    case 3: //店铺转让
        require_once DT_ROOT.'/module/sell/sell.class.php';
        $obj = new sell(5);
        $checkName = 'checkSell';
        $sitetitle .= '店铺转让';
        $moduleidcat = 13;
        break;
    case 4: //名厨学堂 - 菜系
        require_once DT_ROOT.'/module/know/know.class.php';
        $obj = new know();
        $checkName = 'checkKnow';
        $sitetitle .= '名厨学堂';
        $moduleidcat = 13;
        break;
    case 5: //分享
        require_once DT_ROOT.'/module/buy/buy.class.php';
        $obj = new buy(6);
        $checkName = 'checkBuy';
        $sitetitle .= '美食分享';
        break;
    case 6: //文章
        require_once DT_ROOT.'/module/article/article.class.php';
        $obj = new article();
        $checkName = 'checkUserEdit';
        $sitetitle .= '美食文章';
        $moduleidcat = 21;
        break;
    default:
        dheader('/mobile/member/index.php');
        break;
}

$info = array();
$itemid = isset($itemid)?intval($itemid):0;
if($itemid){
    $obj->itemid=$itemid;
    $info = $obj->get_one();
    $check = is_can_edit($info);
    if($check!==true) $isajax?exit(json_encode(array('status'=>'n','info'=>$check))):dalert($check,$forward);
    $thumbinputarr = array();
    if($info['content']){
        preg_match("/<div.*class=\"womdnlsandh\".*>(.*)<\\/div>/iU",$info['content'],$msd);
        if(isset($msd[1])){
            preg_match_all("/<img.*src=\"(.*)\".*>/iU",$msd[1],$dgd);
            if(isset($dgd[1])){
                $thumbinputarr = $dgd[1];
            }
        }
        $info['content'] = preg_replace("/<div.*class=\"womdnlsandh\">(.*)<\\/div>/iU","$2",$info['content']);
    }

    $sitetitle = '信息编辑—'.$sitetitle;
}else{
    $code = isset($code)?$code:'';
    $codeinfo = array();
    if(strlen($code)==0 || !$codeinfo = $oSpecial->codeCheck($code)){
        $isajax?exit(json_encode(array('status'=>'n','info'=>$oSpecial->errmsg))):dheader($CFG['url'].'mobile/member/publish.php?moduleidtype='.$moduleidtype);
    }
    $sitetitle = '信息发布—'.$sitetitle;
}





if($isajax){

    //如果开启了价格  就提示功能未实现
    /*if($DT['publishfee']!==0 || $DT['stickfee']!==0){
        exit(json_encode(array('status'=>'n','info'=>'功能未实现')));
    }*/


    if(!check_token()) exit(json_encode(array('status'=>'n','info'=>'操作失效，请重试')));

    $thumbinput = isset($thumbinput) && is_array($thumbinput)?$thumbinput:array();
    $contentimage = '';

    if($thumbinput){
        $contentimage .= '<div class="womdnlsandh">';
        foreach($thumbinput as $k=>$v){
            if($v && is_image($v)){
                $contentimage .= dstripslashes('<img src="'.$v.'" style="display:block;margin:0 auto;" />');
            }
        }
        $contentimage .= '</div>';
    }

    $arr = array(
        'title' => isset($title)?$title:'',
        'catid' => isset($catid)?$catid:'',
        'thumb' => isset($thumb)?$thumb:'',
        'telephone' => isset($mobile)?$mobile:'',
        'areaid' => isset($areaid)?$areaid:'',
        'address' => isset($address)?$address:'',
        'content' => $contentimage.(isset($content)?$content:''),
        'userid'=>$_userid,
        'istop' => isset($istop) && $istop?1:0,  //置顶
        'status'=>2
    );


    if(empty($itemid)){
        $arr['code'] = $code;
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
            $arr['coofee'] = isset($coofee)?$coofee:'';
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
        if(isset($codeinfo) && $codeinfo) $oSpecial->money($info,$codeinfo,$moduleidtype,$itemid);
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