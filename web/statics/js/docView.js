var curMenu = null, zTree_Menu = null;
var ajaxSetting = {
	edit: {
		drag: {
			autoExpandTrigger: true,
			prev: true,
			inner: true,
			next: true
		},
		enable: true,
		showRemoveBtn: false,
		showRenameBtn: false
	},
	view: {
		showLine: false,
		showIcon: false,
		selectedMulti: false,
		dblClickExpand: false,
		addDiyDom: addDiyDom
	},
	data: {
		key:{
			name: 'title',
			children: null,
		},
		simpleData: {
			enable: true,
			idKey: "id",
			pIdKey: 'parent_id',
			rootPId: 0,
		}
	},
	callback: {
		onClick: onClick,
	},
};
var loadingHandler;
function switchNode(event, treeId, treeNode)
{
	if ('pushState' in history)
	{
		getChapter(treeNode.url);
		history.pushState('', '', treeNode.url);
	}
	else
	{
		location = treeNode.url;
	}
	event.preventDefault()
}

function getChapter(url)
{
	if(void 0 === url)
	{
		return;
	}
	loadingHandler = layer.open({
		type:3,
		icon:2,
		shade:0.1,
	});
	$.ajax({
		method: 'GET',
		url: url,
		success: function(result){
			var resultElement = $(result);
			$('#article-content').html(resultElement.find('#article-content').html());
			onContentChange();
			// 有时切换文档不在最上面
			$('#content_body').scrollTop(0)
		},
		error: function(){
			location = url;
		},
		complete: function(){
			layer.close(loadingHandler);
		},
	});
}

function addDiyDom(treeId, treeNode) {
	var spaceWidth = 5;
	var switchObj = $("#" + treeNode.tId + "_switch"),
	icoObj = $("#" + treeNode.tId + "_ico");
	switchObj.remove();
	icoObj.before(switchObj);

	if (treeNode.level > 1) {
		var spaceStr = "<span style='display: inline-block;width:" + (spaceWidth * treeNode.level)+ "px'></span>";
		switchObj.before(spaceStr);
	}
}

function initTree(data)
{
	for(var i in data)
	{
		data[i].target = '_self';
	}
	var treeObj = $("#treeDirectory");
	$.fn.zTree.init(treeObj, ajaxSetting, data);
	zTree_Menu = $.fn.zTree.getZTreeObj("treeDirectory");
	zTree_Menu.selectNode(zTree_Menu.getNodeByParam('id', currentCatalog.id, null));
}

function onClick(event, treeId, treeNode)
{
	// 未修改内容，直接切换
	switchNode(event, treeId, treeNode);
}

function parseLeftHeight()
{
	$('#leftbar .layui-tab-content').css('max-height', ($('#leftbar').height() - $('#leftbar .layui-tab-title').height() - $('#leftbar .copyright').height()) + 'px');
}

function onContentChange()
{
	var content = $('#article-content');
	content.find('blockquote').addClass('layui-elem-quote');
	content.find('table').addClass('layui-table');
	content.find('pre code').each(function(index, item){
		var pre = $(item).parent();
		var content = $(item).text();
		var brush = parseCodeBrush($(item).attr('class'));
		var newPre = $('<pre></pre>').addClass('brush: ' + brush).addClass('toolbar: false').text(content);
		pre.replaceWith(newPre);
	});
	SyntaxHighlighter.highlight();
	initSearchDatas();
	$('#content_body a').each(function(){
		var url = $(this).attr('href');
		if(void 0 !== url && '#' !== url.substr(0, 1))
		{
			$(this).attr('target', '_blank');
		}
	})
	setTimeout(function(){
		resizeCode();
	}, 1)
}

function parseCodeBrush(brush)
{
	for(var i in SyntaxHighlighter.brushes)
	{
		if(SyntaxHighlighter.brushes[i].aliases.indexOf(brush) > -1)
		{
			return brush;
		}
	}
	return 'text';
}

function resizeCode()
{
	var guttelines=$('.gutter .line');
	var codelines=$('.code .line');
	for(var i=0;i<guttelines.length;i++){
		guttelines.eq(i).css('height',codelines.eq(i).css('height'))
	}
}

$(function(){
	parseLeftHeight();

	$(window).resize(function(){
		parseLeftHeight();
	});

	$('body').on('click', '.ztree.showIcon li a span.button.switch', function(e){
		return false;
	});

	$('#btn-about').click(function(){
		layer.alert('本文档由 <a href="https://github.com" target="_blank">mddoc</a> 生成！');
	});

	SyntaxHighlighter.all();
	onContentChange();
})

$(window).load(function(){
	$(window).resize(resizeCode);
});