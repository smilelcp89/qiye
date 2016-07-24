<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="author" content="phpok.com" />
	<meta http-equiv="expires" content="wed, 26 feb 1997 08:21:57 gmt"> 
	<title><?php echo $config['title'];?></title>
	<link href="css/css.php?file=open.css,artdialog.css,form.css,smartmenu.css&version=<?php echo VERSION;?>" rel="stylesheet" type="text/css" />
	<?php echo phpok_head_css();?>
	<script type="text/javascript" src="<?php echo phpok_url(array('ctrl'=>'js'));?>"></script>
	<?php echo phpok_head_js();?>
	<?php echo $GLOBALS["app"]->plugin_html_ap("head");?>
<?php echo $app->plugin_html_ap("phpokhead");?></head>
<body ondragstart="return false;">