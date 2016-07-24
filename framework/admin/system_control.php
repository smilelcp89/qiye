<?php
/***********************************************************
	Filename: {phpok}/admin/system_control.php
	Note	: 核心配置
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2012-12-04 16:07
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class system_control extends phpok_control
{
	function __construct()
	{
		parent::control();
	}

	# 核心配置列表页，这里显示全部，不分页
	public function index_f()
	{
		$rslist = $this->model('sysmenu')->get_all($_SESSION["admin_site_id"]);
		$this->assign("rslist",$rslist);
		$this->view("sysmenu_index");
	}

	# 添加子项目
	function set_f()
	{
		$id = $this->get("id","int");
		$pid = $this->get("pid","int");
		if($id){
			$rs = $this->model('sysmenu')->get_one($id);
			$this->assign("id",$id);
			$this->assign("rs",$rs);
			$pid = $rs["parent_id"];
			$popedom_list = $this->model('popedom')->get_list($id);
			$this->assign("popedom_list",$popedom_list);
		}
		if($pid)
		{
			$parent_list = $this->model('sysmenu')->get_list(0,0);
			$this->assign("parent_list",$parent_list);
			$this->assign("pid",$pid);
			$list = $this->lib('file')->ls($this->dir_phpok."admin");
			$dirlist = array();
			foreach($list AS $key=>$value)
			{
				$tmp = str_replace("_control.php","",strtolower(basename($value)));
				if(strpos($tmp,".func.php") === false)
				{
					$dirlist[$key] = array("id"=>$tmp,"title"=>basename($value));
				}
			}
			$this->assign("dirlist",$dirlist);
			$css = $this->lib("file")->cat($this->dir_root.'css/icomoon.css');
			preg_match_all("/\.icon-([a-z\-0-9]*):before\s*(\{|,)/isU",$css,$iconlist);
			$iconlist = $iconlist[1];
			sort($iconlist);
			$this->assign('iconlist',$iconlist);
		}
		$this->view("sysmenu_set");
	}

	# 存储项目
	// 没试
	function save_f()
	{
		$id = $this->get("id","int");
		$popedom_id = $id ? 'modify' : 'add';
		$error_url = admin_url("system","set");
		if($id) $error_url .= "&id=".$id;
		$title = $this->get("title");
		if(!$title){
			error(P_Lang('名称不能为空'),$error_url,"error");
		}
		$array = array();
		$array["title"] = $title;
		$array["taxis"] = $this->get("taxis","int");
		$array["appfile"] = $this->get("appfile");
		$array['icon'] = $this->get('icon');
		$array['func'] = $this->get('func');
		$array['ext'] = $this->get('ext');
		if(!$id){
			$parent_id = $this->get("parent_id","int");
			if(!$parent_id){
				error(P_Lang('未指定上一级项目'),$error_url);
			}
			if(!$array["appfile"]){
				error(P_Lang('未指定控制层'),$error_url,'error');
			}
			$array["parent_id"] = $parent_id;
			$array["site_id"] = $_SESSION["admin_site_id"];
			$id = $this->model('sysmenu')->save($array);
			if(!$id){
				error(P_Lang('项目添加失败'),$error_url,"ok");
			}
			error(P_Lang('项目添加成功'),$this->url("system"),"ok");
		}
		$rs = $this->model('sysmenu')->get_one($id);
		if(!$rs){
			error(P_Lang('获取数据失败，请检查'),$error_url,"error");
		}
		if($rs["parent_id"]){
			$parent_id = $this->get("parent_id","int");
			if(!$parent_id){
				$parent_id = $rs["parent_id"];
			}
		}else{
			$parent_id = 0;
		}
		$array["parent_id"] = $parent_id;
		$this->model('sysmenu')->save($array,$id);
		error(P_Lang('项目更新成功'),$this->url("system"),"ok");
	}

	//更新状态
	function status_f()
	{
		$id = $this->get("id","int");
		if(!$id){
			$this->json(P_Lang('没有指定ID'));
		}
		$rs = $this->model('sysmenu')->get_one($id);
		if($rs["if_system"]){
			$this->json(P_Lang('系统栏目不支持执行此操作'));
		}
		$status = $rs["status"] ? 0 : 1;
		$action = $this->model('sysmenu')->update_status($id,$status);
		if(!$action){
			$this->json(P_Lang('操作失败，请检查SQL语句'));
		}else{
			$this->json($status,true);
		}
	}

	//批量更新排序
	function taxis_f()
	{
		$taxis = $this->lib('trans')->safe("taxis");
		if(!$taxis || !is_array($taxis)){
			$this->json(P_Lang('没有指定要更新的排序'));
		}
		foreach($taxis AS $key=>$value){
			$this->model('sysmenu')->update_taxis($key,$value);
		}
		$this->json(P_Lang('数据排序更新成功'),true);
	}

	function delete_f()
	{
		$id = $this->get('id','int');
		if(!$id){
			$this->json(P_Lang('未指定ID'));
		}
		$rs = $this->model('sysmenu')->get_one($id);
		if(!$rs){
			$this->json(P_Lang('数据记录不存在'));
		}
		if(!$rs['parent_id']){
			$this->json(P_Lang('根导航不允许删除'));
		}
		if($rs['if_system']){
			$this->json(P_Lang('核心导航操作不允许删除'));
		}
		$this->model('sysmenu')->delete($id);
		$this->json(P_Lang('删除成功'),true);
	}
}
?>