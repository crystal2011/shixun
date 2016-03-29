<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<style type="text/css">
.quote{border:1px solid #dcdcdc;background:#FFF;padding:10px;margin-bottom:10px;}
.quote_title {font-size:12px;color:#1B4C7A;}
.quote_time {font-size:11px;color:#666666;}
.quote_floor {float:right;font-size:10px;}
.quote_content {clear:both;}
</style>
<form action="?">
<div class="tt">评论搜索</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>
    <select name="type">
<?php foreach($cat_type as $k=>$v){ ?>
    <option value="<?php echo $k; ?>" <?php echo $k==$type?'selected':''; ?>><?php echo $v; ?></option>
<?php } ?>
    </select>
    用户id：<input type="text" size="6" name="userid" value="<?php echo $userid; ?>" />
    评论对象id：<input type="text" size="6" name="id" value="<?php echo $id; ?>" />
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
</table>
</form>
<form method="post">
<div class="tt">管理评论</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="25"><input type="checkbox" onclick="checkall(this.form);"/></th>
<th>评论内容</th>
<th width="60">操作</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td align="left" style="padding:10px;">
<div>
<span class="px11 f_blue">
<?php echo $v['adddate'];?>
</span>
&nbsp;
    类别：<?php echo $cat_type[$v['type']];?>&nbsp;&nbsp;&nbsp;用户ID：<?php echo $v['userid'];?>&nbsp;&nbsp;&nbsp;评论对象ID：<?php echo $v['id'];?>
</div>
<div class="b5 c_b"> </div>
<div>
<?php echo $v['quotation'] ? $v['quotation'] : $v['content'];?>
<?php if($v['reply']) { ?>
<br/>
<span class="f_red"><?php echo $v['editor'] ? '管理员'.$v['editor'] : $v['replyer'];?> <?php echo $v['replydate'];?> 回复</span><br/><?php echo nl2br($v['reply']);?>
<?php } ?>
</div>
<div class="b5 c_b"> </div>
<div class="b5 c_b"> </div>
<div>IP:<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&ip=<?php echo $v['ip'];?>"><?php echo $v['ip'];?></a> - <?php echo ip2area($v['ip']);?></div>
</td>
<td>
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="admin/image/edit.png" width="16" height="16" title="修改" alt=""/></a>&nbsp;
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&itemid=<?php echo $v['itemid'];?>" onclick="return _delete();"><img src="admin/image/delete.png" width="16" height="16" title="删除" alt=""/></a>
</td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="submit" value=" 删 除 " class="btn" onclick="if(confirm('确定要删除选中评论吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<?php if($action == 'check') { ?>
<input type="submit" value=" 通过审核 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check&status=3';"/>&nbsp;
<?php } else { ?>
<input type="submit" value=" 取消审核 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check&status=2';"/>&nbsp;
<?php } ?>
</div>
</form>
<div class="pages"><?php echo $pages;?></div>
<br/>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>