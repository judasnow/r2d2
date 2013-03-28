<?php
/**
 * 系统配置信息
 */
class Config {
        static public $store_server = 'tcp://172.17.0.46:6379/';
        static public $huaban123_server = 'http://icket.us:1979/';
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

                //基本帮助信息
                'help' => '目前可以使用的指令有：
1、使用微信【+】号下面的“位置”发送地理位置
2、进入注册zc
3、更换查看性别h
4、上传照片sczp（注册后使用）
5、退出注册q
6、打开帮助help
7、客服邮箱：981789018@qq.com',

                //未注册时 笑话后的附加消息
                'joke_extra_info_before_reg' => '【可以输入“zc”进行注册，或者输入“？”获取帮助信息】',
                'joke_extra_info_after_reg' => '【可以输入“c”进行详细查询，或者输入“？”获取帮助信息】',

                //成功进入 search_method_selcet circle 时的提示信息
                'enter_search_method_selcet' => '按条件查询附近的人，请输入查询条件前数字编号：
1按身高查询
2按照体重查询
3按照年龄查询',
                //输入无效信息 不是 1 2 3 
                'search_method_selcet_input_invalid' => '请按照提示输入相应数字编号查询：
1按身高查询
2按照体重查询
3按照年龄查询',

                //未注册的时候 尝试进入 search_method_selcet circle 时的提示信息
                'enter_search_method_selcet_without_reg' => '需要注册之后才能使用高级搜索功能啊，亲，可以按：身高，体重，年龄查询。赶快输入 "zc" 注册吧。',

                //未注册时查询次数达到最大
                'search_count_outrange_before_reg' => '咦，您还未注册，已达到查询%s次的上限。赶快输入“zc”开始注册吧，获得更多的查询机会哦。注册后，你也可以出现在附近被推荐哦。',
                //已经注册之后 查询次数达到最大
                'search_count_outrange_after_reg' => '你已超过每天查询%s次上限，请用注册账号登陆花瓣网查询，不限查询次数，试试吧。【花瓣网】-遇见出色男女，结识异性伴侣，约会同城情人，全国首家微信互动互助交友平台[http://www.huaban123.com]',

                //注册时每一步成功时的提示信息
                'input_success_message' =>  array(
                        'just_begin' => '请在下面4选项中选择一个与您相符的列表编号输入~选择性别后不可更改哦。
1、我是【男】，想查看【女】；
2、我是【女】，想查看【男】；
3、我是【男】，想查看【男】；
4、我是【女】，想查看【女】。',
                        //用户还没有设置地址信息时的情况
                        'sex_and_target_sex_index_without_location' => '设置性别及取向成功！请发送微信【+】号下面的“位置”信息作为你所在城市信息。',
                        //用户已经设置了地址信息
                        'sex_and_target_sex_index_with_location' => '设置性别及取向成功！请输入一个用户名，可以由字母，数字以及下划线组成',
                        'location' => '城市信息设置成功！请输入用户名，由字母、数字以及下划线组成,不少于3个字符',
                        'username' => '用户名设置成功！请输入你的昵称，给自己取个好听的名字，由中文、数字和字母组成，不低于4个字符',
                        'nickname' => '昵称设置成功！请输入你的QQ号码',
                        'qq' => '设置QQ号码成功！请选择你的身高（厘米），150-230之间，请输入此范围的数字',
                        'height' => '身高输入成功！请输入您的体重（斤），70-230之间，请输入此范围的数字',
                        'weight' => '体重输入成功！请输入你的年龄（岁），18-60之间，请输入此范围数字',
                        'age' => '年龄输入成功！请输入你的交友宣传（140字符以内）',
                        'zwms' => '交友宣传输入成功！上传几张照片吧，有照片会员才能获得更多关注哦。第一张照片将作为你的头像，务必上传本人清晰生活照.。请勿上传色情、反动、风景照，否则不予审核通过。上传头像步骤：按输入框旁“+”选择照片或拍摄。'
                ),
                //注册时 用户输入无效信息时的提示
                'input_invalid_message' => array(
                        'sex_and_target_sex_index' => '请按照提示输入每一个选项前面的数字，（1 或 2 或 3 或 4）',
                        //获取中文标签信息失败
                        'location_fetch_label_fail' => '获取地图位置信息失败，请手动输入你所在地级市/州城市名，如成都或成都市。',
                        //用户输入地级城市信息无效
                        'location_input_city_name_invalid' => '输入城市名错误，请重新输入你的地级市城市名，如成都或成都市。',
                        'username_has_reg' => '此用户名太受欢迎，已被注册啦，换一个吧~',
                        'username' => '请重新输入用户名，由字母、数字以及下划线组成,不少于3个字符~',
                        'nickname_has_reg' => '此昵称太受欢迎已被注册啦，换一个吧~',
                        'nickname' => '昵称输入不合法，请重新输入！给自己取个好听的名字，昵称由中文、数字和字母组成，不低于4个字符',
                        'qq' => '亲~ QQ号没输对哦，请重新输入你的QQ号码',
                        'height' => '身高输入不合法，请输入150-230之间的数字',
                        'weight' => '体重输入不合法，请输入70-230之间的数字',
                        'age' => '年龄输入不合法，请输入18-60范围内的数字',
                        'zwms' => '超过字符限制了啊 亲，要在140个字符之内啊'
                ),

