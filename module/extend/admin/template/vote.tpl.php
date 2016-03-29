<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?">
<div class="tt">投票搜索</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>
<select name="type">
    <option value="0">类型</option>
    <option value="1" <?php echo $type==1?'selected':''; ?>>名厨</option>
    <option value="2" <?php echo $type==2?'selected':''; ?>>菜系</option>
</select>
    菜系/名厨ID：<input type="text" size="5" name="id" value="<?php echo $id;?>" />&nbsp;&nbsp;
    用户id：<input type="text" size="6" name="userid" value="<?php echo $userid; ?>" />&nbsp;&nbsp;
    <?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/>
</td>
</tr>
</table>
</form>
<form method="post">
<div class="tt">管理投票</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th>ID</th>
<th>类别</th>
<th>用户</th>
    <th>名厨/菜系id</th>
<th>投票时间</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><?php echo $v['itemid'];?></td>
<td><?php echo $v['type']==1?'名厨':'菜系';;?></td>
    <td><?php echo $v['userid'];?></td>
    <td><?php echo $v['id'];?></td>
<td class="px11"><?php echo $v['adddate'];?></td>

</tr>
<?php }?>
</table>
</form>
<div class="pages"><?php echo $pages;?></div>
<br/>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>