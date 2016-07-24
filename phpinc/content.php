<?php
/*****************************************************************************************
	文件： phpinc/content.php
	备注： 手机版内容样式清理
	版本： 4.x
	网站： www.phpok.com
	作者： qinggan <qinggan@188.com>
	时间： 2015年12月27日 13时58分
*****************************************************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
if($rs['content']){
	$rs['content'] = preg_replace("/<p\s+style=['|\"].*['|\"]>/isU",'<p style="font-weight:bold;">',$rs['content']);
}
?>