                //返回注册流程时 对于下一项的提示信息 
                //要注意各个步骤之间的顺序
                'when_back_reg' => array(
                        'just_begin' => '请在下面4选项中选择一个与您相符的列表编号输入~选择性别后不可更改哦。
1、我是【男】，想查看【女】；
2、我是【女】，想查看【男】；
3、我是【男】，想查看【男】；
4、我是【女】，想查看【女】。',
                        'sex_and_target_sex_index' => '欢迎注册，请在下列 4 个选项中选择一个与您相符的，输入列表编号。1 我是男，查看女。2 我是男，查看男。3 我是女，查看男。4 我是女，查看女。',
                        'location' => '上次注册时，设置性别及取向已经成功，请发送微信【+】号下面的“位置”信息作为你所在城市信息。',
                        'username' => '上次注册时，城市信息已经输入成功了，请输入一个用户名由字母、数字以及下划线组成,不少于3个字符',
                        'nickname' => '上次注册时，用户名已经输入成功了，请输入你的昵称，给自己取个好听的名字，由中文、数字和字母组成，不低于4个字符',
                        'qq' => '上次注册时，昵称已经输入成功了，请输入QQ号码',
                        'height' => '上次注册时，QQ号码已经成功输入了，请输入你的身高（厘米），150-230之间，请输入此范围的数字',
                        'weight' => '上次注册时，身高已经输入成功了，请输入体重（斤），70-230之间，请输入此范围的数字',
                        'age' => '上次注册时，体重已经输入成功了，请输入你的年龄（岁），18-60之间，请输入此范围数字',
                        'zwms' => '上次注册时，你的年龄已经输入成功了，请输入你的交友宣传（140字符以内）',
                        'upload_image' => '上次注册时，交友宣传已经输入成功了，作为注册的最后一步，上传几张照片吧，有照片会员才能获得更多关注哦。第一张照片将作为你的头像，务必上传本人清晰生活照.。请勿上传色情、反动、风景照，否则不予审核通过。上传头像步骤：按输入框旁“+”选择照片或拍摄。'
                ),

                //上传照片成功
                'upload_image_success' => '上传成功！已上传%s张照片。可继续上传也可以输入“q”退出上传照片模式。',
                //上传照片失败
                'upload_image_fail' => '上传照片失败，请稍后再试一试',
                //上传照片发送信息格式不正确
                'upload_image_invalid' => '确定你发送的是图片哦~亲。上传头像步骤：按输入框旁“+”选择照片或拍摄。',

                //注册成功时的提示
                'reg_success' => '成功退出上传照片模式！恭喜你注册成功！
【花瓣网定位真实、高端、高品质互助交友网站；拒绝寂寞，拒绝孤单，用爱和帮助温暖彼此。】
你的注册信息如下：
----------------------------------
您的用户名：%s
您的密码：huaban123（默认密码，可到站点进行修改）
您的昵称：%s
您的QQ：%s
您的身高：%s
您的体重： %s
您的邮箱地址：%s
您的交友宣言：%s
------------------------------------
查看附近的人请发送微信【+】号下面的“位置”；
输入“c”按条件查询附近的人；
输入“h”更换查看性别。',

                //退出某种模式时 显示给用户的信息
                'quit_circle' => array(
                        'reg' => '还没注册完呢，你可以输入 “zc” 继续进行注册。',
                        'upload_image' => '成功退出上传照片模式！',
                        'search_method_selcet' => '已经成功退出选择查询条件模式',
                        'search_by_age' => '1 输入身高查询（150-230）。
2 输入体重查询（70-220）。
3 输入年龄查询（18-60）'
                ),

                //这是在期待用户输入一个可能包含地址信息的消息时 
                //输入的信息无效(不可能包含地址信息) 区别于输入的二级地址无效
                'location_input_valid' => '请直接发送地址信息或输入地级市名称查询。',

                //搜索结果的提示信息
                'search_result_tips_before_reg' => '[小提示：换一个请回复“n”，
换性别请回复“h”，如也想被
推荐请输入注册“ZC”查看TA
的更多资料请阅读全文。]',
                'search_result_tips_after_reg' => '[www.huaban123.com
花瓣网-全国首家互助交友平台
小提示：换一个请回复“n”，
换性别请回复“h”，查看TA
的更多资料请阅读全文。输入
“c”按身高、体重、年龄查看
附近的人。]'

        ); 
}
