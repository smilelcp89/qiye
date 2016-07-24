<?php
/***********************************************************
	Filename: {phpok}/admin/site_control.php
	Note	: 站点管理器
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2013年10月8日
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class site_control extends phpok_control
{
	public function __construct()
	{
		parent::control();
	}

	public function index_f()
	{
		$rslist = $this->model('site')->get_all_site();
		$this->assign("rslist",$rslist);
		$this->view("site_list");
	}

	public function delete_f()
	{
		$id = $this->get("id","int");
		if(!$id) $this->json(P_Lang('未指定ID'));
		$rs = $this->model('site')->get_one($id);
		if(!$rs) $this->json(P_Lang('站点信息不存在'));
		if($rs['is_default']){
			$this->json(P_Lang('默认站点不支持删除操作'));
		}
		//删除网站内容
		$this->model("site")->site_delete($id);
		if($id == $_SESSION['admin_site_id']){
			$d_rs = $this->model('site')->get_one_default();
			$_SESSION['admin_site_id'] = $d_rs['id'];
		}
		$this->json(P_Lang('网站删除成功'),true);
	}

	public function default_f()
	{
		$id = $this->get("id","int");
		if(!$id) $this->json(P_Lang('未指定站点信息'));
		$rs = $this->model('site')->get_one($id);
		if(!$rs) $this->json(P_Lang('站点信息不存在'));
		if($rs['is_default']) $this->json(P_Lang('默认站点不支持此操作'));
		$this->model('site')->set_default($id);
		$this->json(P_Lang('默认站点设置成功'),true);
	}
	public function alias_f()
	{
		$id = $this->get('id','int');
		if(!$id){
			$this->json(P_Lang('未指定站点ID'));
		}
		$alias = $this->get('alias');
		if(!$alias){
			$this->json(P_Lang('未指定别名'));
		}
		$this->model('site')->alias_save($alias,$id);
		$this->json(true);
	}
}
?>