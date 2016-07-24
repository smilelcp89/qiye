<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><?php $title = $page_rs['title'].' - '.$parent_rs['title'];?>
<?php $title=$title;?><?php $this->assign("title",$title); ?><?php $menutitle=$parent_rs['title'];?><?php $this->assign("menutitle",$parent_rs['title']); ?><?php $this->output("head","file"); ?>
<div class="banner"<?php if($parent_rs['banner']){ ?> style="background-image:url('<?php echo $parent_rs['banner']['gd']['auto'];?>')"<?php } ?>></div>
<div class="main clearfix">
	<div class="left">
		<div class="pfw">
			<div class="title"><h3><?php echo $parent_rs['title'];?></h3></div>
			<ul class="artlist">
				<?php $list=phpok('_sublist',array('pid'=>$parent_rs['id']));?>
				<?php $tmpid["num"] = 0;$list=is_array($list) ? $list : array();$tmpid["total"] = count($list);$tmpid["index"] = -1;foreach($list AS $key=>$value){ $tmpid["num"]++;$tmpid["index"]++; ?>
				<li<?php if($value['id'] == $page_rs['id']){ ?> class="current"<?php } ?>><a href="<?php echo $value['url'];?>" title="<?php echo $value['title'];?>"><?php echo $value['title'];?></a></li>
				<?php } ?>
				<li><a href="<?php echo $parent_rs['url'];?>" title="<?php echo $parent_rs['title'];?>"><?php echo $parent_rs['title'];?></a></li>
			</ul>
		</div>
		<?php $this->output("block_contact","file"); ?>
	</div>
	<div class="right">
		<div class="pfw">
			<div class="title">
				<h3><?php echo $page_rs['title'];?></h3>
				<span class="breadcrumbs">
					您现在的位置：
					<a href="<?php echo $sys['url'];?>" title="<?php echo $config['title'];?>">首页</a>
					<span class="arrow">&gt;</span> <a href="<?php echo $parent_rs['url'];?>" title="<?php echo $parent_rs['title'];?>"><?php echo $parent_rs['title'];?></a>
					<span class="arrow">&gt;</span> <a href="<?php echo $page_rs['url'];?>" title="<?php echo $page_rs['title'];?>"><?php echo $page_rs['title'];?></a>
				</span>
			</div>
			<div class="detail" style="background:#fff;">
				<div class="content"><?php echo $page_rs['content'];?></div>
			</div>
		</div>
	</div>
</div>

<?php $this->output("foot","file"); ?>