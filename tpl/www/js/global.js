/**************************************************************************************************
	文件： js/global.js
	说明： 公共JS
	版本： 4.0
	网站： www.phpok.com
	作者： qinggan <qinggan@188.com>
	日期： 2015年09月05日 17时14分
***************************************************************************************************/
function top_search()
{
	var keywords = $("#top_keywords").val();
	if(!keywords){
		$.dialog.alert('请输入要搜索的关键字');
		return false;
	}
	return true;
}

function add_fav(sTitle,sURL) 
{
	try {
		window.external.addFavorite(sURL, sTitle);
	} catch (e) {
		try {
			window.sidebar.addPanel(sTitle, sURL, "");
		} catch (e) {
			$.dialog.alert("加入收藏失败，请使用Ctrl+D进行添加");
		}
	}
}