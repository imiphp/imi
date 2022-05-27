var curMenu = null, menuTree = null;
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
		history.pushState('', '', treeNode.url);
		getChapter(treeNode.url);
	}
	else
	{
		location = treeNode.url;
	}
	menuTree.expandNode(treeNode, true)
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
			try {
				var resultElement = $(result);
				$('#article-content').html(resultElement.find('#article-content').html());
				onContentChange();
				// 有时切换文档不在最上面
				$('#content_body').scrollTop(0)
			}
			catch(err) {
				layer.alert('文档加载失败', {icon:2});
			}
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
	menuTree = $.fn.zTree.getZTreeObj("treeDirectory");
	menuTree.selectNode(menuTree.getNodeByParam('id', currentCatalog.id, null));
}

function onClick(event, treeId, treeNode)
{
	// 未修改内容，直接切换
	switchNode(event, treeId, treeNode);
	setTimeout(function(){
		closeMenuLeft();
	}, 600);
}

function parseLeftHeight()
{
	$('#leftbar .layui-tab-content').css('max-height', ($('#leftbar').height() - $('#leftbar .layui-tab-title').height() - $('#leftbar .copyright').height()) + 'px');
}

function onContentChange()
{
	$('#content-toc').css('max-height', ($(document).height() / 2) + 'px');
	hCatalog('#article-content', '#content-toc');
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

// 基于这里的代码做了修改: https://blog.csdn.net/weixin_57215431/article/details/115676752
function hCatalog(current, target) {
	var box = document.querySelector(target)
	if (!box)
	{
		return;
	}
	box.innerHTML = ''
	var titleTree = hTree(document.querySelector(current))
	hCreatEle(titleTree, box)
}

function hCreatEle(arr, parent) {
	if (!arr.length) return
	var ol = document.createElement('ol')
	arr.forEach(function(item) {
		if ('' !== item.node.innerHTML)
		{
			var li = document.createElement('li')
			li.innerHTML = item.node.innerHTML
			hCreatEle(item.children, li)
			ol.appendChild(li)
		}
	})
	parent.appendChild(ol)
}

function hTree(wrapNode) {
	var root = { children: [] }
	var current = root
	
	for (var i = 0; i < wrapNode.children.length; ++i)
	{
		var item = wrapNode.children[i]
		if (item.localName.indexOf('h') === 0)
		{
			var obj = { node: item, children: [], parent: undefined }
			while (current !== root && current.node.localName[1] - obj.node.localName[1] !== -1) {
				current = current.parent
			}
			obj.parent = current
			obj.parent.children.push(obj)
			current = obj
		}
	}
	return root.children
}

function initContentToc()
{
	var isDown = false;
	var beginLeft = 0
	var beginTop = 0;
	var mouseBeginLeft = 0
	var mouseBeginTop = 0;

	$(document).on('mousedown', '.content-toc-title', function(e){
		var wrap = $('#content-toc-wrap');
		isDown = true;
		beginLeft = wrap[0].offsetLeft;
		beginTop = wrap[0].offsetTop;
		mouseBeginLeft = e.pageX;
		mouseBeginTop = e.pageY;
		e.preventDefault();
	});

	$(document).on('mouseup', function(){
		isDown = false;
	});

	$(document).on('mousemove', function(e){
		if(isDown)
		{
			var wrap = $('#content-toc-wrap');
			wrap.css('left', (beginLeft + (e.pageX - mouseBeginLeft)) + 'px');
			wrap.css('top', (beginTop + (e.pageY - mouseBeginTop)) + 'px');
		}
	});
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
		layer.alert('本文档由 <a href="https://github.com/Yurunsoft/mddoc" target="_blank">mddoc</a> 生成！');
	});

	initContentToc();

	SyntaxHighlighter.all();
	onContentChange();
})

$(window).load(function(){
	$(window).resize(resizeCode);
});