<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><div class="pfw">
	<div class="title"><h3><?php echo $page_rs['title'];?></h3></div>
	<ul class="artlist">
		<?php $list=phpok('_arclist',array('pid'=>$page_rs['id'],'psize'=>"9999",'fields'=>"id"));?>
		<?php $tmpid["num"] = 0;$list['rslist']=is_array($list['rslist']) ? $list['rslist'] : array();$tmpid["total"] = count($list['rslist']);$tmpid["index"] = -1;foreach($list['rslist'] AS $key=>$value){ $tmpid["num"]++;$tmpid["index"]++; ?>
		<li><a href="<?php echo $value['url'];?>" title="<?php echo $value['title'];?>"><?php echo phpok_cut($value['title'],'15','…');?></a></li>
		<?php } ?>
	</ul>
</div>