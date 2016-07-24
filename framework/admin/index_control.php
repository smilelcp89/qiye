<?php
/***********************************************************
	Filename: phpok/admin/index_control.php
	Note	: 后台首页控制台
	Version : 4.0
	Web		: www.phpok.com
	Author  : qinggan <qinggan@188.com>
	Update  : 2012-10-19 13:03
***********************************************************/
if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class index_control extends phpok_control
{
	function __construct()
	{
		parent::control();
	}

	public function index_f()
	{
		if(!$this->license_code){
			$this->license = "LGPL";
		}
		$license = strtoupper($this->license);
		$code = P_Lang('LGPL开源授权');
		if($license == "PBIZ" && $this->license_code && $this->license_name){
			$code = P_Lang('个人（{license}）商业授权',array('license'=>$this->license_name));
		}elseif($license == "CBIZ" && $this->license_code && $this->license_name){
			$code = P_Lang('企业（{license}）商业授权',array('license'=>$this->license_name));
		}
		$license_site = $this->license_site;
		if(substr($license_site,0,1) == '.'){
			$license_site = substr($license_site,1);
		}
		$this->assign('license_site',$license_site);
		$this->assign("license",$code);
		$this->assign("version",$this->version);
		$sitelist = $this->model('site')->get_all_site();
		$this->assign('sitelist',$sitelist);
		$menulist = $this->model('sysmenu')->get_all($_SESSION["admin_site_id"],1);
		if(!$menulist){
			$menulist = array();
		}
		$ftmp = array('list','index','res');
		foreach($menulist AS $key=>$value){
			if(!$value["sublist"] || !is_array($value["sublist"]) || count($value["sublist"]) < 1){
				unset($menulist[$key]);
				continue;
			}
			foreach($value["sublist"] AS $k=>$v){
				$ext = "menu_id=".$v["id"];
				if($v["identifier"]){
					$ext .= "&identifier=".$v["identifier"];
				}
				if($v['ext']){
					$ext .= "&".$v['ext'];
				}
				$v['url'] = $this->url($v['appfile'],$v['func'],$ext);
				$value['sublist'][$k] = $v;
			}
			if(!$value["sublist"] || !is_array($value["sublist"]) || count($value["sublist"]) < 1){
				unset($menulist[$key]);
				continue;
			}
			$menulist[$key] = $value;
		}
		$this->assign('menulist',$menulist);
		
		if($menulist){
			$iconlist = false;
			foreach($menulist as $key=>$value){
				if($value['sublist']){
					foreach($value['sublist'] as $k=>$v){
						if($v['icon']){
							$iconlist[] = $v;
						}
					}
				}
			}
			if($iconlist){
				$this->assign('iconlist',$iconlist);
			}
		}
		$all_info = $this->all_info();
		if($all_info){
			$this->assign('all_info',$all_info);
		}
		$list_setting = $this->list_setting();
		if($list_setting){
			$this->assign('list_setting',$list_setting);
		}
		$this->view("index");
	}

	function all_setting_f()
	{
		$info = $this->all_info();
		if(!$info){
			$this->json(false);
		}
		$this->json($info,true);
	}

	function all_info()
	{
		$rslist = $this->model('site')->all_list($_SESSION["admin_site_id"]);
		$this->assign("all_rslist",$rslist);
		$rs = $this->model('site')->get_one($_SESSION['admin_site_id']);
		$this->assign("all_rs",$rs);
		return $this->fetch('index_block_allsetting');
	}

	function list_setting_f()
	{
		$info = $this->list_setting();
		if(!$info){
			$this->json(false);
		}
		$this->json($info,true);
	}

	function list_setting()
	{
		$site_id = $_SESSION["admin_site_id"];
		$rslist = $this->model('project')->get_all($site_id,0,"p.status=1 AND p.hidden=0");
		if(!$rslist || count($rslist)< 1){
			return false;
		}
		foreach($rslist as $key=>$value){
			$value['url'] = $this->url('list','action','id='.$value['id']);
			$rslist[$key] = $value;
		}
		$this->assign('list_rslist',$rslist);
		return $this->fetch('index_block_listsetting');
	}

	function clear_f()
	{
		$this->lib('file')->rm($this->dir_root."data/tpl_www/");
		$this->lib('file')->rm($this->dir_root."data/tpl_admin/");
		$this->cache->clear();
		$this->json(true);
	}

	function site_f()
	{
		$siteid = $this->get("id","int");
		if(!$siteid){
			error(P_Lang('请选择要维护的站点'),$this->ur('index'));
		}
		$rs = $this->model("site")->get_one($siteid);
		if(!$rs){
			error(P_Lang('站点信息不存在'),$this->url("index"));
		}
		$_SESSION['admin_site_id'] = $siteid;
		$tip = P_Lang('您正在切换到网站：{sitename}，请稍候…',array('sitename'=>"<span style='color:red;font-weight:bold;'>".$rs['title']."</span>"));
		error($tip,$this->url("index"),"ok");
	}

	//获取待处理信息
	function pendding_f()
	{
		$list = false;
		//读取未操作的主题
		$rslist = $this->model('list')->pending_info($_SESSION['admin_site_id']);
		if($rslist){
			foreach($rslist AS $key=>$value){
				if(!$value['parent_id']){
					$url = $this->url("list","action","id=".$value["pid"]);
					$list['project_'.$value['pid']] = array("title"=>$value["title"],"total"=>$value["total"],"url"=>$url,'id'=>$value['pid']);
				}
			}
			//合并子项目提示
			foreach($rslist as $key=>$value){
				if(!$value['total'] || !$value['parent_id']){
					continue;
				}
				if($list['project_'.$value['parent_id']]){
					$list['project_'.$value['parent_id']]['total'] += $value['total'];
				}else{
					$url = $this->url("list","action","id=".$value["pid"]);
					$list['project_'.$value['parent_id']] = array("title"=>$value["title"],"total"=>$value["total"],"url"=>$url,'id'=>$value['parent_id']);
				}
			}
			if($list){
				foreach($list as $key=>$value){
					if(!$value['total']){
						unset($list[$key]);
					}
				}
			}
		}
		if(!$list){
			$this->json(P_Lang('没有消息'));
		}
		$this->json($list,true);
	}

	function pendding_sublist_f()
	{
		$list = false;
		$rslist = $this->model('list')->pending_info($_SESSION['admin_site_id']);
		if($rslist){
			foreach($rslist AS $key=>$value){
				if($value['parent_id']){
					$url = $this->url("list","action","id=".$value["pid"]);
					$list['project_'.$value['pid']] = array("title"=>$value["title"],"total"=>$value["total"],"url"=>$url,'id'=>$value['pid']);
				}
			}
		}
		if(!$list){
			$this->json(P_Lang('没有消息'));
		}
		$this->json($list,true);
	}
}
?>