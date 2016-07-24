<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><?php $this->output("head","file"); ?>
<script type="text/javascript" src="<?php echo add_js('call.js');?>"></script>
<div class="tips">
	<div class="action"><a href="<?php echo admin_url('call','set');?>">添加调用</a></div>
	您当前的位置：<a href="<?php echo admin_url('call');?>" title="调用列表">调用列表</a>
</div>
<div class="list">
<table class="list" width="100%" cellpadding="0" cellspacing="0">
<tr>
	<th class="w50">ID</th>
	<th>名称</th>
	<th class="w150 lft">参数变量</th>
	<th class="w60">操作</th>
</tr>
<?php $rslist_id["num"] = 0;$rslist=is_array($rslist) ? $rslist : array();$rslist_id["total"] = count($rslist);$rslist_id["index"] = -1;foreach($rslist AS $key=>$value){ $rslist_id["num"]++;$rslist_id["index"]++; ?>
<tr>
	<td class="center"><?php echo $value['id'];?></td>
	<td><?php echo $value['title'];?>
		<span class="gray i">（<?php echo $phpok_type_list[$value['type_id']];?>）</span>
	</td>
	<td><?php echo $value['identifier'];?></td>
	<td>
		<a class="icon edit" href="<?php echo admin_url('call','set');?>&id=<?php echo $value['id'];?>" title="修改"></a>
		<a class="icon delete end" onclick="call_del('<?php echo $value['id'];?>','<?php echo $value['title'];?>')" title="删除"></a>
	</td>
</tr>
<?php } ?>
</table>
</div>
<div class="table"><?php $this->output("pagelist","file"); ?></div>

<?php $this->output("foot","file"); ?>