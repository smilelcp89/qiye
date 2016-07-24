/**************************************************************************************************
	文件： {phpok}/js/rewrite.js
	说明： 伪静态页网址
	版本： 4.0
	网站： www.phpok.com
	作者： qinggan <qinggan@188.com>
	日期： 2015年02月03日 18时06分
***************************************************************************************************/
function add_it()
{
	var url = get_url('rewrite','set');
	$.phpok.go(url);
}
function edit_it(id)
{
	var url = get_url('rewrite','set','id='+id);
	$.phpok.go(url);
}
function delete_it(id,title)
{
	$.dialog.confirm("确定要删除规则：<span class='red'>"+title+"</span> 吗？",function(){
		var url = get_url('rewrite','delete','id='+id);
		var rs = $.phpok.json(url);
		if(rs.status == 'ok'){
			$.dialog.alert("删除成功",function(){
				$.phpok.reload();
			},'succeed');
		}else{
			$.dialog.alert(rs.content);
			return false;
		}
	});
}
function insert_input(val,id,space)
{
	if(!id || id == 'undefined'){
		id = 'rule';
	}
	if(!space || space == 'undefined'){
		space = '';
	}
	var info = $("#"+id).val();
	if(info){
		val = info + space +val;
	}
	$("#"+id).val(val);
}

function update2(val,id)
{
	if(!val){
		return false;
	}
	var info = $("#"+id).val();
	if(info){
		var lst = info.split('|');
		var is_add = true;
		for(var i in lst){
			if(lst[i] == val){
				is_add = false;
			}
		}
		if(!is_add){
			$.dialog.alert('数据已经使用，不能重复');
			return false;
		}
		val = info + "|"+val;
	}
	$("#"+id).val(val);
	if(id == 'ctrl'){
		update_func(val);
	}
}

function update_func(val)
{
	if(!val || val == 'undefined'){
		val = $("#ctrl").val();
		if(!val){
			return false;
		}
	}
	var url = get_url('rewrite','getfunc','id='+$.str.encode(val));
	$.phpok.json(url,function(rs){
		if(rs.status == 'ok'){
			var lst = rs.content;
			html = '<option value="">请选择…</option>';
			for(var i in lst){
				html += '<option value="'+i+'">'+lst[i]+'</option>';
			}
			$("#func_select").html(html);
		}
	})
}
