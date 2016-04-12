<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?">
<div class="tt"><?php echo $MOD['name'];?>搜索</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>
&nbsp;<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<?php echo $level_select;?>&nbsp;

<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>
&nbsp;<select name="datetype">
<option value="addtime" <?php if($datetype == 'addtime') echo 'selected';?>>发布日期</option>
<option value="updatetime" <?php if($datetype == 'updatetime') echo 'selected';?>>更新日期</option>
<option value="edittime" <?php if($datetype == 'edittime') echo 'selected';?>>修改日期</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '');?> 至 <?php echo dcalendar('todate', $todate, '');?>&nbsp;
<?php echo category_select('catid', '分类', $catid, 13);?>&nbsp;
ID：<input type="text" size="4" name="itemid" value="<?php echo $itemid;?>"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<div class="tt"><?php echo $menus[$menuid][0];?></div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="50"><input type="checkbox" onclick="checkall(this.form);"/></th>
    <th width="14"> </th>
<th>分类</th>
<th>标 题</th>
    <th>会员</th>
    <th>技术合作费</th>
<th width="130"><?php echo $timetype == 'add' ? '添加' : '更新';?>时间</th>
<th>浏览</th>
<th width="150">操作</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/><?php echo $v['itemid'];?></td>
    <td><?php if($v['level']) {?><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&level=<?php echo $v['level'];?>"><img src="admin/image/level_<?php echo $v['level'];?>.gif" title="<?php echo $v['level'];?>级" alt=""/></a><?php } ?></td>
    <td><?php echo $v['catname'];?></td>
  <td class="px11"><?php echo $v['title'];?></td>
    <td>
        <a href="javascript:_user('<?php echo $v['userid'];?>','userid');"><?php echo $getListUser[$v['userid']]['username'];?></a>
    </td>
    <td><?php echo doubleval($v['coofee']);?></td>
    <?php if($timetype == 'add') {?>
<td class="px11" title="更新时间<?php echo $v['editdate'];?>"><?php echo $v['adddate'];?></td>
<?php } else { ?>
<td class="px11" title="添加时间<?php echo $v['adddate'];?>"><?php echo $v['editdate'];?></td>
<?php } ?>
<td class="px11"><?php echo $v['hits'];?></td>
<td>
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="admin/image/edit.png" width="16" height="16" title="修改" alt=""/></a>&nbsp;
    <?php if($v['status']==0){ ?>
        <a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=restore&itemid=<?php echo $v['itemid'];?>">还原</a>
    <?php }else{ ?>
        <a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1&itemid=<?php echo $v['itemid'];?>">回收站</a>
    <?php } ?>
</td>
</tr>
<?php }?>
</table>
<div class="btns">

<?php if($action == 'check') { ?>

<input type="submit" value=" 通过审核 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check';"/>&nbsp;
<input type="submit" value=" 拒 绝 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=reject';"/>&nbsp;
<input type="submit" value=" 回收站 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;

<?php } else if($action == 'reject') { ?>

<input type="submit" value=" 回收站 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;

<?php } else if($action == 'recycle') { ?>
<input type="submit" value=" 还 原 " class="btn" onclick="if(confirm('确定要还原选中<?php echo $MOD['name'];?>吗？状态将被设置为已通过')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=restore'}else{return false;}"/>&nbsp;
<input type="submit" value=" 清 空 " class="btn" onclick="if(confirm('确定要清空回收站吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear';}else{return false;}"/>

<?php } else { ?>
    <input type="submit" value=" 回收站 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
    <?php echo level_select('level', '设置级别为</option><option value="0">取消', 0, 'onchange="this.form.action=\'?moduleid='.$moduleid.'&file='.$file.'&action=level\';this.form.submit();"');?>

<?php } ?>

</div>
</form>
<div class="pages"><?php echo $pages;?></div>
<br/>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>