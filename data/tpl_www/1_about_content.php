<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><?php $title = $rs['title'].' - '.$page_rs['title'];?>
<?php $title=$title;?><?php $this->assign("title",$title); ?><?php $menutitle=$page_rs['title'];?><?php $this->assign("menutitle",$page_rs['title']); ?><?php $this->output("head","file"); ?>
<div class="banner"<?php if($page_rs['banner']){ ?> style="background-image:url('<?php echo $page_rs['banner']['gd']['auto'];?>')"<?php } ?>></div>
<div class="main clearfix">
	<div class="left">
		<?php $this->output("block_rslist","file"); ?>
		<?php $this->output("block_contact","file"); ?>
	</div>
	<div class="right">
		<div class="pfw">
			<div class="title">
				<h3><?php echo $rs['title'];?></h3>
				<span class="breadcrumbs">
					您现在的位置：
					<a href="<?php echo $sys['url'];?>" title="<?php echo $config['title'];?>">首页</a>
					<span class="arrow">&gt;</span> <a href="<?php echo $rs['url'];?>" title="<?php echo $rs['title'];?>"><?php echo $rs['title'];?></a>
				</span>
			</div>
			<div class="detail">
				<div class="content"><?php echo $rs['content'];?></div>
			</div>
		</div>
	</div>
</div>

<?php $this->output("foot","file"); ?>