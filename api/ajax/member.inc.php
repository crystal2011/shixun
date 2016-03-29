<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

isset($value) or $value = '';
$value = convert($value, 'UTF-8', DT_CHARSET);
require MD_ROOT.'/member.class.php';
$do = new member;

if(isset($userid) && $userid) $do->userid = $userid;
switch($job) {
    case 'username':
        $value = isset($param)?$param:'';
        if(!$do->is_username($value)) exit(json_encode(array('info'=>$do->errmsg,'status'=>'n')));
        exit(json_encode(array('info'=>'用户名可用','status'=>'y')));
        break;
    case 'passport':
        if(!$value) exit;
        if($_userid) $do->userid = $_userid;
        if(!$do->is_passport($value)) exit($do->errmsg);
        break;
    case 'password':
        //验证用户密码
        $value = trim(dhtmlspecialchars($param));
        if(!$do->is_password($value, $value)){
            exit(json_encode(array('status'=>'n','info'=>$do->errmsg)));
        }
        exit(json_encode(array('status'=>'y')));
        break;
    case 'payword':
        if(!$do->is_payword($value, $value)) exit($do->errmsg);
        break;
    case 'email':
        $value = trim($value);
        if(!$do->is_email($value)) exit($do->errmsg);
        if($do->email_exists($value)) exit($L['member_email_reg']);
        break;
    case 'emailcode':
        $value = trim($value);
        if(!preg_match("/[0-9]{6}/", $value)) exit('&nbsp;');
        $session = new dsession();
        if($_SESSION['email_code'] != md5($_SESSION['email'].'|'.$value)) exit('&nbsp;');
        break;
    case 'mobile':
        //手机验证  判断手机是否存在
        $value = trim(dhtmlspecialchars($param));
        $must_exists = isset($must_exists)?true:false;
        if(!is_mobile($value)) exit(json_encode(array('status'=>'n','info'=>$L['member_mobile_null'])));
        if($must_exists){
            if(!$do->mobile_exists($value)) exit(json_encode(array('status'=>'n','info'=>'手机不存在')));
        }else{
            if($do->mobile_exists($value)) exit(json_encode(array('status'=>'n','info'=>$L['member_mobile_reg'])));
        }
        exit(json_encode(array('status'=>'y')));
        break;
    case 'mobilecode':
        $value = trim($value);
        if(!preg_match("/[0-9]{6}/", $value)) exit('&nbsp;');
        $session = new dsession();
        if($_SESSION['mobile_code'] != md5($_SESSION['mobile'].'|'.$value)) exit('&nbsp;');
        break;
    case 'company':
        if(!$value) exit($L['member_company_null']);
        if(!$do->is_company($value)) exit($L['member_company_reg']);
        if($do->company_exists($value)) exit($L['member_company_reg']);
        break;
    case 'get_company':
        $user = $do->get_one($value);
        if($user) {
            echo '<a href="'.$user['linkurl'].'" target="_blank" class="t">'.$user['company'].'</a>'.( $user['vip'] ? ' <img src="'.DT_SKIN.'image/vip.gif" align="absmiddle"/> <img src="'.DT_SKIN.'image/vip_'.$user['vip'].'.gif" align="absmiddle"/>' : '');
        } else {
            echo '1';
        }
        exit;
        break;
    case 'sendmobile':
        //发送短信
        $session = new dsession();
        $type = isset($type) && intval($type)?intval($type):1;
        if(!check_token()) exit(json_encode(array('status'=>'n','info'=>'登录失效，请重试')));
        if($type==3){
            if (!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        }else{
            if ($_userid) exit(json_encode(array('status'=>'n','info'=>'您已登录')));
        }
        $mobile = isset($mobile) ? $mobile : '';
        require_once DT_ROOT.'/include/send.class.php';
        $sends = new send;
        if(!is_mobile($mobile)) exit(json_encode(array('status'=>'n','info'=>$L['member_mobile_null'])));
        if($type==1 || $type==3){
            if($do->mobile_exists($mobile)) exit(json_encode(array('status'=>'n','info'=>$L['member_mobile_reg'])));
        }else{
            if(!$do->mobile_exists($mobile)) exit(json_encode(array('status'=>'n','info'=>'手机号不存在')));
        }
        $send_info = $sends->check_mobile($mobile);
        if($send_info!==true){
            exit(json_encode(array('status'=>'n','info'=>$send_info)));
        }else{
            exit(json_encode(array('status'=>'y','info'=>"发送成功")));
        }
        break;
    case 'memberlogo':
        if($_userid){
            $logo = isset($logo)?$logo:'';
            $db->query("update {$DT_PRE}member set thumb = '{$logo}' where userid = ".$_userid);
            clear_upload($logo);
        }
        break;
    case 'saveinfo':
        if (!$_userid) exit(json_encode(array('status'=>'n','info'=>'请先登录')));
        $field = isset($field)?$field:'';
        switch($field){
            case 'gender':
                $add = array('gender'=>isset($gender)&&$gender==1?1:2);
                break;
            case 'mobile':
                $mobile = isset($mobile)?$mobile:'';
                $code = isset($code)?$code:'';
                //手机验证码检测
                if(!check_token()) exit(json_encode(array('status'=>'n','info'=>'注册失效，请重试')));
                require_once DT_ROOT.'/include/send.class.php';
                $send = new send;
                if(!$send->check_mobile_code($code,$DT_IP,$mobile)){
                    exit(json_encode(array('status' => 'n', 'info' => $send->errmsg)));
                }
                $add = array('mobile'=>$mobile,'userid'=>$_userid);

                break;
            case 'address':
                $areaid = isset($areaid)?intval($areaid):0;
                $address = isset($address)?$address:'';
                if(empty($areaid) || !get_area($areaid)) exit(json_encode(array('status'=>'n','info'=>'请选择地区')));
                if(mb_strlen($address,'utf-8')==0) exit(json_encode(array('status'=>'n','info'=>'请填写地址')));
                $add = array('areaid'=>$areaid,'address'=>$address);
                break;
            case 'truename':
                $truename = isset($truename)?$truename:'';
                if(!is_length($truename,2,5)) exit(json_encode(array('status'=>'n','info'=>'真实姓名输入有误')));
                $add = array('truename'=>$truename);
                break;
            case 'introduce':
                $introduce = isset($introduce)?$introduce:'';
                if(!is_length($introduce,5,200)) exit(json_encode(array('status'=>'n','info'=>'个人简介输入有误')));
                $add = array('introduce'=>$introduce);
                break;
            case 'username':
                $username = isset($username)?$username:'';
                if(!$do->is_username($username)) exit(json_encode(array('info'=>$do->errmsg,'status'=>'n')));
                $add = array('username'=>$username);
                break;
            default:
                exit(json_encode(array('status'=>'n','info'=>'操作有误')));
                break;
        }

        $post = dhtmlspecialchars($add);
        $sql = '';
        foreach($post as $k=>$v) {
            $sql .= ",$k='$v'";
        }
        $sql = substr($sql, 1);
        $db->query("UPDATE {$db->pre}member SET $sql WHERE userid=".$_userid);
        exit(json_encode(array('status'=>'y','info'=>'提交成功')));
        break;
}
?>