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
<div class="tt"><?php echo $action == 'add' ? '添加' : '修改';?>简历</div>
<table cellpadding="2" cellspacing="1" class="tb">

    <tr><tr>
        <td class="tl"><span class="f_red">*</span> 会员名</td>
        <td><input name="post[username]" type="text" datatype="*2-20"  size="20" value="<?php echo $username;?>" id="username"/> <a href="javascript:_user(Dd('username').value);" class="t">[资料]</a> <span id="dusername" class="f_red"></span></td>
    </tr>
        <td class="tl"><span class="f_red">*</span> 所属分类</td>
        <td><?php echo ajax_category_select('post[catid]', '选择分类', $catid, $moduleid);?> <span class="Validform_checktip"></span></td>

    </tr>

<tr>
<td class="tl"><span class="f_red">*</span> 真实姓名</td>
<td><input name="post[truename]" type="text" id="truename" datatype="*2-10"  size="20" value="<?php echo $truename;?>"/> </td>
</tr>
    <tr>
        <td class="tl"><span class="f_red">*</span> 联系电话</td>
        <td><input name="post[telephone]" id="telephone" datatype="mobile" type="text" size="30" value="<?php echo $telephone;?>"/> <span id="dtelephone" class="f_red"></span></td>
    </tr>
    <tr>
        <td class="tl"><span class="f_red">*</span> 电子邮件</td>
        <td><input name="post[email]" id="email" type="text" size="30" datatype="e" value="<?php echo $email;?>"/> <span id="demail" class="f_red"></span></td>
    </tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 免冠照片</td>
<td><input name="post[thumb]" type="text" size="60" id="thumb" datatype="empty|url" errormsg="请上传头像" value="<?php echo $thumb;?>"/>&nbsp;&nbsp;<span onclick="Dthumb(<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, Dd('thumb').value);" class="jt">[上传]</span>&nbsp;&nbsp;<span onclick="_preview(Dd('thumb').value);" class="jt">[预览]</span>&nbsp;&nbsp;<span onclick="Dd('thumb').value='';" class="jt">[删除]</span></td>
</tr>

<tr>
<td class="tl"><span class="f_red">*</span> 性别</td>
<td>
<?php
foreach($GENDER as $k=>$v) {
	if($k == 0) continue;
?>
<input type="radio" name="post[gender]" id="gender_<?php echo $k;?>" value="<?php echo $k;?>"<?php echo $k == $gender ? ' checked' : '';?>/><label for="gender_<?php echo $k;?>"> <?php echo $v;?></label> 
<?php
}
?>
</td>
</tr>

<tr>
<td class="tl"><span class="f_red">*</span> 婚姻状况</td>
<td>
<?php
foreach($MARRIAGE as $k=>$v) {
	if($k == 0) continue;
?>
<input type="radio" name="post[marriage]" id="marriage_<?php echo $k;?>" value="<?php echo $k;?>"<?php echo $k == $marriage ? ' checked' : '';?>/><label for="marriage_<?php echo $k;?>"> <?php echo $v;?></label> 
<?php
}
?>
</td>
</tr>

<tr>
<td class="tl"><span class="f_red">*</span> 现居住地</td>
<td><?php echo ajax_area_select('post[areaid]', '请选择', $areaid);?> <span id="dareaid" class="f_red"></span><span class="Validform_checktip"></span></td>
</tr>
    <tr>
        <td class="tl"><span class="f_red">*</span> 联系地址</td>
        <td><input name="post[address]" id="address" type="text" datatype="*2-50" size="60" value="<?php echo $address;?>"/></td>
    </tr>
<tr>
<td class="tl"><span class="f_red">*</span> 生日</td>
<td>
<input name="post[byear]" type="text" id="byear" size="4" datatype="n4-4" errormsg="请输入年份" value="<?php echo $byear;?>"/> 年
<select name="post[bmonth]">
<?php for($i = 1; $i < 13; $i++) {
	echo '<option value="'.$i.'"'.($i == $bmonth ? ' selected' : '').'>'.$i.'</option>';
}
?>
</select>
月
<select name="post[bday]">
<?php for($i = 1; $i < 32; $i++) {
	echo '<option value="'.$i.'"'.($i == $bday ? ' selected' : '').'>'.$i.'</option>';
}
?>
</select>
日

<span id="dbyear" class="f_red"></span>
</td>
</tr>


<tr>
<td class="tl"><span class="f_hid">*</span> 身高</td>
<td><input name="post[height]" type="text" id="height" size="10" datatype="empty|n2-3" errormsg="请输入身高"  value="<?php echo $height?$height:'';?>"/> cm <span id="dheight" class="f_red"></span></td>
</tr>

