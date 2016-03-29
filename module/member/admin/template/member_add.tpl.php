<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
load('profile.js');
?>
    <script src="<?php echo DT_SKIN; ?>js/Validform_v5.3.2_min.js"></script>
    <div class="tt">会员添加</div>
    <form method="post" action="?" id="validform">
        <input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
        <input type="hidden" name="file" value="<?php echo $file;?>"/>
        <input type="hidden" name="action" value="<?php echo $action;?>"/>
        <table cellpadding="2" cellspacing="1" class="tb">
            <tr>
                <td class="tl"><span class="f_red">*</span> 用户名</td>
                <td><input type="text" size="20" datatype="*2-12" name="member[username]" ajaxurl="ajax.php?moduleid=<?php echo $moduleid;?>&action=member&job=username"/></td>
            </tr>
            <tr>
                <td class="tl"><span class="f_red">*</span> 手机号码</td>
                <td><input type="text" size="20" datatype="m" name="member[mobile]" ajaxurl="ajax.php?moduleid=<?php echo $moduleid;?>&action=member&job=mobile"/></td>
            </tr>
            <tr>
                <td class="tl"><span class="f_red">*</span> 登录密码</td>
                <td><input type="password" size="20" name="member[password]" datatype="*" ajaxurl="ajax.php?moduleid=<?php echo $moduleid;?>&action=member&job=password" /></td>
            </tr>
            <tr>
                <td class="tl"><span class="f_red">*</span> 重复输入密码</td>
                <td><input type="password" size="20" name="member[cpassword]" datatype="*" recheck="member[password]" /></td>
            </tr>
        </table>
        <div class="sbt"><input type="submit" id="btn_sub" name="submit" value=" 确 定 " class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value=" 重 置 " class="btn btnReset"/></div>
    </form>
    <script type="text/javascript">
        $("#validform").Validform({
            btnReset: ".btnReset",
            tiptype: 3
        })
    </script>
    <script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>