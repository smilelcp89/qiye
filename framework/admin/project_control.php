<?php
/***********************************************************
	Filename: {phpok}/admin/project_control.php
	Note	: 项目任务处理器
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2012-12-26 11:50
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class project_control extends phpok_control
{
	function __construct()
	{
		parent::control();
	}

	public function index_f()
	{
		$site_id = $_SESSION["admin_site_id"];
		$rslist = $this->model('project')->get_all_project($site_id);
		$this->assign("rslist",$rslist);
		$this->view("project_index");
	}

	public function set_f()
	{
		$site_id = $_SESSION["admin_site_id"];
		$id = $this->get("id","int");
		$idstring = "";
		if($id){
			$this->assign("id",$id);
			$rs = $this->model('project')->get_one($id);
			$this->assign("rs",$rs);
			$ext_module = "project-".$id;
		}else{
			$rs = array();
			$ext_module = "add-project";
			$parent_id = $this->get("pid");
			$rs["parent_id"] = $parent_id;
			$rs['taxis'] = $this->model('project')->project_next_sort($parent_id);
			$this->assign("rs",$rs);
		}		
		$parent_list = $this->model('project')->get_all($site_id,0);
		$this->assign("parent_list",$parent_list);
		$this->assign("ext_module",$ext_module);
		$forbid = array("id","identifier");
		$forbid_list = $this->model('ext')->fields("project");
		$forbid = array_merge($forbid,$forbid_list);
		$forbid = array_unique($forbid);
		$this->assign("ext_idstring",implode(",",$forbid));
		$module_list = $this->model('module')->get_all();
		$this->assign("module_list",$module_list);
		$site_id = $_SESSION["admin_site_id"];
		$catelist = $this->model('cate')->root_catelist($site_id);
		$this->assign("catelist",$catelist);
		$emailtpl = $this->model('email')->simple_list($site_id);
		$this->assign("emailtpl",$emailtpl);

		$c_rs = $this->model('sysmenu')->get_one_condition("appfile='list' AND parent_id>0");
		$gid = $c_rs["id"];
		unset($c_rs);
		$note_content = form_edit('admin_note',$rs['admin_note'],"editor","btn_image=1&height=300");
		$this->assign('note_content',$note_content);
		$icolist = $this->lib('file')->ls('images/ico/');
		if(($rs['ico'] && !in_array($rs['ico'],$icolist)) || !$rs['ico']){
			$rs['ico'] = 'images/ico/default.png';
		}
		$this->assign('icolist',$icolist);
		$this->view("project_set");
	}

	function content_f()
	{
		$id = $this->get("id","int");
		if(!$id){
			error(P_Lang('未指定ID'),$this->url("project"),"error");
		}
		$this->assign("id",$id);
		$rs = $this->model('project')->get_one($id);
		$this->assign("rs",$rs);
		$ext_module = "project-".$id;
		$this->assign("ext_module",$ext_module);
		$extlist = $this->model('ext')->ext_all($ext_module);
		if($extlist){
			$tmp = false;
			foreach($extlist AS $key=>$value){
				if($value["ext"]){
					$ext = unserialize($value["ext"]);
					foreach($ext AS $k=>$v){
						$value[$k] = $v;
					}
				}
				$tmp[] = $this->lib('form')->format($value);
				$this->lib('form')->cssjs($value);
			}
			$this->assign('extlist',$tmp);
		}
		$this->view("project_content");
	}

	//取得模块的扩展字段
	function mfields_f()
	{
		$id = $this->get('id','int');
		if(!$id){
			$this->json(P_Lang('未指定ID'));
		}
		$rslist = $this->model('module')->fields_all($id);
		if(!$rslist) $this->json('',true);

		$list = array();
		foreach($rslist AS $key=>$value){
			if($value["field_type"] != "longtext" && $value["field_type"] != "longblob" && $value["field_type"] != "text"){
				$list[] = array("id"=>$value["id"],"identifier"=>$value["identifier"],"title"=>$value["title"]);
			}
		}
		$this->json($list,true);
	}

	function save_f()
	{
		$id = $this->get('id','int');
		$site_id = $_SESSION["admin_site_id"];
		$title = $this->get("title");
		$identifier = $this->get("identifier");
		$module = $this->get("module","int");
		$cate = $this->get("cate","int");
		if($cate){
			$cate_multiple = $this->get('cate_multiple','int');
		}else{
			$cate_multiple = 0;
		}
		$tpl_index = $this->get("tpl_index");
		$tpl_list = $this->get("tpl_list");
		$tpl_content = $this->get("tpl_content");
		$taxis = $this->get("taxis","int");
		if(!$title){
			$this->json(P_Lang('名称不能为空'));
		}
		$check_rs = $this->check_identifier($identifier,$id,$site_id);
		if($check_rs != "ok"){
			$this->json($check_rs);
		}
		$array = array();
		if(!$id){
			$array["site_id"] = $_SESSION["admin_site_id"];
		}
		$array["parent_id"] = $this->get("parent_id","int");
		$array["module"] = $module;
		$array["cate"] = $cate;
		$array['cate_multiple'] = $cate_multiple;
		$array["title"] = $title;
		$array["nick_title"] = $this->get("nick_title");
		$array["alias_title"] = $this->get("alias_title");
		$array["alias_note"] = $this->get("alias_note");
		$array["psize"] = $this->get("psize","int");
		$array["taxis"] = $taxis;
		$array["tpl_index"] = $tpl_index;
		$array["tpl_list"] = $tpl_list;
		$array["tpl_content"] = $tpl_content;
		$array["ico"] = $this->get("ico");
		$array["orderby"] = $this->get("orderby");
		$array["status"] = $this->get("lock","checkbox") ? 0 : 1;
		$array["hidden"] = $this->get("hidden","checkbox");
		$array["identifier"] = $identifier;
		$array["seo_title"] = $this->get("seo_title");
		$array["seo_keywords"] = $this->get("seo_keywords");
		$array["seo_desc"] = $this->get("seo_desc");
		$array["subtopics"] = $this->get("subtopics",'checkbox');
		$array["is_search"] = $this->get("is_search",'checkbox');
		$array["is_tag"] = $this->get("is_tag",'checkbox');
		$array["admin_note"] = $this->get("admin_note","html");
		$array['post_status'] = $this->get('post_status','checkbox');
		$array['post_tpl'] = $this->get('post_tpl');
		$array['etpl_admin'] = $this->get('etpl_admin');
		$array['is_attr'] = $this->get('is_attr','checkbox');
		$array['is_tpl_content'] = $this->get('is_tpl_content','checkbox');
		$array['is_seo'] = $this->get('is_seo','checkbox');
		$array['is_identifier'] = $this->get('is_identifier','checkbox');
		$array['tag'] = $this->get('tag');
		$ok_url = $this->url("project");
		$c_rs = $this->model('sysmenu')->get_one_condition("appfile='list' AND parent_id>0");
		$gid = $c_rs["id"];
		unset($c_rs);
		if($id){
			$action = $this->model('project')->save($array,$id);
			if(!$action){
				$this->json(P_Lang('编辑失败'));
			}
			$rs = $this->model('project')->get_one($id);
		}else{
			$id = $this->model('project')->save($array);
			if(!$id){
				$this->json(P_Lang('添加失败'));
			}
		}
		$this->_save_tag($id);
		$this->json(true);
	}

	private function _save_tag($id)
	{
		$rs = $this->model('project')->get_one($id,false);
		if($rs['tag']){
			$this->model('tag')->update_tag($rs['tag'],'p'.$id,$_SESSION['admin_site_id']);
		}else{
			$this->model('tag')->stat_delete('p'.$id,"title_id");
		}
		return true;
	}

	function content_save_f()
	{
		$id = $this->get('id','int');
		if(!$id){
			$this->json(P_Lang('未指定ID'));
		}
		$title = $this->get("title");
		if(!$title){
			$this->json(P_Lang('名称不能为空'));
		}
		$array = array("title"=>$title);
		$this->model('project')->save($array,$id);
		ext_save("project-".$id);
		$this->json(true);
	}

	private function check_identifier($sign,$id=0,$site_id=0)
	{
		if(!$sign){
			return P_Lang('标识串不能为空');
		}
		$sign = strtolower($sign);
		//字符串是否符合条件
		if(!preg_match("/[a-z][a-z0-9\_\-\.]+/",$sign)){
			return P_Lang("标识不符合系统要求，限字母、数字及下划线（中划线）且必须是字母开头");
		}
		if(!$site_id){
			$site_id = $_SESSION["admin_site_id"];
		}
		$rs = $this->model('id')->check_id($sign,$site_id,$id);
		if($rs){
			return P_Lang('标识符已被使用');
		}
		return 'ok';
	}

	//删除项目操作
	function delete_f()
	{
		$id = $this->get('id','int');
		if(!$id){
			$this->json(P_Lang('未指定ID'));
		}
		//判断是否有子项目
		$list = $this->model('project')->get_son($id);
		if($list){
			$this->json(P_Lang('已存在子项目，请移除子项目'));
		}
		$rs = $this->model('project')->get_one($id,false);
		if(!$rs){
			$this->json(P_Lang('项目信息不存在'));
		}
		$this->model('project')->delete_project($id);
		$this->model('tag')->stat_delete('p'.$id,"title_id");
		$this->json(true);
	}

	# 设置页面状态
	function status_f()
	{
		$id = $this->get('id','int');
		if(!$id){
			$this->json(P_Lang('未指定ID'));
		}
		$status = $this->get("status","int");
		$this->model('project')->status($id,$status);
		$this->json(true);
	}

	function sort_f()
	{
		$sort = $this->get('sort');
		if(!$sort || !is_array($sort)){
			$this->json(P_Lang('更新排序失败'));
		}
		foreach($sort AS $key=>$value){
			$key = intval($key);
			$value = intval($value);
			$this->model('project')->update_taxis($key,$value);
		}
		$this->json(true);
	}

	//取得根分类
	function rootcate_f()
	{
		$catelist = $this->model('cate')->root_catelist($_SESSION['admin_site_id']);
		$this->json($catelist,true);
	}

	//复制
	public function copy_f()
	{
		$id = $this->get('id');
		if(!$id){
			$this->json(P_Lang('未指定项目ID'));
		}
		$rs = $this->model('project')->get_one($id,false);
		if(!$rs){
			$this->json(P_Lang('项目不存在'));
		}
		//自定义标识串
		$identifier = $rs['identifier'].$_SESSION['admin_id'].$this->time;
		$array = array();
		$array['site_id'] = $_SESSION["admin_site_id"];
		$array["parent_id"] = $rs['parent_id'];
		$array["module"] = $rs['module'];
		$array["cate"] = $cate;
		$array["title"] = $rs['title'];
		$array["nick_title"] = $rs['nick_title'];
		$array["alias_title"] = $rs['alias_title'];
		$array["alias_note"] = $rs['alias_note'];
		$array["psize"] = $rs['psize'];
		$array["taxis"] = $rs['taxis'];
		if($rs['module']){
			$array["tpl_index"] = $rs['tpl_index'];
			$array["tpl_list"] = $rs['tpl_list'] ? $rs['tpl_list'] : $rs['identifier'].'_list';
			$array["tpl_content"] = $rs['tpl_content'] ? $rs['tpl_content'] : $rs['identifier'].'_content';
		}else{
			$array["tpl_index"] = $rs['tpl_index'];
			$array["tpl_list"] = $rs['tpl_list'];
			$array["tpl_content"] = $rs['tpl_content'];
			if(!$array['tpl_list'] && !$array['tpl_content'] && !$array['tpl_index']){
				$array['tpl_index'] = $rs['identifier'].'_page';
			}
		}
		$array["ico"] = $rs['ico'];
		$array["orderby"] = $rs['orderby'];
		$array["status"] = $rs['status'];
		$array["hidden"] = $rs['hidden'];
		$array["identifier"] = $identifier;
		$array["subtopics"] = $rs['subtopics'];
		$array["is_search"] = $rs['is_search'];
		$array["is_tag"] = $rs['is_tag'];
		$array["is_biz"] = $rs['is_biz'];
		$array["currency_id"] = $rs['currency_id'];
		$array['post_status'] = $rs['post_status'];
		$array['comment_status'] = $rs['comment_status'];
		$array['post_tpl'] = $rs['post_tpl'];
		$array['etpl_admin'] = $rs['etpl_admin'];
		$array['etpl_user'] = $rs['etpl_user'];
		$array['etpl_comment_admin'] = $rs['etpl_comment_admin'];
		$array['etpl_comment_user'] = $rs['etpl_comment_user'];
		$array['is_attr'] = $rs['is_attr'];
		$c_rs = $this->model('sysmenu')->get_one_condition("appfile='list' AND parent_id>0");
		$gid = $c_rs["id"];
		$nid = $this->model('project')->save($array);
		if(!$nid){
			$this->json(P_Lang('复制项目失败'));
		}
		$this->json(true);
	}
}
?>