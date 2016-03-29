<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
    <form method="post" action="?" id="dform" onsubmit="return check();">
        <input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
        <input type="hidden" name="file" value="<?php echo $file;?>"/>
        <input type="hidden" name="action" value="<?php echo $action;?>"/>
        <input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
        <input type="hidden" name="forward" value="<?php echo $forward;?>"/>
        <div class="tt"><?php echo $action == 'add' ? '添加' : '修改';?><?php echo $MOD['name'];?></div>
        <table cellpadding="2" cellspacing="1" class="tb">
            <tr>
                <td class="tl"><span class="f_red">*</span> 地区</td>
                <td><?php echo ajax_area_select('post[areaid]', '请选择', $areaid,'data-type="1" isarea = "1"');?> <span id="dareaid" class="f_red"></span><span class="Validform_checktip"></span></td>
            </tr>
            <tr>
                <td class="tl"><span class="f_red">*</span> 名称</td>
                <td><input name="post[title]" type="text" id="title" datatype="*2-10" size="60" value="<?php echo $title;?>"/> <br/><span id="dtitle" class="f_red"></span></td>
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