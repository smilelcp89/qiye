<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><?php echo $GLOBALS["app"]->plugin_html_ap("body");?>
<?php echo $GLOBALS["app"]->plugin_html_ap("foot");?>
<script type="text/javascript" src="js/zeroclipboard/zeroclipboard.min.js"></script>
<script type="text/javascript">
var phpok_clip;
var phpok_clip_status = false;
$(document).ready(function(){
	var r_menu_in_copy = [{
		'text':'复制',
		'func':function(){
			//
		}
	},{
		'text':'刷新内容',
		'func':function(){
			$.phpok.reload();
		}
	}];
	var r_menu_not_copy = [{
		'text':'刷新内容',
		'func':function(){
			$.phpok.reload();
		}
	}];
	var r_menu = [[{
		'text':'刷新内容',
		'func':function(){
			$.phpok.reload();
		}
	}],[{
		'text': "新窗口打开",
    	'func': function() {
	    	var url = window.location.href;
			//去除随机数
			url = url.replace(/\_noCache=[0-9\.]+/g,'');
			if(url.substr(-1) == '&' || url.substr(-1) == '?'){
				url = url.substr(0,url.length-1);
			}
			window.open(url);
	    }    
	},{
		'text':'网页属性',
		'func':function(){
			var url = window.location.href;
			top.$.dialog({
				'title':'网址属性',
				'content':'网址：'+url+'<br /><div style="text-indent:36px"><a href="'+url+'" target="_blank" class="red">新窗口打开</a></div>',
				'lock':true,
				'drag':false,
				'fixed':true
			});
		}
	}]];
	$(window).smartMenu(r_menu,{
		'name':'smart',
		'beforeShow':function(){
			$.smartMenu.remove();
			var info= window.getSelection ?  window.getSelection() : (document.selection.createRange ? document.selection.createRange().text : '');
			if(info != ''){
				r_menu[0] = r_menu_in_copy;
				ZeroClipboard.config({
					debug: false,
					swfPath: 'js/zeroclipboard/zeroclipboard.swf'
				});
				phpok_clip = new ZeroClipboard();
				phpok_clip_status = true;
				phpok_clip.on('copy', function (e) {
			        $.smartMenu.remove();
			        $.smartMenu.hide();
			    });
			    phpok_clip.on('mouseover mouseout', function (e) {
			        if (e.type == 'mouseover') {
				        $("#smartMenu_smart .smart_menu_a:eq(0)").addClass("smart_menu_a_hover");
			        } else if (e.type == 'mouseout') {
				        $("#smartMenu_smart .smart_menu_a:eq(0)").removeClass("smart_menu_a_hover");
			        }
			    });
			    phpok_clip.on('wrongflash noflash', function (e) {
			        ZeroClipboard.destroy();
			        phpok_clip_status = false;
			        r_menu[0] = r_menu_not_copy;
			    });
			    phpok_clip.on('aftercopy',function(e){
				    ZeroClipboard.destroy();
				    phpok_clip_status = false;
				    r_menu[0] = r_menu_not_copy;
			    });
			}else{
				r_menu[0] = r_menu_not_copy;
			}
		},
		'afterShow':function(e){
			if(phpok_clip_status){
				var obj = $("#smartMenu_smart .smart_menu_a:eq(0)");
				phpok_clip.clip(obj);
				var info= window.getSelection ?  window.getSelection() : (document.selection.createRange ? document.selection.createRange().text : '');
				obj.attr("data-clipboard-text",info);
			}else{
				ZeroClipboard.destroy();
			}
		}
	});
});
</script>
<?php echo $app->plugin_html_ap("phpokbody");?></body>
</html>