<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php
    $pageTitle = $currentCatalog['title'] . ' - imi 框架 v2.0 开发手册文档';
    ?>
    <title><?php echo $pageTitle; ?></title>
    <!-- jQuery-->
    <script src="<?php echo $this->path('statics/plugin/jquery/jquery-1.12.0.min.js'); ?>"></script>
    <!-- layui -->
    <link rel="stylesheet" href="<?php echo $this->path('statics/plugin/layui/css/layui.css'); ?>" />
    <script src="<?php echo $this->path('statics/plugin/layui/layui.all.js'); ?>"></script>
    <!-- zTree -->
    <link rel="stylesheet" href="<?php echo $this->path('statics/plugin/zTree/css/zTreeStyle/zTreeStyle.css'); ?>" />
    <script src="<?php echo $this->path('statics/plugin/zTree/js/jquery.ztree.core.js'); ?>"></script>
    <!-- SyntaxHighlighter -->
    <script src="<?php echo $this->path('statics/plugin/SyntaxHighlighter/shCore.js'); ?>" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->path('statics/plugin/SyntaxHighlighter/shCoreDefault.css'); ?>"/>
    <!-- 自定义 -->
    <link rel="stylesheet" href="<?php echo $this->path('statics/css/style.css'); ?>" />
    <script src="<?php echo $this->path('statics/js/home.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo $this->path('statics/css/docView.css'); ?>" />
    <script src="<?php echo $this->path('statics/js/docView.js'); ?>"></script>
</head>
<body>
    <!-- top-begin -->
    <div id="navbar">
        <ul class="layui-nav" lay-filter="">
            <div class="navRight">
                <li class="layui-nav-item layui-this" lay-unselect>
                    <a href="/v2.0/" target="_blank" style="padding-right: 40px;">v2.0 <span class="layui-badge">新</span></a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="/v1/" target="_blank">v1.x</a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a id="btn-about" href="javascript:;">关于</a>
                </li>
            </div>
        </ul>
        <div class="nav-menu">
            <a href="<?php echo $this->path(''); ?>" class="logo"><?php echo $this->config['name']; ?></a>
            <a href="javascript:;" id="navMenuLeft"><i class="layui-icon">&#xe658;</i></a>
            <a href="javascript:;" id="navMenuRight"><i class="layui-icon">&#xe61a;</i></a>
        </div>
        <div class="clear"></div>
    </div>
    <script>
    function openMenuLeft()
    {
        $('#leftbar').addClass('show-item');
        $('#navMenuLeft').addClass('active');
    }
    function closeMenuLeft()
    {
        $('#leftbar').removeClass('show-item');
        $('#navMenuLeft').removeClass('active');
    }
    $('#navMenuLeft').click(function(){
        var isShow = $('#leftbar').hasClass('show-item');
        if(isShow)
        {
            closeMenuLeft();
        }
        else
        {
            closeMenuRight();
            openMenuLeft();
        }
    });
    function openMenuRight()
    {
        $('#navbar > .layui-nav').addClass('show-item')
        $('#navMenuRight').addClass('active').find('i').html('&#xe619;');
    }
    function closeMenuRight()
    {
        $('#navbar > .layui-nav').removeClass('show-item')
        $('#navMenuRight').removeClass('active').find('i').html('&#xe61a;');
    }
    $('#navMenuRight').click(function(){
        var isShow = $('#navbar > .layui-nav').hasClass('show-item');
        if(isShow)
        {
            closeMenuRight();
        }
        else
        {
            closeMenuLeft();
            openMenuRight();
        }
    });
    </script>
    <!-- top-end -->

    <!-- left-begin -->
    <div id="leftbar" class="layui-nav-side">
        <div class="layui-tab layui-tab-brief" style="margin-top:0">
            <ul class="layui-tab-title">
                <li class="layui-this"><i class="layui-icon">&#xe705;</i> 目录</li>
                <li><i class="layui-icon">&#xe615;</i> 搜索</li>
            </ul>
            <div class="layui-tab-content" style="padding:0;overflow-y: auto;position: relative;padding-top: 12px;box-sizing: border-box;">
                <div class="layui-tab-item layui-show">
                    <ul id="treeDirectory" class="ztree showIcon"></ul>
                </div>
                <div class="layui-tab-item">
                    <div class="searchBox">
                        <div id="searchForm">
                            <div class="inputBox">
                                <input type="text" id="search-keyword" name="keyword" placeholder="搜索关键词" class="layui-input"/>
                                <i class="layui-icon input-icon">&#xe615;</i>
                            </div>
                        </div>
                        <ul id="treeSearch" class="ztree">
                        </ul>
                        <div class="searchResultNone">
                            <i class="layui-icon">&#xe615;</i>
                            <p>未搜索到结果</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright noScroll">mddoc</div>
        </div>
    </div>
    <script id="searchListTemplate" type="text/html">
        {{#  layui.each(d, function(index, item){ }}
        <li>
            <a href="{{ item.url }}"><span>{{ item.title }}</span></a>
        </li>
        {{#  }) }}
    </script>
    <!-- left-end -->

    <div id="body">
        <div id="content_body" name="content_body" style="width:100%;height:100%;border:none;overflow: auto;">
            <div id="article-content" class="markdown-body">
                <script>
                    function parseCatalogItem(item)
                    {
                        item.url = new Array(<?php echo substr_count($currentCatalog['url'], '/'); ?> + 1).join('../') + item.url;
                        return item;
                    }
                    document.title = '<?php echo $pageTitle; ?>';
                    var currentCatalog = parseCatalogItem(<?php echo json_encode($currentCatalog); ?>);
                    var catalogList = <?php echo json_encode($data['catalogList']); ?>;
                    for(var i = 0; i < catalogList.length; ++i)
                    {
                        if(void 0 !== catalogList[i].url)
                        {
                            catalogList[i] = parseCatalogItem(catalogList[i]);
                        }
                    }
                    initTree(catalogList);
                </script>
                <?php echo $articleContent; ?>
            </div>
        </div>
    </div>

    <script>
        $(function(){
            var leftBarTimeout = null;
            $('#leftbar').hover(function(e){
                if(null !== leftBarTimeout)
                {
                    clearTimeout(leftBarTimeout);
                    leftBarTimeout = null;
                }
                if(e.type === 'mouseenter')
                {
                    $('.left-show-hide').fadeIn(250);
                }
                else if($('#leftbar').css('left') == '0px')
                {
                    $('.left-show-hide').fadeOut(500);
                }
            });
        });
        function showLeftbar()
        {
            $('#leftbar').css('left', 0);
            $('#body').css('padding-left','');
            $('.left-show-hide > i.layui-icon').html('&#xe603;');
        }
        function hideLeftbar()
        {
            $('#leftbar').css('left', '-240px');
            $('#body').css('padding-left',0);
            $('.left-show-hide > i.layui-icon').html('&#xe602;');
            $('.left-show-hide').fadeIn(250);
        }
        $('.left-show-hide').click(function(){
            if($('#leftbar').css('left') == '0px')
            {
                hideLeftbar();
            }
            else
            {
                showLeftbar();
            }
        })
    </script>
    <script src="<?php echo $this->path('statics/js/mddoc-search.js'); ?>"></script>
    <script src="https://s13.cnzz.com/z_stat.php?id=1273991018&web_id=1273991018" language="JavaScript"></script>
</body>
</html>