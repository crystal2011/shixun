<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" target="_blank" id="check_title">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="kw" value="" id="kw"/>
</form>
<form method="post" action="?" id="dform" >
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<div class="tt"><?php echo $action == 'add' ? '添加' : '修改';?><?php echo $MOD['name'];?></div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 所属分类</td>
<td><?php echo ajax_category_select('post[catid]', '选择分类', $catid, $moduleid,'data-type="1"');?><span class="Validform_checktip"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> <?php echo $MOD['name'];?>标题</td>
<td><input name="post[title]" type="text" id="title" size="60" datatype="*2-50" value="<?php echo $title;?>"/> <?php echo level_select('post[level]', '级别', $level, 'id="level"');?> </td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 标题图片</td>
<td><input name="post[thumb]" id="thumb" type="text" size="60" nullmsg="请上传图片" errormsg="请上传图片" datatype="url" value="<?php echo $thumb;?>"/>&nbsp;&nbsp;<span onclick="Dthumb(<?php echo $moduleid;?>,'','', Dd('thumb').value,true);" class="jt">[上传]</span>&nbsp;&nbsp;<span onclick="_preview(Dd('thumb').value);" class="jt">[预览]</span>&nbsp;&nbsp;<span onclick="Dd('thumb').value='';" class="jt">[删除]</span></td>
</tr>

<tbody id="basic">
<tr>
<td class="tl"><span class="f_red">*</span> <?php echo $MOD['name'];?>内容</td>
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
<tbody id="subtitle" style="display:<?php echo $pagebreak ? '' : 'none';?>;">
<tr>
<td class="tl"><span class="f_hid">*</span> 分页标题</td>
<td>
<textarea name="post[subtitle]" style="width:90%;height:45px;"><?php echo $subtitle;?></textarea>
<br/>1行一个分页标题，按回车换行
</td>
</tr>
</tbody>
<tr>
<td class="tl"><span class="f_hid">*</span> <?php echo $MOD['name'];?>简介</td>
<td><textarea name="post[introduce]" style="width:90%;height:45px;"><?php echo $introduce;?></textarea></td>
</tr>
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
        tiptype: 3,
        datatype:{
            "mobile":function(gets,obj,curform,regxp){
                if(!is_mobile(gets) && !is_phone(gets)) return '输入错误';
                return true;
            },
            'money':function(gets,obj,curform,regxp){
                if(!is_price(gets)) return false;
                return true;
            }
        },
        beforeCheck:function(curform){
            var isnotselect = true;

            var dn = 0;
            $("#dform select").each(function(){
                var selvel = $(this).val();
                var datatype = $(this).attr("data-type");
                if($(this).attr("isarea")==1 && selvel!=0){
                    dn = dn+1;
                }
                if(selvel==0 && datatype==1  && !($(this).attr("isarea")==1 && dn>=2)){
                    isnotselect = false;
                    $(this).parent().parent().find('.Validform_checktip').removeClass('Validform_right').addClass('Validform_wrong').html('请选择');
                    $(this).focus();
                    return false;
                }
            })
            if(!isnotselect) return false;
            return true;
        }
    })

    $("select").live("change",function(){
        $(this).parent().parent().find('.Validform_checktip').removeClass('Validform_wrong').html('');

    })
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>