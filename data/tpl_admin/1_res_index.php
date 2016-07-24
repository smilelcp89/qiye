<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><?php $this->output("head","file"); ?>
<script type="text/javascript" src="js/laydate/laydate.js"></script>
<script type="text/javascript">
function pl_update()
{
	var id = $.input.checkbox_join(".checkbox input[type=checkbox]");
	if(!id || id == "undefined"){
		$.dialog.alert("<?php echo P_Lang("未指定要操作的附件");?>");
		return true;
	}
	var url = get_url("res","update_pl") + "&id="+$.str.encode(id);
	top.$.win('<?php echo P_Lang("附件批量更新中，请不要关掉这个页面");?>',url,{'is_max':true,'win_max':false,'width':600,'height':400});
}
function pl_delete()
{
	var id = $.input.checkbox_join(".checkbox input[type=checkbox]");
	if(!id || id == "undefined"){
		$.dialog.alert("<?php echo P_Lang("未指定要操作的附件");?>");
		return false;
	}
	$.dialog.confirm("<?php echo P_Lang("确定要删除选中的附件吗？删除后是不可恢复的");?>",function(){
		var url = get_url("res","delete_pl") + "&id="+$.str.encode(id);
		$.phpok.json(url,function(rs){
			if(rs.status == 'ok'){
				$.dialog.alert('<?php echo P_Lang("批量删除附件操作成功");?>',function(){
					$.phpok.reload();
				},'succeed');
			}else{
				$.dialog.alert(rs.content);
			}
		})
	});
}

function file_delete(id)
{
	$.dialog.confirm('<?php echo P_Lang("确定要删除此附件吗？删除后不能恢复");?><br />附件ID：<span class="red">'+id+'</span>',function(){
		url = "<?php echo phpok_url(array('ctrl'=>'upload','func'=>'delete'));?>&id="+id;
		$.phpok.json(url,function(rs){
			if(rs.status == 'ok'){
				$.phpok.reload();
			}else{
				$.dialog.alert(rs.content);
				return false;
			}
		})
	});
}
function preview_attr(id)
{
	$.dialog.open(get_url('upload','preview','id='+id),{
		'title':'<?php echo P_Lang("预览附件信息");?>',
		'width':'700px',
		'height':'400px',
		'lock':true,
		'okVal':'关闭',
		'ok':function(){}
	});
}
function modify(id)
{
	$.dialog.open(get_url('res','set','id='+id),{
		'title':'<?php echo P_Lang("编辑附件信息");?>',
		'width':'700px',
		'height':'400px',
		'lock':true,
		'okVal':'提交',
		'ok':function(){
			var iframe = this.iframe.contentWindow;
			if (!iframe.document.body) {
				alert('<?php echo P_Lang("iframe还没加载完毕呢");?>');
				return false;
			};
			iframe.save();
			return false;
		},
		'cancelVal':'取消修改',
		'cancel':function(){}
	});
}
function add_file()
{
	$.dialog.open(get_url('res','add'),{
		'title':'添加附件信息',
		'width':'700px',
		'height':'400px',
		'lock':true,
		'ok':function(){
			var iframe = this.iframe.contentWindow;
			if (!iframe.document.body) {
				alert('<?php echo P_Lang("iframe还没加载完毕呢");?>');
				return false;
			};
			iframe.save();
			return false;
		},
		'okVal':'<?php echo P_Lang("执行附件上传");?>',
		'cancelVal':'<?php echo P_Lang("取消上传并关闭");?>',
		'cancel':function(){
			var iframe = this.iframe.contentWindow;
			if (!iframe.document.body) {
				alert('<?php echo P_Lang("iframe还没加载完毕呢");?>');
				return false;
			};
			iframe.cancel();
			return true;
		}
	});
}
function update_pl_pictures()
{
	$.dialog.confirm('<?php echo P_Lang("确定要全部更新图片吗？执行此操作占用时间很长，程序会新开桌面，请不要关闭这个页面");?>',function(){
		var url = get_url("res","update_pl") + "&id=all";
		top.$.win('<?php echo P_Lang("附件批量更新中");?>',url,{'is_max':true,'win_max':false,'width':600,'height':400});
	});
}
function action_search()
{
	if($("#adv_search").is(":hidden")){
		$("#adv_search").show();
	}else{
		$("#adv_search").hide();
	}
}
function move_cate()
{
	var id = $.input.checkbox_join(".checkbox input[type=checkbox]");
	if(!id || id == "undefined"){
		$.dialog.alert("<?php echo P_Lang("未指定要操作的附件");?>");
		return false;
	}
	$.dialog({
		'title':'<?php echo P_Lang("移动分类，请选择目标移动分类");?>',
		'content':document.getElementById('move_cate_html'),
		'lock':true,
		'width':'500px',
		'height':'100px',
		'cancel':function(){},
		'cancelVal':'取消移动',
		'okVal':'执行',
		'ok':function(){
			var newcate = $("input[name=newcate]:checked").val();
			var url = "<?php echo phpok_url(array('ctrl'=>'res','func'=>'movecate'));?>&id="+$.str.encode(id)+"&newcate="+newcate;
			$.phpok.json(url,function(){
				$.input.checkbox_none('.checkbox input[type=checkbox]');
				return $.dialog.alert('分类移动成功');
			})
		}
	});
}
</script>
<div class="tips">
	<div class="action"><a href="javascript:update_pl_pictures();void(0);"><?php echo P_Lang("图片全部更新");?></a></div>
	<div class="action"><a href="javascript:add_file();void(0)"><?php echo P_Lang("添加资源");?></a></div>
	<div class="action"><a href="javascript:action_search();void(0);"><?php echo P_Lang("搜索");?></a></div>
	<?php echo P_Lang("您当前的位置：");?><a href="<?php echo phpok_url(array('ctrl'=>'res'));?>"><?php echo P_Lang("资源管理");?></a> &raquo; <?php echo P_Lang("列表");?>
	<div class="clear"></div>
