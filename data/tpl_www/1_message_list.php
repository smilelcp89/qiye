<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><?php $title = $cate_rs ? $cate_rs['title'].' - '.$page_rs['title'] : $page_rs['title'];?>
<?php $title=$title;?><?php $this->assign("title",$title); ?><?php $menutitle=$page_rs['title'];?><?php $this->assign("menutitle",$page_rs['title']); ?><?php $this->output("head","file"); ?>
<div class="banner"<?php if($page_rs['banner']){ ?> style="background-image:url('<?php echo $page_rs['banner']['gd']['auto'];?>')"<?php } ?>></div>
<script type="text/javascript">
$(document).ready(function(){
	$("#postform").submit(function(){
		if(!$("#title").val()){
			$.dialog.alert('请填写留言主题');
			return false;
		}
		if(!$("#fullname").val()){
			$.dialog.alert('请填写您的姓名');
			return false;
		}
		if(!$("#email").val() && !$("#mobile").val()){
			$.dialog.alert('请留下您的联系方式，邮箱或者手机');
			return false;
		}
		if(!$("#content").val()){
			$.dialog.alert('请填写您要咨询的内容');
			return false;
		}
		$(this).ajaxSubmit({
			'url':api_url('post','save'),
			'type':'post',
			'dataType':'json',
			'success':function(rs){
				if(rs.status == 'ok'){
					$.dialog.alert('感觉您提交的留言，我们会尽快处理您的留言',function(){
						$.phpok.reload();
					},'succeed');
				}else{
					$.dialog.alert(rs.content);
					return false;
				}
			}
		});
		return false;
	});
});
</script>
<div class="main clearfix">
	<div class="left">
		<?php $this->output("block_contact","file"); ?>
	</div>
	<div class="right">
		<div class="pfw">
			<div class="title">
				<h3><?php echo $page_rs['title'];?></h3>
				<span class="breadcrumbs">
					您现在的位置：
					<a href="<?php echo $sys['url'];?>" title="<?php echo $config['title'];?>">首页</a>
					<span class="arrow">&gt;</span> <a href="<?php echo $page_rs['url'];?>" title="<?php echo $page_rs['title'];?>"><?php echo $page_rs['title'];?></a>
				</span>
			</div>
			<div class="message_post">
			<form method="post" class="form" id="postform">
			<input type="hidden" name="id" value="<?php echo $page_rs['identifier'];?>" />
			<?php $list = phpok('_fields','pid='.$page_rs['id'].'&fields_format=1&in_title=1');?>
			<?php $tmpid["num"] = 0;$list=is_array($list) ? $list : array();$tmpid["total"] = count($list);$tmpid["index"] = -1;foreach($list AS $key=>$value){ $tmpid["num"]++;$tmpid["index"]++; ?>
			<div class="table clearfix" id="form_<?php echo $value['identifier'];?>">
				<div class="l"><?php echo $value['title'];?>：</div>
				<div class="r"><?php echo $value['html'];?></div>
			</div>
			<?php } ?>
			<?php if($sys['is_vcode'] && function_exists('imagecreate')){ ?>
			<div class="table clearfix" id="form_<?php echo $value['identifier'];?>">
				<div class="l">验证码：</div>
				<div class="r">
					<input class="vcode"  type="text" name="_chkcode" id="_chkcode" />
					<img src="" border="0" align="absmiddle" id="vcode" class="hand" />
				</div>
			</div>
			<script type="text/javascript">
			$(document).ready(function(){
				$("#vcode").phpok_vcode();
				$("#vcode").click(function(){
					$(this).phpok_vcode();
				});
			});
			</script>
			<?php } ?>
			<div class="table clearfix">
				<div class="l">&nbsp;</div>
				<div class="r"><input type="submit" value="提交" class="large button blue" /></div>
			</div>
			</form>
			</div>
		</div>
		<?php $tmpid["num"] = 0;$rslist=is_array($rslist) ? $rslist : array();$tmpid["total"] = count($rslist);$tmpid["index"] = -1;foreach($rslist AS $key=>$value){ $tmpid["num"]++;$tmpid["index"]++; ?>
		<div class="pfw">
			<div class="title"><h3><?php echo $value['title'];?></h3><span class="ext"><?php echo time_format($value['dateline']);?></span></div>
			<div class="detail">
				<?php if($value['content']){ ?><div class="content"><?php echo nl2br($value['content']);?></div><?php } ?>
				<?php if($value['pictures']){ ?>
				<ul class="piclist">
					<?php $value_pictures_id["num"] = 0;$value['pictures']=is_array($value['pictures']) ? $value['pictures'] : array();$value_pictures_id["total"] = count($value['pictures']);$value_pictures_id["index"] = -1;foreach($value['pictures'] AS $k=>$v){ $value_pictures_id["num"]++;$value_pictures_id["index"]++; ?>
					<li style="margin-right:10px;"><a href="<?php echo $v['gd']['auto'];?>" target="_blank"><img src="<?php echo $v['gd']['thumb'];?>" width="90" /></a></li>
					<?php } ?>
				</ul>
				<?php } ?>
				<?php if($value['adm_reply']){ ?>
				<fieldset><legend>管理员回复</legend><?php echo $value['adm_reply'];?></fieldset>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
		<?php $this->output("block_pagelist","file"); ?>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".thumb").mouseover(function(){
		var pid = $(this).attr('data-id');
		var thumb = $(this).attr('data');
		$("#product_"+pid).attr("src",thumb);
		$(".thumb[data-id="+pid+"]").removeClass("hover");
		$(this).addClass('hover');
	});
});
</script>
<?php $this->output("foot","file"); ?>