<?php
//{{{
require_once 'lib/store.class.php';
require_once 'lib/curl.class.php';
require_once 'lib/config.class.php';

//@todo 注入危险
$user_id = $_GET['user_id'];
//$weixin_id = $_GET['weixin_id'];
//$gallery_page_no = $_GET['gallery_page_no'];

//获取用户数据
$store = new Store();
$user_info_json = $store->get( 'user_info:' . $user_id );

//如果为空 则需要调用 api 获取信息
if( empty( $user_info ) ) {
        $user_info_json = Curl::post( Server_config::$huaban123_server . "/action/weixinmpapi.aspx?action=userInfo&user_id=$user_id&gallery_page_no=1" );
        $store->set( 'user_info:' . $user_id , $user_info_json );
}

$user_info = json_decode( json_decode( $user_info_json , true )['info'] , true );
$user_common_info = json_decode( $user_info[0]["info"] , true );
$user_photo_info =  json_decode( $user_info[1]["info"] , true );

$pron = '她';
$default_user_head_pic = '/jsimages/woman.jpg';
if( $user_common_info['UserSex'] == '男' ) {
        $pron = '男';
        $default_user_head_pic = '/jsimages/man.jpg';
}

//判断用户头像是否存在
if( empty( $user_common_info['HeadPic'] ) ) {
        $user_head_pic = $user_head_pic_mini = $default_user_head_pic;
} else {
        $user_head_pic = '/UploadFiles/UHP/' . $user_common_info['HeadPic'];
        $user_head_pic_mini = '/UploadFiles/UHP/MIN/' . $user_common_info['HeadPic'];
}

