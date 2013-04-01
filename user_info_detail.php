<!DOCTYPE html>
<html lang="zh-CN" class="ua-windows ua-webkit">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>
                用户详细信息
        </title>
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css" />
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
        <script type="text/javascript" src="http://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.js"></script>
        <script type="text/javascript" src="/js/jquery.touch-gallery-1.0.0.min.js"></script>

<style>
        #main {
                background: #eee;
        }
        span {
                color #fff; 
        }
        #base_info {
                font-size: 14px;    
        }
        .ui-li-heading {
                color: #b42a7c;
        }
        .ui-li-desc {
                color: #7b3f8a;    
        }
        .footer {
                font-size: 12px;
                font-weight: 100;
                color: #7b3f8a;
        }
        #gallery {
                min-height: 100px;
                width: 100%;    
                padding-bottom: 2px;
        }
        .gallery_item_img {
                width: 100px;
                height: 100px;
                border: 1px solid #999;
                margin: 0 5px;
                float: left;
        }
        .button {
                background : -webkit-linear-gradient( rgb(101, 57, 126),rgb(238, 57, 187) );
        }
        .ui-btn-up-b {
                border: 1px solid rgb(71, 12, 73);
        }
    </style>

</head>
        <body>
        <!-- main -->
        <div data-role="page" id="main">
                <div data-theme="a" data-role="header">
                        <h3>
                                <span>查询结果</span>
                        </h3>
                </div>

                <div data-role="content">
                        <ul data-role="listview" class="ui-listview">
                                <li class="ui-li ui-li-static ui-btn-up-c" style="color: #742157; font-family: '黑体';font-weight: 100;">
                                <!--一般信息-->
                                <span id="nickName">陌上花开</span>,
                                <span id="sex">女</span>,
                                <span id="age">24</span>
                                岁,
                                <span id="areaDes">四川-自贡-自流井区</span>,
                                <span id="sg">162</span>
                                厘米,
                                <span id="xn">本科</span>
                                </li>
                        </ul>

                <div id="gallery">
                </div>

<ul data-role="listview" class="ui-listview">
<li class="ui-li ui-li-static ui-btn-up-c" style="color: #742157; font-size: 13px;
font-weight: 100;">
<span id="zwms">在生活中我是一个安静的女生，不过有时也会有点小脾气，觉得两个人一起信任是重要的。</span></li>
<li class="ui-li ui-li-static ui-btn-up-c">
<h3 class="ui-li-heading">
交友原因
</h3>
<p class="ui-li-desc">
<span id="yy">缓解压力 孤独寂寞 心理需要 空虚无聊 孤枕难眠</span>
</p>
</li>
<li class="ui-li ui-li-static ui-btn-up-c">
<h3 class="ui-li-heading">
我能提供
</h3>
<p class="ui-li-desc">
<span id="tg1">精神帮助 其他帮助</span>
</p>
</li>
<li class="ui-li ui-li-static ui-btn-up-c">
<h3 class="ui-li-heading">
我期望获得
</h3>
<p class="ui-li-desc">
<span id="hd1">精神帮助 其他帮助</span>
</p>
</li>
<li class="ui-li ui-li-static ui-btn-up-c">
<h3 class="ui-li-heading">
期望的情人
</h3>
<p class="ui-li-desc">
<span id="wxz">只要你懂浪漫</span>
</p>
</li>
<li class="ui-li ui-li-static ui-btn-up-c">
<h3 class="ui-li-heading">
情爱观念
</h3>
<p class="ui-li-desc">
<span id="xa">开放 性欲旺盛 偏向短期关系 好新鲜 爱成熟型 拒绝嫖妓的</span>
</p>
</li>
<li class="ui-li ui-li-static ui-btn-up-c">
<h3 class="ui-li-heading">
我的外貌
</h3>
<p class="ui-li-desc">
<span id="wm">性感 迷人 妩媚 长发 惹火身材 风情万种 成熟少妇 丰韵犹在</span>
</p>
</li>
<li class="ui-li ui-li-static ui-btn-up-c">
<h3 class="ui-li-heading">
我的个性
</h3>
<p class="ui-li-desc">
<span id="gx">开朗 开放 活泼 温柔 贤惠 真诚 体贴 成熟 天真 简单 通情达理</span>
</p>
</li>
<li class="ui-li ui-li-static ui-btn-up-c">
<h3 class="ui-li-heading">
我的兴趣
</h3>
<p class="ui-li-desc">
<span id="xq">拥抱 调情 旅游 追求刺激 运动建身 上网聊天</span>
</p>
</li>
</ul>
<hr />
<p>
<a id="homePage" data-role="button" data-theme="b" class="button" href="http://huaban123.com/space/430.html">到她的主页看看</a>
</p>
<p class="footer">
花瓣网<a href="http://www.huaban123.com" style="color: #b42a7c;">www.huaban123.com</a>-全国首家微信互动互助交友平台。【小提示：你的花瓣网账号也可以在电脑上
登陆哦！查看更多帅哥美女资料，拒绝寂寞，拒绝孤单！】
</p>
</div>
<!--/.content-->
<div data-theme="a" data-role="footer" data-position="fixed">
<h3 class="footer">
&copy; 2009－2013 huaban123.com, all rights reserved
</h3>
</div>
</div>
</form>
</body>
<script>
$('img[data-large]').touchGallery({
        getSource: function () {
                return $(this).attr('data-large');
        }
});
</script>
</html>

