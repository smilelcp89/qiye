<?php
/***********************************************************
	Filename: phpok/admin/global.func.php
	Note	: 后台通用函数加载
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2012-10-27 14:04
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}

function show_pending_info()
{
	$site_id = $_SESSION["admin_site_id"];
	//取得所有未审核的主题列表
	$list = array();
	$rslist = $GLOBALS['app']->model('list')->pending_info($site_id);
	if($rslist)
	{
		foreach($rslist AS $key=>$value)
		{
			if($value["total"]>0)
			{
				$url = $GLOBALS['app']->url("list","action","id=".$value["pid"]);
				$list[] = array("title"=>$value["title"],"total"=>$value["total"],"url"=>$url);
			}
		}
	}
	return $list;
}

//返回JS URL 或 <script type="text/javascript" src="url"></script>
function include_js($js="",$mini=false,$injs=false)
{
	global $app;
	$array = $app->config['load_js'][$app->app_id];
	$str = array();
	if($array && is_array($array)){
		foreach($array AS $key=>$value){
			if($key == 'global' || $key == $app->ctrl){
				$str[] = $value;
			}
		}
	}
	if($js){
		$str[] = $js;
	}
	$str = implode(",",$str);
	$url = $mini ? $app->url('js','mini','ext='.rawurlencode($str)) : $app->url('js','ext','js='.rawurlencode($str));
	if($injs){
		$url = '<script type="text/javascript" src="'.$url.'"></script>';
	}
	return $url;
}
?>