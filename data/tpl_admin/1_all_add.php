<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><?php $this->output("head_open","file"); ?>
<script type="text/javascript">
function add_site_save()
{
	var url = get_url("all",'addok');
	var title = $("#title").val();
	if(!title){
		$.dialog.alert("<?php echo P_Lang("网站名称不能为空");?>");
		return false;
	}
	var domain = $("#domain").val();
	if(!domain){
		$.dialog.alert("<?php echo P_Lang("域名不能为空");?>");
		return false;
	}
	url += "&title="+$.str.encode(title)+"&domain="+$.str.encode(domain);
	$.dialog.tips('正在添加站点，请稍候…');
	$.phpok.json(url,function(rs){
		if(rs.status == 'ok'){
			$.dialog.alert(rs.content,function(){
				top.$.phpok.reload();
			});
		}else{
			if(!rs.content) rs.content = "<?php echo P_Lang("创建失败");?>";
			$.dialog.alert(rs.content);
			return false;
		}
	});
	return false;
}
</script>
<form method="post" action="javascript:void(0)" onsubmit="return add_site_save();return false;">
<div class="table">
	<div class="title">
		<?php echo P_Lang("网站名称：");?>
		<span class="note"><?php echo P_Lang("设置网站的名称");?></span>
	</div>
	<div class="content">
		<input type="text" id="title" name="title" class="default" />
	</div>
</div>

<div class="table">
	<div class="title">
		<?php echo P_Lang("网站主域名：");?>
		<span class="note"><?php echo P_Lang("设置访问此网站的域名，此项不能为空");?></span>
	</div>
	<div class="content">
		<input type="text" id="domain" name="domain" class="default" />
	</div>
</div>
<div class="table">
	<div class="content">
		<input type="submit" value="<?php echo P_Lang("提交");?>" class="submit" />
	</div>
</div>
</form>
<?php $this->output("foot_open","file"); ?>