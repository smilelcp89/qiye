<?php
/***********************************************************
	Filename: {phpok}/api/email_control.php
	Note	: 邮件相关操作
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2015年06月13日 11时31分
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class email_control extends phpok_control
{
	public function __construct()
	{
		parent::control();
	}

	//邮件发送
	public function index_f()
	{
		if(!$_SESSION['admin_id']){
			$token = $this->get('token');
			if(!$token){
				$this->json(P_Lang('Email获取异常，未指定Token信息'));
			}
			$info = $this->lib('token')->decode($token);
			if(!$info || !$info['email']){
				$this->json(P_Lang('异常，内容不能为空'));
			}
			$email = $info['email'];
		}else{
			$email = $this->get('email');
			if(!$email){
				$this->json(P_Lang('Email不能为空'));
			}
		}
		$title = $this->get('title');
		$content = $this->get('content','html');
		if(!$content){
			$this->json(P_Lang('邮件内容不能为空'));
		}
		if(!$title){
			$title = phpok_cut($content,50,'…');
		}
		if(!$this->site['email_server'] || !$this->site['email_account'] || !$this->site['email_pass'] || !$this->site['email']){
			$this->json(P_Lang('SMTP未配置好'));
		}
		$list = explode(',',$email);
		foreach($list AS $key=>$value){
			$value = trim($value);
			if($value && phpok_check_email($value)){
				$value_name = str_replace(strstr($value,'@'),'',$value);
				$info = $this->lib('email')->send_mail($value,$title,$content,$value_name);
				if(!$info){
					$this->json($this->lib('email')->error());
				}
			}
		}
		$this->json(true);		
	}
}
?>