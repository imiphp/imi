var originSearchDatas = <?php echo json_encode($searchDatas, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE); ?>;
var searchDatas = [];
function initSearchDatas()
{
    searchDatas = JSON.parse(JSON.stringify(originSearchDatas));
    for(var i = 0; i < searchDatas.length; ++i)
    {
        if(void 0 !== searchDatas[i].url)
        {
            searchDatas[i].url = rootPath + searchDatas[i].url;
        }
    }
    doSearch($('#search-keyword').val());
}

function filterRegex(r)
{
    return r.replace(/[|\\{}()[\]^$+*?.]/g, '\\$&');
}

String.prototype.indexOf2 = function(f){
    var rt = this.match(eval('/' + filterRegex(f) + '/i'));
    return (rt == null) ? -1 : rt.index;
}

function switchSearchNode(a)
{
    if ('pushState' in history)
    {
        getChapter(a.attr('href'));
        history.pushState('', '', a.attr('href'));
        $('#treeSearch a').removeClass('curSelectedNode');
        a.addClass('curSelectedNode');
    }
    else
    {
        location = treeNode.url;
    }
}

function searchArticle(keyword)
{
    var keywords = keyword.split(' ');
    var tSearchDatas = searchDatas;
    var result = [];
    keywords.forEach(function(kw){
        if('' === kw)
        {
            return;
        }
        result = [];
        tSearchDatas.forEach(function(item, index){
            var titleIndex = item.title.indexOf2(kw);
            var contentIndex = item.content.indexOf2(kw);
            if(titleIndex > -1 || contentIndex > -1)
            {
                var newItem = JSON.parse(JSON.stringify(item));
                if(titleIndex > -1)
                {
                    newItem.searchedTitle = lightKeyword(newItem.title, kw);
                }
                else
                {
                    newItem.searchedTitle = newItem.title;
                }
                if(contentIndex > -1)
                {
                    var start = contentIndex < 11 ? 0 : contentIndex - 10;
                    var end = start === 0 ? 70 : contentIndex + kw.length + 60;

                    newItem.searchedContent = lightKeyword(newItem.content.substring(start, end), kw);
                }
                else
                {
                    newItem.searchedContent = newItem.content.substring(0, 60);
                }
                newItem.searchedContent = '...' + newItem.searchedContent + '...';
                result.push(newItem);
            }
        });
        tSearchDatas = result;
    });
    return result;
}

function lightKeyword(content, keyword)
{
    return content.replace(new RegExp('(' + filterRegex(keyword) + ')', 'ig'), '<strong>$1</strong>')
}

var searchTimer = null;
function parseSearch()
{
    $('#search-keyword').on('input', function(){
        if(null != searchTimer)
        {
            clearTimeout(searchTimer);
            searchTimer = null;
        }
        var inputKeyword = $(this);
        searchTimer = setTimeout(function(){
            doSearch(inputKeyword.val());
            searchTimer = null;
        }, 200);
        return false;
    });
}

function doSearch(keyword)
{
    var result = searchArticle(keyword);
    if(result.length > 0)
    {
        $('.searchResultNone').hide();
    }
    else
    {
        $('.searchResultNone').show();
    }
    var index = -1;
    $('#treeSearch a').each(function(i, item){
        ++index;
        if($(item).hasClass('curSelectedNode'))
        {
            return false;
        }
    });
    layui.laytpl($('#searchListTemplate').html()).render(result, function(html){
        $('#treeSearch').html(html);
        if(index >= 0)
        {
            var node = $('#treeSearch a').get(index);
            if(null !== node)
            {
                $(node).addClass('curSelectedNode');
            }
        }
    });
}

$(function(){

    $('body').on('click', '#treeSearch a', function(e){
        switchSearchNode($(this));
        return false;
    });

    parseSearch();

})