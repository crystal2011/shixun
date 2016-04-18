<?php
/**
 * 手机端-文章模块-文章详情
 */
define('DT_REWRITE', true);
require '../../common.inc.php';
require_once '../../module/article/article.class.php';
$oArticle = new article(21);
$isajax = isset($isajax)?true:false;
if($isajax){  //用户发布、编辑预览
    $_POST = strip_sql($_POST);
    strip_key($_POST);
    require_once '../../include/post.func.php';
    require_once '../../include/module.func.php';
    $_POST['content'] = stripslashes($_POST['content']);

    $thumbinput = isset($_POST['thumbinput']) && is_array($_POST['thumbinput'])?$_POST['thumbinput']:array();
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

    $_POST['content'] = save_local($contentimage.$_POST['content']);
    $_POST['content'] = clear_link($_POST['content']);
    $_POST['introduce'] = addslashes(get_intro($_POST['content'], 200));
    $content = $_POST['content'];
    unset($_POST['content']);
    $_POST = dhtmlspecialchars($_POST);
    $_POST['content'] = dsafe($content);
    $info = $_POST;
    $codetishicode = $info['code'];
    $id = $info['likes'] = $info['hits'] = $info['comments'] = 0;
    $info['addtime'] = $DT_TIME;
    $seo_title = $info['title'].'-文章详情-预览';
    $topname = '文章详情-预览';
    $backurl = 'javascript:history.back();';
}else{
    $id = isset($id)?intval($id):0;
    if(empty($id)){
        dalert('非法操作','/mobile/article/index.php');
    }

    $oArticle->itemid = $id;
    $info= $oArticle->get_one();

    if(isset($showtype)){   //预览
        $s = chcekHorn($info,6);
        if($s!==true) dalert($s,$forward);
        $backurl = $forward;
    }else{
        if(!$oArticle->checkArticle($info)){
            dalert($oArticle->errmsg,'/mobile/article/list.php');
        }
        //更新浏览量
        $oArticle->editHits();
        addHits($info['userid']);
        $backurl = '/mobile/article/index.php';
    }
    //当时发布的code
    require_once '../../module/special/special.class.php';
    $oSpecial = new special(11);
    $codetishicode = $oSpecial->getShowCode($id,6);
    $seo_title = $info['title'].'-文章详情-';
    $topname = '文章详情';

}

$islikenum = true;
$aRecommendFood = $oArticle->getright('title,itemid,introduce,hits,thumb,addtime,comments',4,'addtime desc');  //推荐

$commenttypeid = 9;
$moduleidtype = 6;

include template('article/show','mobile');