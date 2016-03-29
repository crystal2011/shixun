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
                    ID：<input type="text" size="4" name="itemid" value="<?php echo $itemid;?>"/>&nbsp;
                    编号：<input type="text" size="8" name="code" value="<?php echo $code;?>"/>&nbsp;
                    用户id：<input type="text" size="4" name="userid" value="<?php echo $userid;?>"/>&nbsp;
                    <input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
                    <input type="submit" value="搜 索" class="btn"/>&nbsp;
                    <input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;<select name="datetype">
                        <option value="edittime" <?php if($datetype == 'edittime') echo 'selected';?>>更新日期</option>
                        <option value="addtime" <?php if($datetype == 'addtime') echo 'selected';?>>发布日期</option>
                    </select>&nbsp;
                    <?php echo dcalendar('fromdate', $fromdate, '');?> 至 <?php echo dcalendar('todate', $todate, '');?>&nbsp;

                </td>
            </tr>
        </table>
    </form>
    <form method="post">
        <div class="tt"><?php echo $menus[$menuid][0];?></div>
        <table cellpadding="2" cellspacing="1" class="tb">
            <tr>
                <th width="50">ID</th>
                <th>用户</th>
                <th>金额(元)</th>
                <th>申请时间</th>
                <th>结算时间</th>
                <th>状态</th>
                <th width="100">操作</th>
            </tr>
            <?php foreach($lists as $k=>$v) {?>
                <tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
                    <th><?php echo $v['itemid'] ?></th>
                    <td><a href="javascript:_user('<?php echo $v['userid'];?>','userid');"><?php echo $getListUser[$v['userid']]['username'];?></a></td>
                    <td><?php echo $v['money'];?></td>
                    <td><?php echo date('Y-m-d',$v['addtime']);?></td>
                    <td></td>
                    <td>
                        <?php if($v['status']==0){ ?>
                            结算中
                        <?php }else if($v['status']==1){ ?>
                            拒绝
                        <?php }else{ ?>
                            完成
                        <?php } ?>
                    </td>
                    <td></td>
                </tr>
            <?php }?>
        </table>
        <div class="btns">



        </div>
    </form>
    <div class="pages"><?php echo $pages;?></div>
    <br/>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>