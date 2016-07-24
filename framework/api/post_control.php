<?php
/***********************************************************
	Filename: {phpok}/api/post_control.php
	Note	: 存储发布的项目信息
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2013年11月11日
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class post_control extends phpok_control
{
	public function __construct()
	{
		parent::control();
	}

	public function edit_f()
	{
		$this->save_f();
	}

	public function save_f()
	{
		if($this->config['is_vcode'] && function_exists('imagecreate')){
			$code = $this->get('_chkcode');
			if(!$code){
				$this->json(P_Lang('验证码不能为空'));
			}
			$code = md5(strtolower($code));
			if($code != $_SESSION['vcode']){
				$this->json(P_Lang('验证码填写不正确'));
			}
			unset($_SESSION['vcode']);
		}
		$id = $this->get('id','system');
		if(!$id){
			$this->json(P_Lang('未绑定相应的项目'));
		}
		$project_rs = $this->call->phpok('_project','phpok='.$id);
		if(!$project_rs || !$project_rs['status']){
			$this->json(P_Lang('项目信息不存在或未启用'));
		}
		if(!$project_rs['module']){
			$this->json(P_Lang('此项目没有表单功能'));
		}
		if(!$project_rs['post_status']){
			$this->json(P_Lang('此项目未启用发布功能'));
		}
		$array = array();
		$array["title"] = $this->get("title");
		if(!$array['title']){
			$tip = $project_rs['alias_title'] ? $project_rs['alias_title'] : P_Lang('主题');
			$this->json($tip.' '.P_Lang("不能为空"));
		}
		$tid = $this->get('tid','int');
		$array["status"] = 0;
		$array["hidden"] = 0;
		$array["module_id"] = $project_rs["module"];
		$array["project_id"] = $project_rs["id"];
		$array["site_id"] = $project_rs["site_id"];
		$array["cate_id"] = $this->get("cate_id","int");
		$array['user_id'] = 0;
		if($tid){
			$get_result = $this->model('list')->save($array,$tid);
			if(!$get_result){
				$this->json(P_Lang('编辑失败，请联系管理员'));
			}
			if($array["cate_id"]){
		 		$ext_cate = $this->get('ext_cate_id');
		 		if(!$ext_cate){
			 		$ext_cate = array($array["cate_id"]);
		 		}
		 		$this->model('list')->save_ext_cate($tid,$ext_cate);
	 		}
		}else{
			$array["dateline"] = $this->time;
			$insert_id = $this->model('list')->save($array);
			if(!$insert_id){
				$this->json(P_Lang('添加失败，请联系管理'));
			}
			if($array["cate_id"]){
		 		$ext_cate = $this->get('ext_cate_id');
		 		if(!$ext_cate){
			 		$ext_cate = array($array["cate_id"]);
		 		}
		 		$this->model('list')->save_ext_cate($insert_id,$ext_cate);
	 		}
		}
		$ext_list = $this->model('module')->fields_all($project_rs["module"]);
		if(!$ext_list){
			$ext_list = array();
		}
		$tmplist = array();
		if(!$tid){
			$tmplist["id"] = $insert_id;
		}
		$tmplist["site_id"] = $project_rs["site_id"];
		$tmplist["project_id"] = $project_rs["id"];
		$tmplist["cate_id"] = $array["cate_id"];
		foreach($ext_list AS $key=>$value){
			$val = ext_value($value);
			if($value["ext"]){
				$ext = unserialize($value["ext"]);
				foreach($ext AS $k=>$v){
					$value[$k] = $v;
				}
			}
			if($value["form_type"] == "password"){
				$content = $rs[$value["identifier"]] ? $rs[$value["identifier"]] : $value["content"];
				$val = ext_password_format($val,$content,$value["password_type"]);
			}
			if($val){
				$tmplist[$value["identifier"]] = $val;
			}
		}
		if($tid){
			$this->model('list')->update_ext($tmplist,$project_rs['module'],$tid);
			$this->json(P_Lang('内容编辑成功'),true);
		}
		$this->model('list')->save_ext($tmplist,$project_rs["module"]);
		if($project_rs['etpl_admin']){
			$email_rs = $this->model('email')->get_identifier($project_rs['etpl_admin']);
			$email = $this->model('admin')->get_mail();
			if($email_rs && $email){
				$tmp = array_merge($tmplist,$array);
				$tmp['id'] = $insert_id;
				$this->assign('rs',$tmp);
				$this->assign('page_rs',$project_rs);
				$title = $this->fetch($email_rs["title"],"content");
				$content = $this->fetch($email_rs["content"],"content");
				$this->lib('email')->send_admin($title,$content,$email);
			}
		}
		$this->json(true);
	}
}
?>