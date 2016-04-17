<?php
/**
 * 手机端-地区选择
 */
define('DT_REWRITE', true);
require '../common.inc.php';

$topname = '地区选择';
$seo_title = '地区选择-';
$forward = strstr($forward,'?')?$forward.'&':$forward.'?';

include template('selectarea','mobile');