//}}}
?>
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
                <script type="text/javascript" src="http://172.17.0.20:1979/js/jquery.touch-gallery-1.0.0.min.js"></script>

                <!--{{{-->
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
                        .summary_info {
                                color: #742157; 
                                font-family: '黑体';
                                font-weight: 100;
                        }
                        .zwms{
                                color: #742157; 
                                font-size: 13px;
                                font-weight: 100;
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
                                width: 100%;
                                overflow-x: hidden;
                                overflow-y: hidden;
                                padding-top: 5px;
                        }
                        #gallery_items {
                                overflow: hidden;
                                position: relative;
                                top: 0px;
                                margin: 0px;
                                padding: 0px;
                                width: 2048px;
                                list-style: none outside none;
                        }
                        .gallery_li {
                                display: inline;
                                float: left;
                                list-style:none;
                                margin: 0;
                                position: relative;
                                text-align: center;
                                z-index: 0;
                                margin-right: 8px;
                        }
                        .gallery_item_img {
                                width: 64px;
                                height: 64px;
                                border-radius: 5px;
                                border: 1px solid #bbb;
                                padding: 2px;
                        }
                        .button {
                                background : -webkit-linear-gradient( rgb(101, 57, 126),rgb(238, 57, 187) );
                        }
                        .ui-btn-up-b {
                                border: 1px solid rgb(71, 12, 73);
                        }
                </style>
                <!--}}}-->
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
                                        <li class="ui-li ui-li-static ui-btn-up-c summary_info">
                                        <!--一般信息-->
                                        <span id="nickName"><?php echo $user_common_info['NickName']; ?></span>,
                                        <span id="sex"><?php echo $user_common_info['UserSex']; ?></span>,
                                        <span id="age"><?php echo $user_common_info['Age']; ?></span>
                                岁,
                                <span id="areaDes"><?php echo str_replace( '-' , ' ' , $user_common_info['AreaDes'] ); ?></span>,
                                <span id="sg"><?php echo $user_common_info['SG']; ?></span>
                                厘米,
                                <span id="xn"><?php echo $user_common_info['XN']; ?></span>
                                </li>
                        </ul>
        
                        <p>
                        <div id="gallery">
                                <ul id="gallery_items">
                                <!--无论如何都会显示的头像-->
                                <li class="gallery_li">
                                        <img class="gallery_item_img" src="<?php echo Server_config::$huaban123_server . $user_head_pic_mini; ?>" data-large="<?php echo @Server_config::$huaban123_server . $user_head_pic; ?>"/>
                                </li>
                                <?php foreach( $user_photo_info as $no => $item ) { ?>
                                        <li class="gallery_li">
                                                <img class="gallery_item_img"
                                                        src="<?php echo Server_config::$huaban123_server; ?>/UploadFiles/UPP/MIN/<?php echo $item['PicName']; ?>"
                                                        data-large="<?php echo Server_config::$huaban123_server; ?>UploadFiles/UPP/<?php echo $item['PicName']; ?>"
                                                />
                                        </li>
                                <?php } ?>
                                </ul>
                        </div>
                        </p>
        
                        <ul data-role="listview" class="ui-listview">
                        <!--{{{-->
                                <li class="ui-li ui-li-static ui-btn-up-c zwms">
                                <span id="zwms"><?php echo $user_common_info['ZWMS']; ?></span>
                                </li>
                                <li class="ui-li ui-li-static ui-btn-up-c">
                                        <h3 class="ui-li-heading">
                                                交友原因
                                        </h3>
                                        <p class="ui-li-desc">
                                                <span id="yy"><?php echo $user_common_info['YY']; ?></span>
                                        </p>
                                </li>
                                <li class="ui-li ui-li-static ui-btn-up-c">
                                        <h3 class="ui-li-heading">
                                                我能提供
                                        </h3>
                                        <p class="ui-li-desc">
                                                <span id="tg1"><?php echo $user_common_info['TG1']; ?></span>
                                        </p>
                                </li>
                                <li class="ui-li ui-li-static ui-btn-up-c">
                                        <h3 class="ui-li-heading">
                                                我期望获得
                                        </h3>
                                        <p class="ui-li-desc">
                                                <span id="hd1"><?php echo $user_common_info['HD2']; ?></span>
                                        </p>
                                </li>
                                <li class="ui-li ui-li-static ui-btn-up-c">
                                        <h3 class="ui-li-heading">
                                                期望的情人
                                        </h3>
                                        <p class="ui-li-desc">
                                                <span id="wxz"><?php echo $user_common_info['WXZ']; ?></span>
                                        </p>
                                </li>
                                <li class="ui-li ui-li-static ui-btn-up-c">
                                        <h3 class="ui-li-heading">
                                                情爱观念
                                        </h3>
                                        <p class="ui-li-desc">
                                                <span id="xa"><?php echo $user_common_info['XA']; ?></span>
                                        </p>
                                </li>
                                <li class="ui-li ui-li-static ui-btn-up-c">
                                        <h3 class="ui-li-heading">
                                                我的外貌
                                        </h3>
                                        <p class="ui-li-desc">
                                                <span id="wm"><?php echo $user_common_info['WM']; ?></span>
                                        </p>
                                </li>
                                <li class="ui-li ui-li-static ui-btn-up-c">
                                        <h3 class="ui-li-heading">
                                                我的个性
                                        </h3>
                                        <p class="ui-li-desc">
                                                <span id="gx"><?php echo $user_common_info['GX']; ?></span>
                                        </p>
                                </li>
                                <li class="ui-li ui-li-static ui-btn-up-c">
                                        <h3 class="ui-li-heading">
                                                我的兴趣
                                        </h3>
                                        <p class="ui-li-desc">
                                                <span id="xq"><?php echo $user_common_info['XQ']; ?></span>
                                        </p>
                                </li>
                        </ul>
                        <!--}}}-->

                        <hr />
                        
                        <p>
                                <a id="homePage" data-role="button" data-theme="b" class="button" href="http://huaban123.com/space/<?php echo $user_common_info["UserId"]; ?>.html">到她的主页看看</a>
                        </p>
                       
                        <p class="footer">
                        花瓣网<a href="http://www.huaban123.com" style="color: #b42a7c;">www.huaban123.com</a>-全国首家微信互动互助交友平台。【小提示：你的花瓣网账号也可以在电脑上
                        登陆哦！查看更多帅哥美女资料，拒绝寂寞，拒绝孤单！】
                        </p>
                </div>

                <div data-theme="a" data-role="footer" data-position="fixed">
                        <h5>
                                &copy; 2009－2013 huaban123.com, all rights reserved
                        </h5>
                </div>

        </div>
</form>
</body>
<script>
        (function(){
                //设定触发 swipe 事件阀值
                $.event.special.swipe.scrollSupressionThreshold = "1px";

                //绑定滑动事件
                $gallery = $( "#gallery" );
                $galleryItems = $( "#gallery_items" );

                //type 表明的是向右还是向左滑动
                swipeHandler = function( type ) {
                        //取出原有的 left 值
                        left = $galleryItems.css( "left" ).replace( "px" , "" );
                        if( isNaN( left ) ) {
                                left = 0;
                        }

                switch( type ){
                        case 'left':
                                left = Number(left) - 50;

                                $galleryItems.stop().animate( { "left": left + "px" } , 500 );
                                break;
                        case 'right':
                                left = Number(left) + 50;

                                //最左
                                if( left > 0 ) {
                                        $galleryItems.css( "left" , "0px" );
                                        return false;
                                }

                                $galleryItems.stop().animate( { "left": left + "px" } , 500 );
                                break;
                        }
        }

        $gallery.bind( {
                swipeleft: function( event ) {
                        swipeHandler( 'left' );
                },
                swiperight: function( event ) {
                        swipeHandler( 'right' );
                }
        });

        $('img[data-large]').touchGallery({
                getSource: function () {
                        return $(this).attr('data-large');
                }
        });
})();
</script>
</html>