<tr>
<td class="tl"><span class="f_hid">*</span> 体重</td>
<td><input name="post[weight]" type="text" id="weight" size="10" datatype="empty|n2-3" errormsg="请输入体重" value="<?php echo $weight?$weight:'';?>"/> kg <span id="dweight" class="f_red"></span></td>
</tr>

<tr>
<td class="tl"><span class="f_red">*</span> 学历</td>
<td>
<?php
foreach($EDUCATION as $k=>$v) {
	if($k == 0) continue;
?>
<input type="radio" name="post[education]" id="education_<?php echo $k;?>" value="<?php echo $k;?>"<?php echo $k == $education ? ' checked' : '';?>/><label for="education_<?php echo $k;?>"> <?php echo $v;?></label> 
<?php
}
?>
&nbsp;&nbsp;(以上)
</td>
</tr>

<tr>
<td class="tl"><span class="f_red">*</span> 毕业院校</td>
<td><input name="post[school]" type="text" id="school" datatype="*2-50" errormsg="请输入院校" size="30" value="<?php echo $school;?>"/> <span id="dschool" class="f_red"></span></td>
</tr>

<tr>
<td class="tl"><span class="f_hid">*</span> 所学专业</td>
<td><input name="post[major]" type="text" id="major" size="30" datatype="empty|*2-50" errormsg="请输入专业" value="<?php echo $major;?>"/></td>
</tr>

<tr>
<td class="tl"><span class="f_hid">*</span> 专业技能</td>
<td><input name="post[skill]" type="text" size="50" datatype="empty|*2-50" errormsg="请输入技能" value="<?php echo $skill;?>"/></td>
</tr>

<tr>
<td class="tl"><span class="f_hid">*</span> 语言水平</td>
<td><input name="post[language]" type="text" datatype="empty|*2-50" errormsg="请输入语言水平" size="50" value="<?php echo $language;?>"/></td>
</tr>

<tr>
<td class="tl"><span class="f_red">*</span> 工作性质</td>
<td>
<?php
foreach($TYPE as $k=>$v) {
?>
<input type="radio" name="post[type]" id="type_<?php echo $k;?>" value="<?php echo $k;?>"<?php echo $k == $type ? ' checked' : '';?>/><label for="type_<?php echo $k;?>"> <?php echo $v;?></label> 
<?php
}
?>
</td>
</tr>

<tr>
<td class="tl"><span class="f_hid">*</span> 期望薪资</td>
<td><input name="post[minsalary]" type="text" id="minsalary" datatype="empty|n1-7" errormsg="请输入期望薪资" size="6" value="<?php echo $minsalary;?>"/> 至 <input name="post[maxsalary]" type="text" id="maxsalary" datatype="empty|n1-7" errormsg="请输入期望薪资" size="6" value="<?php echo $maxsalary;?>"/> <?php echo $DT['money_unit'];?>/月 (不填或者填0为不限)</td>
</tr>

<tr>
<td class="tl"><span class="f_red">*</span> 工作经验</td>
<td>
<input type="text" name="post[experience]" datatype="n1-2" errormsg="请输入工作经验"  value="<?php echo $experience;?>" size="4" id="experience"/> &nbsp;&nbsp;年 <span id="dexperience" class="f_red"></span></td>
</tr>


    <tr>
        <td class="tl"><span class="f_red">*</span> 求职状态</td>
        <td>
            <select name="post[situation]">
                <?php
                foreach($SITUATION as $k=>$v) {  if($k == 0) continue;
                    ?>
                    <option value="<?php echo $k;?>"<?php echo $k == $situation ? ' selected' : ''?>><?php echo $v;?></option>
                <?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php echo $FD ? fields_html('<td class="tl">', '<td>', $item) : '';?>

<tr>
<td class="tl"><span class="f_red">*</span> 自我鉴定</td>
<td><textarea name="post[content]" id="content" class="dsn"><?php echo $content;?></textarea>
<?php echo deditor($moduleid, 'content', $MOD['editor'], '100%', 350);?><br/><span id="dcontent" class="f_red"></span>
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
                'empty':function(gets,obj,curform,regxp){
                    if(gets.length==0) return true;
                    return false;
                }
            },
            beforeCheck:function(curform){
                var isnotselect = true;

                var dn = 0;
                $("#dform select").each(function(){
                    var selvel = $(this).val();
                    var datatype = $(this).attr("data-type");
                    if(selvel==0){
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