<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><?php $this->output("head","file"); ?>
<script type="text/javascript" src="<?php echo add_js('list.js');?>"></script>
<script type="text/javascript">
function pendding_info()
{
	var url = get_url('index','pendding');
	$.ajax({
		'url':url,
		'cache':false,
		'async':true,
		'dataType':'json',
		'success': function(rs){
			if(rs.status == "ok")
			{
				var list = rs.content;
				var html = '<em class="toptip">{total}</em>';
				var total = 0;
				for(var key in list)
				{
					$("li[id=project_"+list[key]['id']+"] em").remove();
					if(list[key]['id'] != 'user' && list[key]['id'] != 'reply')
					{
						$("li[id=project_"+list[key]['id']+"]").append(html.replace('{total}',list[key]['total']));
					}
				}
				//每隔一分钟检测一次
				window.setTimeout("pendding_info()", 60000);
			}
			else
			{
				//移除提示
				$("em.toptip").remove();
				//每隔三分钟进行一次检测
				window.setTimeout("pendding_info()", 180000);
			}
		}
	});
}

$(document).ready(function(){
	pendding_info();
	top.$.desktop.title('内容管理');
	$("#project li").mouseover(function(){
		$(this).addClass("hover");
	}).mouseout(function(){
		$(this).removeClass("hover");
	}).click(function(){
		var url = $(this).attr("href");
		if(url)
		{
			direct(url);
		}
		else
		{
			alert("未指定动作！");
			return false;
		}
	});
});
</script>
<div class="tips">
	点击图标可直接进入内容管理！
</div>
<ul class="project" id="project">
	<?php $rslist_id["num"] = 0;$rslist=is_array($rslist) ? $rslist : array();$rslist_id["total"] = count($rslist);$rslist_id["index"] = -1;foreach($rslist AS $key=>$value){ $rslist_id["num"]++;$rslist_id["index"]++; ?>
	<li id="project_<?php echo $value['id'];?>" title="<?php echo $value['title'];?>" status="<?php echo $value['status'];?>" href="<?php echo admin_url('list','action');?>&id=<?php echo $value['id'];?>">
		<div class="project" href="<?php echo admin_url('list','action');?>&id=<?php echo $value['id'];?>">
			<div class="img"><img src="<?php echo $value['ico'] ? $value['ico'] : 'images/ico/default.png';?>" /></div>
			<div class="txt" id="txt_<?php echo $value['id'];?>"><?php echo $value['nick_title'] ? $value['nick_title'] : $value['title'];?></div>
		</div>
	</li>
	<?php } ?>
</ul>
<div class="clear"></div>
<?php $this->output("foot","file"); ?>