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
                    编号：<input type="text" size="30" name="code" value="<?php echo $code;?>"/>&nbsp;
                    用户id：<input type="text" size="4" name="userid" value="<?php echo $userid;?>"/>&nbsp;
                    类型：
                    <select name="type">
                        <option value="-1">不限</option>
                        <?php foreach($arr as $k=>$v){ ?>
                            <option value="<?php echo $k; ?>" <?php echo $type==$k?'selected':''; ?>><?php echo $v; ?></option>
                        <?php } ?>
                    </select>
                    <input type="submit" value="搜 索" class="btn"/>&nbsp;
                    <input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
                </td>
            </tr>
        </table>
    </form>
    <form method="post">
        <div class="tt"><?php echo $menus[$menuid][0];?></div>
        <table cellpadding="2" cellspacing="1" class="tb">
            <tr>
                <th width="50"><input type="checkbox" onclick="checkall(this.form);"/></th>
                <th>会员</th>
                <th>折扣</th>
                <th>总总额</th>
                <th>应付金额</th>
                <th>折扣金额</th>
                <th>类型</th>
                <th>备注</th>
                <th>使用时间</th>
            </tr>
            <?php foreach($lists as $k=>$v) {?>
                <tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
                    <td><?php echo $v['itemid'];?></td>
                    <td><a href="javascript:_user('<?php echo $v['userid'];?>','userid');"><?php echo $getListUser[$v['userid']]['username'];?></a></td>
                    <td><?php echo doubleval($v['discount']);?>折</td>
                    <td><?php echo $v['allmoney'];?></td>
                    <td><?php echo $v['money'];?></td>
                    <td><?php echo $v['discountfee'];?></td>
                    <td><?php echo $arr[$v['type']];?></td>
                    <td><?php echo $v['note'];?></td>
                    <td class="px11"><?php echo date('Y-m-d',$v['addtime']);?></td>
                </tr>
            <?php }?>
        </table>

    </form>
    <div class="pages"><?php echo $pages;?></div>
    <br/>
<?php if(isset($id) && isset($tm) && $id && $tm > $DT_TIME) { ?>
    <script type="text/javascript">Dwidget('?moduleid=<?php echo $moduleid;?>&file=item&specialid=<?php echo $id;?>', '请添加信息');</script>
<?php } ?>
    <script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>