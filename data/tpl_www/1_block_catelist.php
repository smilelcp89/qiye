<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><div class="pfw">
	<div class="title"><h3><?php echo $page_rs['title'];?></h3></div>
	<ul class="artlist">
		<?php $list=phpok('_catelist',array('pid'=>$page_rs['id']));?>
		<?php $tmpid["num"] = 0;$list['sublist']=is_array($list['sublist']) ? $list['sublist'] : array();$tmpid["total"] = count($list['sublist']);$tmpid["index"] = -1;foreach($list['sublist'] AS $key=>$value){ $tmpid["num"]++;$tmpid["index"]++; ?>
		<li<?php if($cate_rs['id'] == $value['id']){ ?> class="current"<?php } ?>><a href="<?php echo $value['url'];?>" title="<?php echo $value['title'];?>"><?php echo phpok_cut($value['title'],'15','…');?></a></li>
		<?php } ?>
	</ul>
</div>