</div>
<div id="move_cate_html" class="hide">
<table>
<?php $tmpid["num"] = 0;$catelist=is_array($catelist) ? $catelist : array();$tmpid["total"] = count($catelist);$tmpid["index"] = -1;foreach($catelist AS $key=>$value){ $tmpid["num"]++;$tmpid["index"]++; ?>
<tr>
	<td><input type="radio" name="newcate" id="newcate_<?php echo $value['id'];?>" value="<?php echo $value['id'];?>"<?php if($tmpid['num'] == 1){ ?> checked<?php } ?> /></td>
	<td><label for="newcate_<?php echo $value['id'];?>"><?php echo $value['title'];?><?php if($value['typeinfos']){ ?> <span class="gray i">支持类型：<?php echo $value['typeinfos'];?></span><?php } ?></label></td>
</tr>
<?php } ?>
</table>
</div>
<div class="tips" id="adv_search" style="display:none;">
	<form method="post" action="<?php echo admin_url('res');?>">
	<table width="100%">
	<tr>
		<td width="50%">
			<div class="table">
				<div class="title">
					<?php echo P_Lang("关键字：");?>
					<span class="note"><?php echo P_Lang("填写附件名称关键字");?></span>
				</div>
				<div class="content"><input type="text" id="keywords" name="keywords" class="long" value="<?php echo $keywords;?>" /></div>
			</div>
		</td>
		<td>
			<div class="table">
				<div class="title">
					<?php echo P_Lang("附件分类：");?>
				</div>
				<div class="content">
					<select id="cate_id" name="cate_id">
						<option value="0"><?php echo P_Lang("不限");?></option>
						<?php $catelist_id["num"] = 0;$catelist=is_array($catelist) ? $catelist : array();$catelist_id["total"] = count($catelist);$catelist_id["index"] = -1;foreach($catelist AS $key=>$value){ $catelist_id["num"]++;$catelist_id["index"]++; ?>
						<option value="<?php echo $value['id'];?>"<?php if($cate_id == $value['id']){ ?> selected<?php } ?>><?php echo $value['title'];?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="table">
				<div class="title">
					<?php echo P_Lang("时间范围：");?>
					<span class="note"><?php echo P_Lang("左侧是开始时间，右边是结束时间");?></span>
				</div>
				<div class="content">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td><input type="text" id="start_date" name="start_date" value="<?php echo $start_date;?>" placeholder="<?php echo P_Lang("开始时间");?>" onfocus="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" /></td>
						<td>&nbsp; - &nbsp;</td>
						<td><input type="text" id="stop_date" name="stop_date" value="<?php echo $stop_date;?>" placeholder="<?php echo P_Lang("结束时间");?>" onfocus="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})" /></td>
					</tr>
					</table>
				</div>
			</div>
			
		</td>
		<td>
			<div class="table">
				<div class="content">
					<input type="submit" value="开始搜索" class="submit" />
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="table">
				<div class="title">
					<?php echo P_Lang("附件类型：");?>
					<span class="note"><?php echo P_Lang("勾选要搜索的附件类型，如果有未列出来的附件类型，请在编辑框中输入，多种附件类型用逗号隔开");?></span>
				</div>
				<div class="content">
					<table>
					<tr>
						<td><label><input type="checkbox" id="type_jpg" name="ext[]" value="jpg"<?php if($ext && in_array('jpg',$ext)){ ?> checked<?php } ?> /> JPG</label></td>
						<td><label><input type="checkbox" id="type_gif" name="ext[]" value="gif"<?php if($ext && in_array('gif',$ext)){ ?> checked<?php } ?> /> GIF</label></td>
						<td><label><input type="checkbox" id="type_png" name="ext[]" value="png"<?php if($ext && in_array('png',$ext)){ ?> checked<?php } ?> /> PNG</label></td>
						<td><label><input type="checkbox" id="type_rar" name="ext[]" value="rar"<?php if($ext && in_array('rar',$ext)){ ?> checked<?php } ?> /> RAR</label></td>
						<td><label><input type="checkbox" id="type_zip" name="ext[]" value="zip"<?php if($ext && in_array('zip',$ext)){ ?> checked<?php } ?> /> ZIP</label></td>
						<td><label><input type="checkbox" id="type_flv" name="ext[]" value="flv"<?php if($ext && in_array('flv',$ext)){ ?> checked<?php } ?> /> FLV</label></td>
						<td><label><input type="checkbox" id="type_swf" name="ext[]" value="swf"<?php if($ext && in_array('swf',$ext)){ ?> checked<?php } ?> /> SWF</label></td>
						<td><label><input type="checkbox" id="type_mp3" name="ext[]" value="mp3"<?php if($ext && in_array('mp3',$ext)){ ?> checked<?php } ?> /> MP3</label></td>
					</tr>
					<tr>
						<td>&nbsp;  &nbsp; <?php echo P_Lang("其他：");?></td>
						<td colspan="7"><input type="text" id="myext" name="myext" value="<?php echo $myext;?>" class="long" /></td>
					</tr>
					</table>
				</div>
			</div>
		</td>
	</tr>
	</table>
	</form>
</div>
<style type="text/css">
.thumb{float:left;width:144px;margin:3px 5px;padding:1px;border:1px solid #ccc;border-radius:3px;position:relative;z-index:1;}
.thumb div.checkbox{position:absolute;left:2px;top:2px;z-index:2;}
</style>

<ul class="attrlist clearfix" id="attr2list">
	<?php $tmpid["num"] = 0;$rslist=is_array($rslist) ? $rslist : array();$tmpid["total"] = count($rslist);$tmpid["index"] = -1;foreach($rslist AS $key=>$value){ $tmpid["num"]++;$tmpid["index"]++; ?>
	<div class="thumb">
		<div class="checkbox"><input type="checkbox" name="attrid[]" id="attrid_<?php echo $value['id'];?>" value="<?php echo $value['id'];?>" /></div>
		<label for="attrid_<?php echo $value['id'];?>"><div style="text-align:center;"><img src="<?php echo $value['ico'];?>" width="100" height="100" /></div></label>
		<div class="file-action" style="text-align:center;"><div class="button-group">
			<input type="button" value="<?php echo P_Lang("修改");?>" class="phpok-btn" onclick="modify('<?php echo $value['id'];?>')" />
			<input type="button" value="<?php echo P_Lang("预览");?>" class="phpok-btn" onclick="preview_attr('<?php echo $value['id'];?>')" />
			<input type="button" value="<?php echo P_Lang("删除");?>" class="phpok-btn" onclick="file_delete('<?php echo $value['id'];?>')" /></div>
		</div></div>
	</div>
	<?php } ?>
	<div class="clear"></div>
</ul>

<div class="table">
<table width="100%">
<tr>
	<td>
		<div class="button-group">
			<input type="button" value="<?php echo P_Lang("全选");?>" onclick="$.input.checkbox_all('.checkbox input[type=checkbox]')" class="phpok-btn" />
			<input type="button" value="<?php echo P_Lang("全不选");?>" onclick="$.input.checkbox_none('.checkbox input[type=checkbox]')" class="phpok-btn" />
			<input type="button" value="<?php echo P_Lang("反选");?>" onclick="$.input.checkbox_anti('.checkbox input[type=checkbox]')" class="phpok-btn" />
		</div>
		<div class="button-group">
			<input type="button" value="<?php echo P_Lang("移动分类");?>" onclick="move_cate()" class="phpok-btn" />
			<input type="button" value="<?php echo P_Lang("更新图片");?>" onclick="pl_update()" class="phpok-btn" />
			<input type="button" value="<?php echo P_Lang("删除");?>" onclick="pl_delete()" class="phpok-btn" />
		</div>
	</td>
	<td align="right"><?php $this->output("pagelist","file"); ?></td>
</tr>
</table>

</div>

<?php $this->output("foot","file"); ?>