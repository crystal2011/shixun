<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>

    <div class="tt">热门城市</div>
    <form method="post">
        <input type="hidden" name="forward" value="<?php echo $forward;?>"/>
        <table cellpadding="2" cellspacing="1" class="tb">
            <tr>
                <th width="100">排序</th>
                <th width="100">ID</th>
                <th>上级ID</th>
                <th>地区名</th>
                <th width="80">操作</th>
            </tr>
            <?php foreach($DAREA as $k=>$v) {?>
                <tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
                    <td><?php echo $v['listorder'];?></td>
                    <td>&nbsp;<?php echo $v['areaid'];?></td>
                    <td><?php echo $v['parentid'];?></td>
                    <td><?php echo $v['areaname'];?></td>
                    <td>
                        <a href="?file=<?php echo $file;?>&action=deletehot&areaid=<?php echo $v['areaid'];?>&parentid=<?php echo $parentid;?>" onclick="return _delete();"><img src="admin/image/delete.png" width="16" height="16" title="删除" alt=""/></a></td>
                </tr>
            <?php }?>
        </table>
    </form>
    <br/>
    <br/>
    <br/>
    <script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>