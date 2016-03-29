<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!isset($dialog)) show_menu($menus);
?>
<div class="tt">会员资料</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td rowspan="9" align="center" width="160" class="f_gray">
<img src="<?php echo useravatar($username, 'large');?>" width="128" height="128"/>

<div style="padding:2px 0 2px 0;">
<a href="?moduleid=<?php echo $moduleid;?>&action=move&groupids=2&userid=<?php echo $userid;?>" class="t"<?php if(isset($dialog)) {?> target="_blank"<?php } ?> onclick="return confirm('确定要禁止此会员访问吗？');">禁止访问</a> | 
<a href="?moduleid=<?php echo $moduleid;?>&action=delete&userid=<?php echo $userid;?>&forward=<?php echo urlencode('?moduleid='.$moduleid);?>" class="t"<?php if(isset($dialog)) {?> target="_blank"<?php } ?> onclick="return confirm('确定要删除此会员吗？系统将删除选中用户所有信息，此操作将不可撤销');">删除会员</a><br/>
</div>

<?php if($DT['im_web']) { ?><?php echo im_web($username);?> <?php } ?>
<a href="javascript:Dwidget('?moduleid=2&file=sendmail&email=<?php echo $email;?>', '发送邮件');"><img width="16" height="16" src="<?php echo DT_SKIN;?>image/email.gif" title="发送邮件 <?php echo $email;?>" align="absmiddle"/></a> 
<?php if($mobile) { ?><a href="javascript:Dwidget('?moduleid=2&file=sendsms&mobile=<?php echo $mobile;?>', '发送短信');"><img src="<?php echo DT_SKIN;?>image/mobile.gif" title="发送短信" align="absmiddle"/></a> <?php } ?>
<a href="javascript:Dwidget('?moduleid=2&file=message&action=send&touser=<?php echo $username;?>', '发送消息');"><img width="16" height="16" src="<?php echo DT_SKIN;?>image/msg.gif" title="发送消息" align="absmiddle"/></a>




</td>
<td class="tl">会员名</td>
<td>&nbsp;<?php echo $username;?>
[<?php $ol = online($userid);if($ol == 1) { ?><span class="f_red">在线</span><?php } else if($ol == -1) { ?><span class="f_blue">隐身</span><?php } else { ?><span class="f_gray">离线</span><?php } ?>]
</td>
<td class="tl">会员ID</td>
<td>&nbsp;<?php echo $userid;?>&nbsp;&nbsp;

</tr>
<tr>

<td class="tl">会员组</td>
<td class="f_red">&nbsp;<?php echo $GROUP[$groupid]['groupname'];?></td>
</tr>
<tr>

<td class="tl">登录次数</td>
<td>&nbsp;<?php echo $logintimes;?></td>
</tr>

<tr>
<td class="tl">上次登录</td>
<td>&nbsp;<?php echo timetodate($logintime, 6);?></td>
<td class="tl">登录IP</td>
<td>&nbsp;<?php echo $loginip;?> - <?php echo ip2area($loginip);?></td>
</tr>
<tr>
<td class="tl">注册时间</td>
<td>&nbsp;<?php echo timetodate($regtime, 6);?></td>
<td class="tl">注册IP</td>
<td>&nbsp;<?php echo $regip;?> - <?php echo ip2area($regip);?></td>
</tr>
</table>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>