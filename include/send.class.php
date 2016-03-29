<?php
defined('IN_DESTOON') or exit('Access Denied');
/**
	
	返回值：
	success:msgid								提交成功，发送状态请见4.1
	error:msgid								提交失败
	error:Missing username						用户名为空
	error:Missing password						密码为空
	error:Missing apikey						APIKEY为空
	error:Missing recipient					手机号码为空
	error:Missing message content				短信内容为空
	error:Account is blocked					帐号被禁用
	error:Unrecognized encoding				编码未能识别
	error:APIKEY or password error				APIKEY 或密码错误
	error:Unauthorized IP address				未授权 IP 地址
	error:Account balance is insufficient		余额不足
	error:Black keywords is:党中央				屏蔽词
*/


class Send {

	var $send_url = "http://121.199.16.178/webservice/sms.php?method=Submit";         //支付接口
	var $send_username = 'cf_gzmm';       //全部需要传递的参数
	var $send_password = '123shixun';   //安全校验码

    var $errmsg = errmsg;
    var $check_time=60;//发送间隔时间

	
	function sendSMS($mobile,$content)
	{
		$url = $this->send_url;
		$password = $this->send_password;
		$user_name = $this->send_username;
		$content = rawurlencode($content);
		$post_data = "account=".$user_name."&password=".$password."&mobile=".$mobile."&content=".$content;
		//密码可以使用明文密码或使用32位MD5加密

		$gets =  $this->xml_to_array($this->Post($post_data, $url));

		return $gets['SubmitResult'];
	}

	
	function Post($curlPost,$url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
	}
	
	function xml_to_array($xml){
		$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
				$subxml= $matches[2][$i];
				$key = $matches[1][$i];
				if(preg_match( $reg, $subxml )){
					$arr[$key] = $this->xml_to_array( $subxml );
				}else{
					$arr[$key] = $subxml;
				}
			}
		}
		
		return $arr;
	}

    /**
     * 注册、忘记密码、修改手机号
     * 发送短信 验证手机号  并发送短信
     */
    function check_mobile($mobile){

        GLOBAL $DT_TIME,$db,$DT_PRE,$DT_IP;
        $time = $DT_TIME;
        $day = strtotime(date('Y-m-d'));
        $content = '您的验证码是：[mobilecode]。请不要把验证码泄露给其他人。';

        //一个手机号码一天只能发10条
        $log_info = $db->get_one("select num,log_id from {$DT_PRE}send_log where mobile = '{$mobile}' and day = $day");
        if($log_info && $log_info['num']>=10){
            return '今天次数用完';
        }

        //发送短信
        $mobilecode = random(6, '0123456789');
        $content = str_replace('[mobilecode]',$mobilecode,$content);
        $send_arr = $this->sendSMS($mobile,$content);
        if($send_arr['code']!=2){
            return '发送失败';
        }

        $mobile_code  = md5($mobile.'|'.$mobilecode);
        if($log_info){
            $db->query("update {$DT_PRE}send_log set  day={$day},check_time={$time},num = num+1,mobile={$mobile},mobile_code='{$mobile_code}',check_num=0 where log_id = ".$log_info['log_id']."");
        }else{
            $db->query("insert into {$DT_PRE}send_log (ip,day,check_time,num,mobile,mobile_code)  values ('$DT_IP','$day','$time',1,'$mobile','$mobile_code')");
        }
        return true;
    }

    /**
     * 检测验证码
     * @param $code 用户输入的验证码
     * @param $real_ip 用户IP号
     * @param $mobile  手机号
     * @param $type 类型  1注册 2找回密码  3修改手机  4发布创业需求验证
     */
    function check_mobile_code($code,$real_ip,$mobile){
        GLOBAL $DT_TIME,$db,$DT_PRE;
        $day = strtotime(date('Y-m-d'));
        $send_log_info = $db->get_one("select check_time,mobile_code,check_num from {$DT_PRE}send_log where day = '{$day}' and mobile='{$mobile}'");
        $time = $DT_TIME;
        if(empty($code)  || !preg_match("/^[0-9]{6}$/", $code)){
            return $this->_('请填写正确的验证码');
        }else if(!$send_log_info){
            return $this->_('验证码已失效');
        }else if($send_log_info['mobile_code'] != md5($mobile.'|'.$code)){
            return $this->_('验证码不正确');
        }else if($time - $send_log_info['check_time']>1800){
            return $this->_('验证码已过期');
        }else if($send_log_info['check_num']==3){
            return $this->_('验证码已失效');
        }else if($send_log_info['check_time']==0){
            return $this->_('验证码已失效');
        }else{
            return $send_log_info;
        }
    }

    function _($e) {
        $this->errmsg = $e;
        return false;
    }

    /**
     * 实时检测验证码  错误次数累加
     */
    function code_check($type){
        global $db,$DT_TIME,$DT_IP,$DT_PRE,$mobile,$code;
        $type = $type ? $type : 1;
        $mobile = isset($mobile) ? $mobile : '';
        $code = isset($code) ? $code : '';
        $mobile_code = md5($mobile . '|' . $code);
        $send_log_info = $db->get_one("select mobile_code,log_id,check_time,check_num from {$DT_PRE}send_log where mobile='{$mobile}' and ip = '{$DT_IP}' and type = " . $type);
        if ($send_log_info) {
            if ($send_log_info['check_num'] == 3) {
                return $this->_('验证码已失效');
            }
            if ($DT_TIME - $send_log_info['check_time'] > 1800) {
                $db->query("update {$DT_PRE}send_log set check_num = check_num + 1 where log_id = " . $send_log_info['log_id'] . "");
                return $this->_('验证码已失效');
            }
            if ($send_log_info['mobile_code'] != $mobile_code) {
                $db->query("update {$DT_PRE}send_log set check_num = check_num + 1 where log_id = " . $send_log_info['log_id'] . "");
                return $this->_('验证码不正确');
            }
            return true;
        }
        return $this->_('验证码不正确');
    }


}
?>