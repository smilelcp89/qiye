<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><div class="link">
	<ul class='clearfix'>
		<?php $link = phpok('link');?>
		<li class="title"><?php echo $link['project']['title'];?>：</li>
		<?php $tmpid["num"] = 0;$link['rslist']=is_array($link['rslist']) ? $link['rslist'] : array();$tmpid["total"] = count($link['rslist']);$tmpid["index"] = -1;foreach($link['rslist'] AS $key=>$value){ $tmpid["num"]++;$tmpid["index"]++; ?>
		<li><a href="<?php echo $value['url'];?>" target="<?php echo $value['target'];?>" title="<?php echo $value['title'];?>"><?php echo $value['title'];?></a></li>
		<?php } ?>
	</ul>
</div>
<div class="foot">
	<div class="copyright"><?php echo $config['ext']['content'];?></div>
	<?php if($sys['debug']){ ?><div class="debug"><?php echo debug_time('1','1','1','1');?></div><?php } ?>
</div>
<?php $list = phpok('online-service');?>
<?php if($list['project'] && $list['rslist']){ ?>
<div id="right-float-box" class="im_floatonline">
	<div class="float-box-content">
		<div class="toptitle"><?php echo $list['project']['title'];?></div>
		<div class="addlist">
			<ul>
				<?php $list_rslist_id["num"] = 0;$list['rslist']=is_array($list['rslist']) ? $list['rslist'] : array();$list_rslist_id["total"] = count($list['rslist']);$list_rslist_id["index"] = -1;foreach($list['rslist'] AS $key=>$value){ $list_rslist_id["num"]++;$list_rslist_id["index"]++; ?>
				<li><?php echo $value['title'];?><br /><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $value['qq'];?>&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo $value['qq'];?>:51" alt="点击这里给我发消息" title="点击这里给我发消息"/></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<?php } ?>
<?php echo $app->plugin_html_ap("phpokbody");?></body>
</html>