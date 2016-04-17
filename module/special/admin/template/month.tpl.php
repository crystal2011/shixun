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
                    羊角会用户id：<input type="text" size="10" name="codeid" value="<?php echo $codeid?$codeid:'';?>"/>&nbsp;
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
                <th>月份</th>
                <?php if($codeid) { ?>
                <th>羊角会成员</th>
                <?php } ?>
                <th>折扣</th>
                <th>总金额</th>
                <th>应付金额</th>
                <th>折扣金额</th>
            </tr>
            <?php $snallmoney = $snmoney =  $sndiscountfee = 0; ?>
            <?php foreach($list as $k=>$v) {?>
                <?php $snallmoney += $v['allmoney'];  $snmoney += $v['money'];  $sndiscountfee += $v['discountfee']; ?>
                <tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
                    <td><?php echo $v['ym'];?></td>
                    <?php if($codeid) { ?>
                    <td><?php echo isset($codeuserinfo)?$codeuserinfo['title']:''; ?></td>
                    <?php } ?>
                    <td><?php echo doubleval($v['discount']);?>折</td>
                    <td><?php echo $v['allmoney'];?></td>
                    <td><?php echo $v['money'];?></td>
                    <td><?php echo $v['discountfee'];?></td>
                </tr>
            <?php }?>
            <tr style="background-color:#fff;" align="center" >
                <td></td>
                <?php if($codeid) { ?>
                <td></td>
                <?php } ?>
                <td style="color:red;">汇总</td>
                <td style="color:red;"><?php echo $snallmoney; ?></td>
                <td style="color:red;"><?php echo $snmoney; ?></td>
                <td style="color:red;"><?php echo $sndiscountfee; ?></td>
            </tr>
        </table>

    </form>
    <script type="text/javascript">Menuon(7);</script>
<?php include tpl('footer');?>