<?php
/***********************************************************
	Filename: {phpok}/admin/call_control.php
	Note	: 数据调用中心
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2013-04-18 02:22
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class call_control extends phpok_control
{
	private $psize = 20;
	private $phpok_type_list;//可调用类型
	public function __construct()
	{
		parent::control();
		$list = array(
			"arclist"=>P_Lang('文章列表')
			,"arc"=>P_Lang('文章内容')
			,"cate"=>P_Lang('分类信息')
			,"catelist"=>P_Lang('分类树')
			,"project"=>P_Lang('项目信息')
			,"sublist"=>P_Lang('子项目信息')
			,"parent"=>P_Lang('上级项目')
			,"fields"=>P_Lang('字段及表单')
		);
		$this->phpok_type_list = $list;
		$this->assign("phpok_type_list",$list);
	}

	function phpok_autoload()
	{
		$site_id = $_SESSION["admin_site_id"];
		$this->model('call')->site_id($site_id);
		$psize = $this->config["psize"] ? $this->config["psize"] : 20;
		$this->model('call')->psize($psize);
		$this->psize = $psize;
	}

	public function index_f()
	{
		$this->phpok_autoload();
		$pageid = $this->get($this->config["pageid"],"int");
		if(!$pageid) $pageid = 1;
		$keywords = $this->get("keywords");
		$pageurl = $this->url("call");
		$condition = "";
		if($keywords){
			$this->assign("keywords",$keywords);
			$pageurl.="&keywords=".rawurlencode($keywords)."&";
			$condition = " (title LIKE '%".$keywords."%' OR note LIKE '%".$keywords."%' OR identifier LIKE '%".$keywords."%') ";
		}
		$rslist = $this->model('call')->get_list($condition,$pageid);
		$this->assign("rslist",$rslist);
		$total = $this->model('call')->get_count($condition);
		$this->assign("total",$total);
		$string = 'home='.P_Lang('首页').'&prev='.P_Lang('上一页').'&next='.P_Lang('下一页').'&last='.P_Lang('尾页').'&half=5';
		$string.= '&add='.P_Lang('数量：').'(total)/(psize)'.P_Lang('，').P_Lang('页码：').'(num)/(total_page)&always=1';
		$pagelist = phpok_page($pageurl,$total,$pageid,$this->psize,$string);
		$this->assign("pagelist",$pagelist);
		$attrlist = $this->model('list')->attr_list();
		$this->assign("attrlist",$attrlist);
		$this->view("phpok_index");
	}

	public function set_f()
	{
		$this->phpok_autoload();
		$id = $this->get("id");
		if($id){
			$rs = $this->model('call')->get_one($id);
			if($rs['ext']){
				$ext = unserialize($rs['ext']);
				unset($rs['ext']);
				if($ext) $rs = array_merge($ext,$rs);
			}
			$this->assign("rs",$rs);
			$this->assign("id",$id);
		}
		$site_id = $_SESSION["admin_site_id"];
		$rslist = $this->model('project')->get_all_project($site_id);
		$this->assign("rslist",$rslist);
		$attrlist = $this->model('list')->attr_list();
		$this->assign("attrlist",$attrlist);
		$this->view("phpok_set");
	}

	//取得分类列表
	public function cate_list_f()
	{
		$id = $this->get("id","int");
		if(!$id){
			$this->json(P_Lang('未指定项目ID'));
		}
		$val = $this->get("val");
		if($val){
			$val = explode(",",$val);
			$this->assign("val",$val);
		}
		$rs = $this->model('project')->get_one($id);
		$this->assign("rs",$rs);
		if(!$rs["cate"]){
			$this->json(P_Lang('无分类'));
		}
		$cate_rs = $this->model('cate')->cate_info($rs["cate"],false);
		$this->assign("cate_rs",$cate_rs);
		$catelist = $this->model('cate')->get_all($rs["site_id"],0,$rs["cate"]);
		$catelist = $this->model('cate')->cate_option_list($catelist);
		$this->assign("catelist",$catelist);
		$content = $this->fetch("phpok_ajax_cate");
		$this->json($content,true);
	}
	
	public function type_list_f()
	{
		$id = $this->get("id","int");
		if(!$id){
			$val = $this->get("val");
			$this->assign("val",$val);
			$content = $this->fetch("phpok_ajax_list");
			$this->json($content,true);
		}
		$this->assign("id",$id);
		$val = $this->get("val");
		$this->assign("val",$val);
		$rs = $this->model('project')->get_one($id);
		$this->assign("rs",$rs);
		$son_rs = $this->model('project')->get_son($id);
		if($son_rs){
			$this->assign("son_rs",$son_rs);
		}
		if($rs["module"]){
			if($rs["cate"]){
				$cate_rs = $this->model('cate')->get_one($rs["cate"]);
				$catelist = array();
				$this->model('cate')->get_sublist($list,$rs["cate"]);
				$this->assign("catelist",$catelist);
			}
		}
		$content = $this->fetch("phpok_ajax_list");
		$this->json($content,true);
	}

	public function fields_f()
	{
		$pid = $this->get("pid","int");
		if(!$pid){
			$this->json(P_Lang('未指定ID'));
		}
		$p_rs = $this->model('project')->get_one($pid,false);
		if(!$p_rs['module']){
			$this->json(P_Lang('未绑定模块'));
		}
		$rslist = $this->model('module')->fields_all($p_rs['module']);
		$this->assign('rslist',$rslist);
		$info = $this->fetch("phpok_ajax_fields");
		$this->json($info,true);
	}

	public function arclist_f()
	{
		$pid = $this->get("pid","int");
		if(!$pid){
			$this->json(P_Lang('未指定ID'));
		}
		$p_rs = $this->model('project')->get_one($pid,false);
		if(!$p_rs['module']){
			$this->json(P_Lang('未绑定模块'));
		}
		$rslist = $this->model('module')->fields_all($p_rs['module']);
		$this->assign('rslist',$rslist);
		$info = $this->fetch("phpok_ajax_fields");
		$order = $this->fetch("phpok_ajax_orderby");
		$this->json(array('need'=>$info,'orderby'=>$order),true);
	}

	private function check_identifier($identifier)
	{
		if(!$identifier){
			return P_Lang('未指定标识串');
		}
		$identifier = strtolower($identifier);
		if(!preg_match("/^[a-z][a-z0-9\_\-]+$/u",$identifier)){
			return P_Lang('字段标识不符合系统要求，限小写字母、数字、中划线及下划线且必须是小写字母开头');
		}
		$rs = $this->model('call')->chk_identifier($identifier);
		if($rs){
			return P_Lang('字符串已被使用');
		}
		return "ok";		
	}

	public function check_f()
	{
		$identifier = $this->get("identifier");
		$check = $this->check_identifier($identifier);
		$this->json($check == 'ok' ? true : $check);
	}

	public function save_f()
	{
		$id = $this->get("id","int");
		$this->phpok_autoload();
		$array = array();
		$error_url = $this->url("call","set");
		if(!$id)
		{
			$identifier = $this->get("identifier");
			$chk = $this->check_identifier($identifier);
			if($chk != "ok"){
				error($chk,$error_url,"error");
			}
			$array["identifier"] = $identifier;
			$array["site_id"] = $_SESSION["admin_site_id"];
		}else{
			$error_url .= "&id=".$id;
		}
		$title = $this->get("title");
		if(!$title){
			error(P_Lang('备注不能为空'),$error_url,"error");
		}
		$array["title"] = $title;
		$array["pid"] = $this->get("pid","int");
		$array["type_id"] = $this->get("type_id");
		$array["status"] = $this->get("status","int");
		$array["cateid"] = $this->get("cateid",'int');
		$ext = array();
		$ext['psize'] = $this->get("psize",'int');
		$ext['offset'] = $this->get("offset",'int');
		$ext['is_list'] = $this->get("is_list",'int');
		$ext['attr'] = $this->get('attr');
		$ext['fields_need'] = $this->get('fields_need');
		$ext['tag'] = $this->get('tag');
		$ext['keywords'] = $this->get('keywords');
		$ext['orderby'] = $this->get('orderby');
		$ext['cate'] = $this->get('cate');
		$ext['fields_format'] = $this->get('fields_format','int');
		$ext['user'] = $this->get('user');
		$ext['in_sub'] = $this->get('in_sub','int');
		$ext['title_id'] = $this->get('title_id');
		$array['ext'] = serialize($ext);
		$id = $this->model('call')->save($array,$id);
		error(P_Lang('数据调用中心配置成功'),$this->url("call"),"ok");
	}

	public function delete_f()
	{
		$id = $this->get("id","int");
		if(!$id){
			$this->json(P_Lang('未指定ID'));
		}
		$this->model('call')->del($id);
		$this->json(P_Lang('删除成功'),true);
	}

	//取得模块的扩展字段
	public function mfields_f()
	{
		$id = $this->get("id");
		if(!$id){
			$this->json(P_Lang('未指定项目ID'));
		}
		$rs = $this->model('project')->get_one($id);
		if(!$rs || !$rs["module"]){
			$this->json(P_Lang('无数据或未设置模块'));
		}
		$mid = $rs["module"];
		$rslist = $this->model('module')->fields_all($mid);
		if(!$rslist){
			$this->json(P_Lang('没有自定义字段'));
		}
		$list = array();
		foreach($rslist AS $key=>$value){
			if($value["field_type"] != "longtext" && $value["field_type"] != "longblob" && $value["field_type"] != "text"){
				$list[] = array("id"=>$value["id"],"identifier"=>$value["identifier"],"title"=>$value["title"]);
			}
		}
		$this->json($list,true);
	}

}
?>