var originSearchDatas = <?php echo json_encode($searchDatas, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE); ?>;
var searchDatas = [];
function initSearchDatas() {
    searchDatas = JSON.parse(JSON.stringify(originSearchDatas));
    for (var i = 0; i < searchDatas.length; ++i) {
        if (void 0 !== searchDatas[i].url) {
            searchDatas[i].url = rootPath + searchDatas[i].url;
        }
    }
    doSearch($('#search-keyword').val());
}

function filterRegex(r) {
    return r.replace(/[|\\{}()[\]^$+*?.]/g, '\\$&');
}

String.prototype.indexOf2 = function (f) {
    var rt = this.match(eval('/' + filterRegex(f) + '/i'));
    return (rt == null) ? -1 : rt.index;
}

function switchSearchNode(a) {
    if ('pushState' in history) {
        getChapter(a.attr('href'));
        history.pushState('', '', a.attr('href'));
        $('#treeSearch a').removeClass('curSelectedNode');
        a.addClass('curSelectedNode');
    }
    else {
        location = treeNode.url;
    }
}

function searchArticle(keyword) {
    if ('object' === (typeof Intl)) {
        var segmenterJa = new Intl.Segmenter("zh-CN", { granularity: "word" });
        var segments = segmenterJa.segment(keyword);
        var keywords = [];
        for (let segment of segments) {
            if (segment.segment.trim().length > 0) {
                keywords.push(segment.segment);
            }
        }
    }
    else {
        var keywords = keyword.split(' ');
    }
    // 把用户输入原文作为第一匹配关键词
    keywords.unshift(keyword);
    // 去重
    keywords = keywords.filter(function (item, index) {
        return keywords.indexOf(item) === index;
    });

    // var tSearchDatas = searchDatas;
    var result = [];

    var totalScore = 0;
    searchDatas.forEach(function (item, index) {
        var score = 0;
        var first = true;
        var matchedKeywords = [];
        keywords.forEach(function (kw) {
            if ('' === kw) {
                first = false;
                return;
            }
            // 统计标题中关键字出现的次数
            var titleCount = item.title.toUpperCase().split(kw.toUpperCase()).length - 1;
            score += titleCount * 10;
            // 统计内容中关键字出现的次数
            var contentCount = item.content.toUpperCase().split(kw.toUpperCase()).length - 1;
            score += contentCount;
            if (titleCount > 0 || contentCount > 0) {
                matchedKeywords.push(kw);
            }
            // 用户输入完全匹配，给最高分
            if (first && matchedKeywords.length > 0) {
                score = Number.MAX_SAFE_INTEGER;
            }
            first = false;
        });
        if (score > 0) {
            // 完全匹配积分 * 10
            if (matchedKeywords.length >= keywords.length - 1) {
                score *= 10;
            }
            totalScore += score;
            var newItem = JSON.parse(JSON.stringify(item));
            newItem.searchedTitle = newItem.title;
            newItem.searchedContent = newItem.content;
            var parsedContent = false;

            matchedKeywords.forEach(function (kw) {
                var titleIndex = item.title.indexOf2(kw);
                if (titleIndex > -1) {
                    newItem.searchedTitle = lightKeyword(newItem.searchedTitle, kw);
                }
                if (!parsedContent) {
                    var contentIndex = item.content.indexOf2(kw);
                    if (contentIndex > -1) {
                        var start = contentIndex < 11 ? 0 : contentIndex - 10;
                        var end = start === 0 ? 70 : contentIndex + kw.length + 60;

                        newItem.searchedContent = lightKeyword(newItem.searchedContent.substring(start, end), kw);
                        parsedContent = true;
                    }
                }
            });
            if (!parsedContent) {
                newItem.searchedContent = newItem.searchedContent.substring(0, 60);
            }
            newItem.searchedContent = '...' + newItem.searchedContent + '...';
            newItem.score = score;
            result.push(newItem);
        }
    });

    // 按积分排序
    result.sort(function (a, b) {
        return b.score - a.score;
    });

    // 平均数 / 0.618
    var filterScore = totalScore / result.length / 0.618;

    // 把小于过滤分数的过滤掉
    result = result.filter(function (item, index) {
        return item.score >= filterScore;
    });

    // 保留前 20 结果
    result = result.slice(0, 20);

    return result;
}

function lightKeyword(content, keyword) {
    return content.replace(new RegExp('(' + filterRegex(keyword) + ')', 'ig'), '<strong>$1</strong>')
}

var searchTimer = null;
function parseSearch() {
    $('#search-keyword').on('input', function () {
        if (null != searchTimer) {
            clearTimeout(searchTimer);
            searchTimer = null;
        }
        var inputKeyword = $(this);
        searchTimer = setTimeout(function () {
            doSearch(inputKeyword.val());
            searchTimer = null;
        }, 200);
        return false;
    });
}

function doSearch(keyword) {
    var result = searchArticle(keyword);
    if (result.length > 0) {
        $('.searchResultNone').hide();
    }
    else {
        $('.searchResultNone').show();
    }
    var index = -1;
    $('#treeSearch a').each(function (i, item) {
        ++index;
        if ($(item).hasClass('curSelectedNode')) {
            return false;
        }
    });
    layui.laytpl($('#searchListTemplate').html()).render(result, function (html) {
        $('#treeSearch').html(html);
        if (index >= 0) {
            var node = $('#treeSearch a').get(index);
            if (null !== node) {
                $(node).addClass('curSelectedNode');
            }
        }
    });
}

$(function () {

    $('body').on('click', '#treeSearch a', function (e) {
        switchSearchNode($(this));
        return false;
    });

    parseSearch();

})