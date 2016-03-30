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

list($list,$HasNextPage) = $oComment->commentList($id,$typeid,$page);
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
}else {
    $topname = '评论';
    $seo_title = '评论-';
    include template('comment','mobile');
}

