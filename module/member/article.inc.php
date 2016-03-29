<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

require_once '../module/article/article.class.php';
$oArticle = new article();
$aCatCache = cache_read('category-21.php');

$action = isset($action)?$action:'';
switch($action){
    case 'del':
        if(!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $itemid = isset($itemid)?intval($itemid):0;
        if(empty($itemid)) exit(json_encode(array('status'=>'n','info'=>'非法操作')));
        $oArticle->itemid = $itemid;
        $aArticle = $oArticle->get_one();
        if(!$aArticle || $aArticle['userid']!=$_userid) exit(json_encode(array('status'=>'n','info'=>'信息已失效')));
        $oArticle->recycle($itemid);
        exit(json_encode(array('status'=>'y','info'=>'删除成功')));
        break;
    default:

        if(!$_userid) dheader('/member/login.php');
        $where = ' userid = '.$_userid;
        list($aArticle,$totalpage) = $oArticle->articleList('thumb,title,itemid,catid,status',$where.' and (status=3 or status = 2 or status = 1) ','hits desc','10');
        $active = 'article';
        $seo_title = '文章管理-会员中心-';
        $nav_selected='member';
        include template('article', $module);
        break;
}
?>