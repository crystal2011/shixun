<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>

    <form method="post">

        <table cellpadding="2" cellspacing="1" class="tb">
            <tr>
                <th width="25"><input type="checkbox" onclick="checkall(this.form);"/></th>
                <th>地区</th>
                <th>标 题</th>
                <th width="50">操作</th>
            </tr>
            <?php foreach($list as $k=>$v) {?>
                <tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
                    <td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>

                    <td class="px11"><?php echo area_pos($v['areaid'],'');?></td>
                    <td class="px11"><?php echo $v['title'];?></td>

                    <td>
                        <a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=fengedit&itemid=<?php echo $v['itemid'];?>"><img src="admin/image/edit.png" width="16" height="16" title="修改" alt=""/></a>&nbsp;
                        <a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=fengdel&itemid=<?php echo $v['itemid'];?>" onclick="return _delete();"><img src="admin/image/delete.png" width="16" height="16" title="删除" alt=""/></a>
                    </td>
                </tr>
            <?php }?>
        </table>
        <div class="btns">
            <input type="submit" value=" 彻底删除 " class="btn" onclick="if(confirm('确定要删除选中<?php echo $MOD['name'];?>吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=fengdel'}else{return false;}"/>&nbsp;
        </div>
    </form>

    <br/>
    <script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>