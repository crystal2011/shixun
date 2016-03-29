<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
    <form method="post" action="?" id="dform">
        <input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
        <input type="hidden" name="file" value="<?php echo $file;?>"/>
        <input type="hidden" name="action" value="<?php echo $action;?>"/>
        <input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
        <input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<div class="tt"><?php echo $action == 'add' ? '添加' : '修改';?><?php echo $MOD['name'];?></div>
<table cellpadding="2" cellspacing="1" class="tb">

<tr>
<td class="tl"><span class="f_red">*</span> <?php echo $MOD['name'];?>名称</td>
<td><input name="post[title]" type="text" id="title" size="60" datatype="*2-10" value="<?php echo $title;?>"/> <?php echo level_select('post[level]', '级别', $level, 'id="level"');?><br/><span id="dtitle" class="f_red"></span></td>
</tr>
    <tr>
        <td class="tl"><span class="f_red">*</span> 会员名</td>
        <td>
            <input <?php if($itemid){echo 'disabled';}else{ echo 'name="post[username]" datatype="*2-20"'; } ?> type="text"  size="20" value="<?php echo $username;?>" id="username"/>
            <a href="javascript:_user(Dd('username').value);" class="t">[资料]</a> <span id="dusername" class="f_red"></span>
        </td>
    </tr>
    <tr>
        <td class="tl"><span class="f_red">*</span> 内容审核号</td>
        <td>
            <input <?php if($itemid){echo 'disabled';}else{ echo 'name="post[code]" datatype="n6-6"'; } ?>  nullmsg="请输入6位数字" type="text"  size="20" value="<?php echo $code;?>" id="username"/>

        </td>
    </tr>
    <tr>
<td class="tl"><span class="f_red">*</span> 图片</td>
<td><input name="post[thumb]" id="thumb" type="text" datatype="url" nullmsg="请上传图片" errormsg="请上传图片" size="60" value="<?php echo $thumb;?>"/>&nbsp;&nbsp;<span onclick="Dthumb(<?php echo $moduleid;?>,'','', Dd('thumb').value,true);" class="jt">[上传]</span>&nbsp;&nbsp;<span onclick="_preview(Dd('thumb').value);" class="jt">[预览]</span>&nbsp;&nbsp;<span onclick="Dd('thumb').value='';" class="jt">[删除]</span></td>
</tr>
    <tr>
        <td class="tl"><span class="f_hid">*</span> 常任委员会</td>
        <td><input type="radio" name="post[islong]" value="1" checked />是<input type="radio" name="post[islong]" <?php if(isset($islong) && $islong==0){ echo 'checked';} ?> value="0" />不是</span>
        </td>
    </tr>
    <tr>
        <td class="tl"><span class="f_red">*</span>折扣</td>
        <td>
            <select name="post[discount]">
                <option value="10"> 10折(不打折) </option>
                <?php foreach($arrdiscount as $k=>$v){ ?>
                    <option value="<?php echo $v; ?>" <?php echo $v==$discount?'selected':''; ?> ><?php echo $v; ?> 折</option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="tl"><span class="f_red">*</span>排序</td>
        <td>
            <input errormsg="请输入1-1000" nullmsg="1-1000" type="text"  size="6" name="sortnum" value="<?php echo isset($sortnum)?$sortnum:1000;?>" id="username"/> 请输入1-1000 默认1000
        </td>
    </tr>
<tr>
<td class="tl"><span class="f_hid">*</span> <?php echo $MOD['name'];?>详情</td>
<td><textarea name="post[content]" id="content" class="dsn"><?php echo $content;?></textarea>
<?php echo deditor($moduleid, 'content', $MOD['editor'], '100%', 350);?><br/><span id="dcontent" class="f_red"></span>
</td>
</tr>
    <tr>
        <td class="tl" height="30"><span class="f_hid">*</span> 内容选项</td>
        <td>
            <a href="javascript:pagebreak();Ds('subtitle');"><img src="admin/image/pagebreak.gif" align="absmiddle"/> 插入分页符</a>&nbsp;&nbsp;
            <input type="checkbox" name="post[save_remotepic]" value="1"<?php if($MOD['save_remotepic']) echo 'checked';?>/>下载远程图片&nbsp;&nbsp;
            <input type="checkbox" name="post[clear_link]" value="1"<?php if($MOD['clear_link']) echo 'checked';?>/>清除链接&nbsp;&nbsp;
            截取内容 <input name="post[introduce_length]" type="text" size="2" value="<?php echo $MOD['introduce_length']?>"/> 字符至简介&nbsp;&nbsp;
        </td>
    </tr>
    <tr>
        <td class="tl"><span class="f_hid">*</span> <?php echo $MOD['name'];?>简介</td>
        <td><textarea name="post[introduce]" style="width:90%;height:45px;"><?php echo $introduce;?></textarea></td>
    </tr>

</tbody>

<tr>
<td class="tl"><span class="f_hid">*</span> <?php echo $MOD['name'];?>状态</td>
<td>
<input type="radio" name="post[status]" value="3" <?php if($status == 3) echo 'checked';?> id="status_3"/><label for="status_3"> 通过</label>
<input type="radio" name="post[status]" value="2" <?php if($status == 2) echo 'checked';?> id="status_2"/><label for="status_2">  待审</label>
<input type="radio" name="post[status]" value="1" <?php if($status == 1) echo 'checked';?> onclick="if(this.checked) Dd('note').style.display='';" id="status_1"/><label for="status_1">  拒绝</label>
<input type="radio" name="post[status]" value="0" <?php if($status == 0) echo 'checked';?> id="status_0"/><label for="status_0">  删除</label>
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value=" 确 定 " class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" name="reset" value=" 重 置 " class="btn"/></div>
</form>

    <script src="<?php echo DT_SKIN; ?>js/Validform_v5.3.2_min.js"></script>
    <script type="text/javascript">
        $("#dform").Validform({
            tiptype: 3
        })
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>