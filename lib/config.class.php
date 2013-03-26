<?php
/**
 * 系统的配置信息
 */
class Config {
        static public $store_server = 'tcp://172.17.0.46:6379/';
        static public $huaban123_server = 'http://172.17.0.20:1979/';
        //static public $store_server = 'tcp://127.0.0.1:6379/';
        //static public $huaban123_server = 'http://localhost:1979/';

        //没有注册时最多查询次数 注意是从 0 开始的
        static public $max_search_count_without_reg = 2;

        //注册之后最多查询次数
        static public $max_search_count_with_reg = 5;

        //语言信息配置
        static public $response_msg = array( 
                //用户关注之后需要发送的消息
                'hello2bizuser' => '【欢迎关注花瓣网(www.huaban123.com)公众号：huaban123。邂逅出色男女,结交异性伴侣，约会同城情人！花瓣网-全国首家微信互动互助交友平台】你把位置信息发过来，花瓣网就回你一个附近的人。还在等什么？赶快行动吧！提醒下，不是用打字发位置，那早就OUT啦，是用微信【+】号下面的“位置”功能发送！
你可以：
1、发送微信【+】号下面的“位置”查看附近注册的人；
2、输入“zc”注册账号；
3、输入“h”更换查看性别；
4、输入“help”查看帮助信息。',

                //基本帮助信息 注册前
                'help_before_reg' => '目前可以使用的指令有：
1、使用微信【+】号下面的“位置”发送地理位置
2、进入注册zc
3、更换查看性别h
4、退出注册q
5、打开帮助help
6、客服邮箱：:981789018@qq.com',

                //帮助信息 注册之后
                'help_after_reg' => '目前可以使用的指令有：
1、使用微信【+】号下面的“位置”发送地理位置
2、更换查看性别h
3、上传照片sczp
4、退出注册q
5、打开帮助help
6、客服邮箱：:981789018@qq.com'
                
        );  
}
