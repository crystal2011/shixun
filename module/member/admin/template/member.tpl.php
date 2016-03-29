<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?">
<div class="tt">会员搜索</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="20" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<?php echo ajax_area_select('areaid', '所在地区', $areaid);?>&nbsp;
<?php echo $order_select;?>
&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="timetype">
<option value="regtime" <?php if($timetype == 'regtime') echo 'selected';?>>注册时间</option>
<option value="logintime" <?php if($timetype == 'logintime') echo 'selected';?>>登录时间</option>
</select>&nbsp;
<?php echo dcalendar('fromtime', $fromtime);?> 至 <?php echo dcalendar('totime', $totime);?>&nbsp;
    会员名：<input type="text" name="username" value="<?php echo $username;?>" size="6"/>&nbsp;
    会员ID：<input type="text" name="uid" value="<?php echo $uid;?>" size="4"/>
    是否拥有名厨栏目：<input type="checkbox" <?php echo $ischu==1?'checked':'';?> name="ischu"  size="4" value="1"/>
</td>
</tr>
</table>
</form>
<form method="post">
<div class="tt">会员管理</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="25"><input type="checkbox" onclick="checkall(this.form);"/></th>
<th>会员ID</th>
<th>用户名</th>
    <th>手机</th>
    <th>是否为名厨</th>
<th>注册时间</th>
<th>最后登录</th>
<th>登录次数</th>
<th width="100">操作</th>
</tr>
<?php foreach($members as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input type="checkbox" name="userid[]" value="<?php echo $v['userid'];?>"/></td>
<td class="px11"><?php echo $v['userid'];?></td>
<td align="left">&nbsp;<a href="javascript:_user('<?php echo $v['username'];?>');" ><?php echo $v['username'];?></a></td>
    <td><?php echo $v['mobile']; ?></td>
    <td><?php echo $v['ischu']?'是':'否'; ?></td>
    <td class="px11" title="修改时间:<?php echo $v['edittime'] ? timetodate($v['edittime']) : '无';?>"><?php echo $v['regdate'];?></td>

    <td class="px11"><?php echo $v['logindate'];?></td>
<td class="px11"><a href="javascript:Dwidget('?moduleid=<?php echo $moduleid;?>&file=loginlog&username=<?php echo $v['username'];?>&action=record', '[<?php echo $v['username'];?>] 登录记录');"><?php echo $v['logintimes'];?></a></td>
<td>
<a href="?moduleid=2&action=show&userid=<?php echo $v['userid'];?>"><img src="admin/image/view.png" width="16" height="16" title="会员[<?php echo $v['username'];?>]详细资料" alt=""/></a>&nbsp;
<?php if($v['groupid']!=3){ ?>
    <a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&userid=<?php echo $v['userid'];?>" onclick="if(!confirm('确定要删除选中会员吗？系统将选中用户所有信息进入禁止状态，此操作可撤销，在回收站中恢复')) return false;">删除</a>
<?php }else{ ?>
    <a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=huifu&userid=<?php echo $v['userid'];?>" onclick="if(!confirm('确定要恢复选中会员吗？系统将选中用户所有信息进入恢复原来状态')) return false;">恢复</a>

<?php } ?>
</td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="submit" value=" 删除会员 " class="btn" onclick="if(confirm('确定要删除选中会员吗？系统将选中用户所有信息进入禁止状态，此操作可撤销，在回收站中恢复')){this.form.action='?moduleid=<?php echo $moduleid;?>&action=delete'}else{return false;}"/>&nbsp;
    </div>
</form>
<div class="pages"><?php echo $pages;?></div>
<br/><br/><br/>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>