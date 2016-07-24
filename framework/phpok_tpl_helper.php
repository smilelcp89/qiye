<?php
/***********************************************************
	Filename: phpok_tpl_helper.php
	Note	: 在PHPOK模板中常用的函数，此函数在action前才加载
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2012-11-07 20:27
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
function phpok($id='',$ext="")
{
	if(!$id || !$GLOBALS['app']->call){
		return false;
	}
	$count = func_num_args();
	if($count<=2){
		return $GLOBALS['app']->call->phpok($id,$ext);
	}
	$param = array();
	for($i=1;$i<$count;++$i){
		$tmp = func_get_arg($i);
		if(strpos($tmp,'=') !== true){
			$tmp = str_replace(':','=',$tmp);
		}
		$param[] = $tmp;
	}
	$param = implode("&",$param);
	return $GLOBALS['app']->call->phpok($id,$param);
}

function token($data)
{
	if(!$data){
		return false;
	}
	if(is_string($data)){
		parse_str($data,$data);
	}
	return $GLOBALS['app']->lib('token')->encode($data);
}


//在模板页中直接分页传参数，完整参数有
//home=首页&prev=上一页&next=下一页&last=尾页&half=5&opt=1&add={total}/{psize}
function phpok_page($url,$total,$num=0,$psize=20,$param="")
{
	if(!$url || !$total){
		return false;
	}
	if($param){
		parse_str($param,$list);
		if(!$list){
			$list = array();
		}
		foreach($list AS $key=>$value){
			if(substr($value,0,1) == ';' && substr($value,-1) == ';'){
				$value = "&".substr($value,1);
			}
			if($key == 'home' && $value){
				$GLOBALS['app']->lib('page')->home_str($value);
			}
			if($key == 'prev' && $value){
				$GLOBALS['app']->lib('page')->prev_str($value);
			}
			if($key == 'next' && $value){
				$GLOBALS['app']->lib('page')->next_str($value);
			}
			if($key == 'last' && $value){
				$GLOBALS['app']->lib('page')->last_str($value);
			}
			if($key == 'half' && $value !=''){
				$GLOBALS['app']->lib('page')->half($value);
			}
			if($key == 'opt' && $value){
				$GLOBALS['app']->lib('page')->opt_str($value);
			}
			if($key == 'add' && $value){
				$GLOBALS['app']->lib('page')->add_up($value);
			}
			if($key == 'always' && $value){
				$GLOBALS['app']->lib('page')->always($value);
			}
		}
	}
	if($num<1){
		$num = 1;
	}
	return $GLOBALS['app']->lib('page')->page($url,$total,$num,$psize);
}

function phpok_plugin()
{
	$rslist = $GLOBALS['app']->model('plugin')->get_all(1);
	if(!$rslist){
		return false;
	}
	$id = $GLOBALS['app']->app_id;
	$ctrl = $GLOBALS['app']->ctrl;
	$func = $GLOBALS['app']->func;
	
	//装载插件
	foreach($rslist AS $key=>$value)
	{
		if(is_file($GLOBALS['app']->dir_root.'plugins/'.$key.'/'.$id.'.php')){
			if($value['param']){
				$value['param'] = unserialize($value['param']);
			}
			include($GLOBALS['app']->dir_root.'plugins/'.$key.'/'.$id.'.php');
			$name = $id.'_'.$key;
			$cls = new $name();
			$func_name = $ctrl.'_'.$func;
			$mlist = get_class_methods($cls);
			if($mlist && in_array($func_name,$mlist)){
				echo $cls->$func_name($value);
			}
		}
	}
}

// 根据图片存储的ID，获得图片信息
function phpok_image_rs($img_id)
{
	if(!$img_id) return false;
	return $GLOBALS['app']->model('res')->get_one($img_id);
}

//显示评论信息
//回复数据调用
function phpok_reply($id,$psize=10,$orderby="ASC",$vouch=false)
{
	$condition = "tid='".$id."' AND parent_id='0' ";
	$sessid = $GLOBALS['app']->session->sessid();
	$uid = 0;
	$condition .= " AND (status=1 OR (status=0 AND session_id='".$sessid."')) ";
	if($vouch){
		$condition .= " AND vouch=1 ";
	}
	$total = $GLOBALS['app']->model('reply')->get_total($condition);
	if(!$total) return false;
	//判断当前页
	$pageid = $GLOBALS['app']->get($GLOBALS['app']->config['pageid'],'int');
	if(!$pageid) $pageid = 1;
	$order = strtoupper($orderby) == 'ASC' ? 'id ASC' : 'id DESC';
	$offset = ($pageid-1) * $psize;
	$rslist = $GLOBALS['app']->model('reply')->get_list($condition,$offset,$psize,"",$order);
	//读子主题
	$idlist = array();
	foreach($rslist AS $key=>$value){
		$idlist[] = $value["id"];
	}
	
	//整理回复列表
	foreach($rslist AS $key=>$value)
	{
		$rslist[$key] = $value;
	}
	//返回结果集
	$array = array('list'=>$rslist,'psize'=>$psize,'pageid'=>$pageid,'total'=>$total);
	return $array;
}

function phpok_ip()
{
	return $GLOBALS['app']->lib('common')->ip();
}

//网址，下划线分割字符法
function phpok_url($rs)
{
	if(!$rs) return false;
	if(is_string($rs)) parse_str($rs,$rs);
	$ctrl = $func = $id = $appid = "";
	if($rs["ctrl"]) $ctrl = $rs["ctrl"];
	if($rs["func"]) $func = $rs["func"];
	if($rs["id"]) $id = $rs["id"];
	if($rs['appid']) $appid = $rs['appid'];
	if(!$ctrl && !$id) return false;
	if(!$ctrl)
	{
		$ctrl = $id;
		$id = "";
	}
	$tmp = array();
	foreach($rs AS $key=>$value)
	{
		if(!in_array($key,array("ctrl","id","func",'appid')))
		{
			if($key == '_nocache' && $value != 'false' && $value != '0')
			{
				$tmp[] = '_noCache=0.'.$GLOBALS['app']->time.rand(1000,9999);
			}
			else
			{
				$tmp[] = $key."=".rawurlencode($value);
			}
		}
	}
	if($ctrl && $id && $id != $ctrl)
	{
		$tmp[] = "id=".rawurlencode($rs["id"]);
	}
	$string = ($tmp && count($tmp)>0) ? implode("&",$tmp) : "";
	return $GLOBALS['app']->url($ctrl,$func,$string,$appid);
}


function time_format($timestamp)
{
	$current_time = $GLOBALS['app']->time;
    $since = abs($current_time-$timestamp);
    if(floor($since/3600))
	{
        if(date('Y-m-d',$timestamp) == date('Y-m-d',$current_time))
		{
            $output = '今天 ';
            $output.= date('H:i',$timestamp);
        }
		else
		{
            if(date('Y',$timestamp) == date('Y',$current_time))
			{
                $output = date('m月d日 H:i',$timestamp);
            }
			else
			{
                $output = date('Y年m月d日',$timestamp);
            }
        }
    }
	else
	{
        if(($output=floor($since/60)))
		{
            $output = $output.'分钟前';
        }
		else
		{
			$output = '1分钟内';
		}
    }
    return $output;
}

//前台取得地址表单
function phpok_address($format=false)
{
	$shipping = $GLOBALS['app']->site['biz_shipping'];
	$billing = $GLOBALS['app']->site['biz_billing'];
	if(!$shipping && !$billing) return false;
	$rs = array();
	if($shipping)
	{
		$flist = $GLOBALS['app']->call->phpok('_fields',array("pid"=>$shipping,'fields_format'=>$format,'prefix'=>"s_"));
		$rs['shipping'] = $flist;
	}
	if($billing)
	{
		$flist = $GLOBALS['app']->call->phpok('_fields',array("pid"=>$billing,'fields_format'=>$format,'prefix'=>'b_'));
		$rs['billing'] = $flist;
	}
	return $rs;
}

//判断属性是否存在
function in_attr($str="",$info="")
{
	if(!$str || !$info) return false;
	$info = explode(",",$info);
	$str = explode(",",$str);
	$rs = array_intersect($str,$info);
	if($rs && count($rs)>0) return true;
	return false;
}

//读取文本内容，并格式化文本内容
function phpok_txt($file,$pageid=0,$type='txt')
{
	if(!$file || !is_file($GLOBALS['app']->dir_root.$file))
	{
		return false;
	}
	$content = file_get_contents($GLOBALS['app']->dir_root.$file);
	if(!$content) return false;
	$rs = $GLOBALS['app']->model('data')->info_page($content,$pageid);
	if(!$rs) return false;
	if(is_string($rs))
	{
		$rs = $type == 'txt' ? nl2br($rs) : $rs;
		return array('content'=>$rs);
	}
	else
	{
		$rs['content'] = $type == 'txt' ? nl2br($rs['content']) : $rs;
		return $rs;
	}
}


