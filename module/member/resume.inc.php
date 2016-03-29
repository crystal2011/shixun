<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
require_once '../module/job/resume.class.php';
$obj = new resume(9);

$action = isset($action)?$action:'';
switch($action){
    case 'add':
        if(!$_userid) dalert('请先登录','/member/resume.php');

        $post = array(
            'userid'=>$_userid,
            'catid'=>isset($catid)?$catid:'',
            'truename'=>isset($truename)?$truename:'',
            'telephone'=>isset($telephone)?$telephone:'',
            'email'=>isset($email)?$email:'',
            'thumb'=>isset($thumb)?$thumb:'',
            'gender'=>isset($gender)?$gender:'',
            'marriage'=>isset($marriage)?$marriage:'',
            'areaid'=>isset($areaid)?$areaid:'',
            'address'=>isset($address)?$address:'',
            'byear'=>isset($byear)?$byear:'',
            'bmonth'=>isset($bmonth)?$bmonth:'',
            'bday'=>isset($bday)?$bday:'',
            'height'=>isset($height)?$height:'',
            'weight'=>isset($weight)?$weight:'',
            'education'=>isset($education)?$education:'',
            'school'=>isset($school)?$school:'',
            'major'=>isset($major)?$major:'',
            'language'=>isset($language)?$language:'',
            'type'=>isset($type)?$type:'',
            'skill'=>isset($skill)?$skill:'',
            'minsalary'=>isset($minsalary)?$minsalary:'',
            'maxsalary'=>isset($maxsalary)?$maxsalary:'',
            'experience'=>isset($experience)?$experience:'',
            'situation'=>isset($situation)?$situation:'',
            'content'=>isset($content)?$content:'',
        );

        $info = $obj->get_one('userid = '.$_userid.' and status = 3');
        if($info){
            $obj->itemid = $info['itemid'];
        }

        if(!$post = $obj->pass($post,false)) dalert($obj->errmsg,'/member/resume.php');
        if($info){

            $obj->edit($post);
        }else{
            $obj->add($post);
        }
        dalert('提交成功','/member/resume.php');
        break;
    default:
        if(!$_userid) dheader('/member/login.php');
        $where = ' userid = '.$_userid;
        $info = $obj->get_one('userid = '.$_userid.' and status = 3');
        if($info) list($info['byear'], $info['bmonth'], $info['bday']) = explode('-', $info['birthday']);
        $active = 'resume';
        $seo_title = '简历-会员中心-';
        $aSetting9 = cache_read('module-9.php');
        $aSetting9['education'] = explode('|',$aSetting9['education']);
        $aSetting9['type'] = explode('|',$aSetting9['type']);
        $aSetting9['marriage'] = explode('|',$aSetting9['marriage']);
        $aSetting9['situation'] = explode('|',$aSetting9['situation']);
        $nav_selected='member';
        include template('resume', $module);
        break;
}
?>