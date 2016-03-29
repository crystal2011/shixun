<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
    <script src="<?php echo DT_SKIN; ?>js/Validform_v5.3.2_min.js"></script>
<form method="post" action="?" id="validform">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="adminuid" value="<?php echo $user['adminuid'];?>"/>
<input type="hidden" name="username" value="<?php echo $user['username'];?>"/>
<div class="tt">修改管理员</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 会员名</td>
<td><?php echo $user['username'];?></td>
</tr>
    <?php if($_adminadmin==1 || $_adminuid==$user['adminuid']){ ?>
    <tr>
        <td class="tl"><span class="f_red">*</span> 密码</td>
        <td>
            <input type="password" size="20" name="password" id="password" datatype="empty|*" ajaxurl="?moduleid=<?php echo $moduleid;?>&file=admin&action=password" />
    </tr>
    <tr>
        <td class="tl"><span class="f_red">*</span> 确认密码</td>
        <td>
            <input type="password" size="20" name="repassword" id="repassword" datatype="empty|*" recheck="password" />
    </tr>
    <?php } ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 管理员类别</td>
<td>
<div class="b10">&nbsp;</div>
<input type="radio" name="admin" value="1" id="admin_1"<?php echo $user['admin'] == 1 ? ' checked' : '';?>/><label for="admin_1"> 超级管理员</label> <span class="f_gray">拥有除创始人特权外的所有权限</span>
<div class="b10">&nbsp;</div>
<input type="radio" name="admin" value="2" id="admin_2"<?php echo $user['admin'] == 2 ? ' checked' : '';?>/><label for="admin_2"> 普通管理员</label> <span class="f_gray">拥有系统分配的权限</span>
<div class="b10">&nbsp;</div>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 分站权限</td>
<td><?php echo ajax_area_select('aid', '请选择', $user['aid']);?> <span class="f_gray">分站权限仅对<span class="f_red">普通管理员</span>生效</span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 角色名称</td>
<td><input type="text" size="20" name="role" id="role" value="<?php echo $user['role'];?>"/> <span class="f_gray">可以为角色名称，例如编辑、美工、某分站编辑等，也可以为该管理员的备注</span></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="修 改" class="btn"></div>
</form>
<script type="text/javascript">Menuon(1);</script>
    <script type="text/javascript">
        $("#validform").Validform({
            tiptype: 3,
            datatype:{
                "empty":function(gets,obj,curform,regxp){
                    if(gets.length==0){
                        return true;
                    }else{
                        return false;
                    }
                }

                }
        })
    </script>
<?php include tpl('footer');?>