<?php
//重新对其进行彻底的测试
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require( dirname(__FILE__) . '/../lib/strategy.class.php' );

class Test_of_strategy extends UnitTestCase{

        //普通的 xml 请求
        const TEXT_XML = '<xml><ToUserName><![CDATA[gh3_cbb742f45d8f]]></ToUserName>
                <FromUserName><![CDATA[oJenljo3-kzzUDI8SK0fcNfFoFlQk]]></FromUserName>
                <CreateTime>1363395638</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <MsgId>5855739676719055765</MsgId>
                </xml>';

        //图片请求
        const IMAGE_XML = '<xml><ToUserName><![CDATA[gh_cbb742f45d8f]]></ToUserName>
                <FromUserName><![CDATA[oJenljo3-kzzUDI8SK0fcNfFoFlQk]]></FromUserName>
                <CreateTime>1363799720</CreateTime>
                <MsgType><![CDATA[image]]></MsgType>
                <PicUrl><![CDATA[http://mmsns.qpic.cn/mmsns/8GtV9x92iasGVARp3U9FWQz1JzasqMNiausANniciaiaFR0EmsOeQTeKZRQ/0]]></PicUrl>
                <MsgId>5857475195693958630</MsgId>
                </xml>';

        //位置信息
        const LOCATION_XML = '<xml><ToUserName><![CDATA[gh3_cbb742f45d8f]]></ToUserName>
                <FromUserName><![CDATA[oJenljo3-kzzUDI8SK0fcNfFoFlQk]]></FromUserName>
                <CreateTime>1362624002</CreateTime>
                <MsgType><![CDATA[location]]></MsgType>
                <Location_X>29.353037</Location_X>
                <Location_Y>104.770287</Location_Y>
                <Scale>20</Scale>
                <Label><![CDATA[%s]]></Label>
                <MsgId>5852425525334639049</MsgId>
                </xml>';

        public function setUp(){
                $this->_strategy = new Strategy( self::TEXT_XML );
                $this->_context = new Context( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'hello2bizuser' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
        }

        public function tearDown(){
                $this->_context->del( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk' );
                $this->_context->del( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk:circle_stack' );
        }

        //测试关注之后的首条回应 
        //也同时需要测试一些相关的初始化操作是否正确的被执行
        public function _test_when_follow(){
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'Hello2BizUser' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                $this->assertTrue( !empty( $post_obj->Content ) );

                $target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $target_sex == '女' );
        }

        //测试普通文本回复信息
        public function _test_make_res_simple(){
                //初始状态用户应该处于 common circle
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'help' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                //返回的内容不能确定 因此断言其为非空
                $this->assertTrue( !empty( $post_obj->Content ) );
        }

        //测试提交 location 或者 地级市的名称 会进入 look_around circle
        //同时也会尝试进行 location 的设置

        //输入有效地址信息的情况
        function test_either_user_input_location_msg_or_city_name_circle_will_be_lookaround (){
                //断言 context location = null
                $location = $this->_context->get( 'location' );
                $this->assertTrue( empty( $location ) );

                //1
                //输入有效地址信息的情况
                $strategy = new Strategy( sprintf( self::TEXT_XML , '自贡' ) );

                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                //断言会返回一条用户信息
                $this->assertTrue( $post_obj->MsgType == 'news' );
                //断言仍然处于 look_around circle 
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                //2
                //输入 n
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                //断言会返回一条用户信息
                $this->assertTrue( $post_obj->MsgType == 'news' );

                //断言 search_count 被加2
                $search_count = $this->_context->get( 'search_count' );
                $this->assertTrue( $search_count == 2 );

                //断言进行第4次搜索的时候 会提示注册
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                echo $search_count = $this->_context->get( 'search_count' );
                $this->assertTrue( $search_count == 3 );
                $this->assertTrue( $post_obj->MsgType == 'text' );
        }

        //输入无效地址的情况
        //label 为空
        public function test_user_input_empty_label() {
                $location = $this->_context->get( 'location' );
                $this->assertTrue( empty( $location ) );

                //输入有效地址信息的情况
                $strategy = new Strategy( sprintf( self::LOCATION_XML , '' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                //echo $$post_obj->Content;
                $circle = $this->_context->get( 'circle' );
                //$this->assertTrue( $circle != 'look_around' );
        }

        //测试 joke 在需要的时候 返回 joke
        public function test_joke() {

        }

        //先错后对
        public function test_do_reg() {

                $this->_context->set( 'circle' , 'common' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                //echo $post_obj->Content . '<br />';
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );

                //需要输入 1 2 3 4

                //先输入一个不符合的 
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'hehe' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $post_obj->Content . '<br />';
                //断言没有 next_step 没有改变
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'sex_and_target_sex_index' );

                //再输入一个正确的 1 
                $strategy = new Strategy( sprintf( self::TEXT_XML , 1 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $post_obj->Content . '<br />';
                //断言 next_step 变成了 location
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'location' );
                //同时断言 sex , target_sex , index 都已经设置了
                $sex = $this->_context->get( 'sex' );
                $target_sex = $this->_context->get( 'target_sex' );
                $sex_and_target_sex_index = $this->_context->get( 'sex_and_target_sex_index' );
                $this->assertTrue( $sex == '男' );
                $this->assertTrue( $target_sex == '女' );
                $this->assertTrue( $sex_and_target_sex_index == 1 );

                //location

                //没有location的情况

                //输入错误城市
                $this->_context->set( 'location' , '' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , '旧金山' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $post_obj->Content . '<br />';
                //断言返回错误信息
                $location = $this->_context->get( 'location' );
                $this->assertTrue( empty( $location ) );

                //输入正确城市
                $strategy = new Strategy( sprintf( self::TEXT_XML , '天津' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $post_obj->Content . '<br />';
                $location = $this->_context->get( 'location' );
                $this->assertTrue( $location == '天津' );

                //已经输入了 locaton 的情况 比如用户已经 look_around 了
                //这里期待的行为应该是 直接跳过这一步
                $this->_context->set( 'location' , '自贡' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , rand() ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';
                echo $location = $this->_context->get( 'location' );
                $this->assertTrue( $location == '自贡' );
                //输入 username 
                $strategy = new Strategy( sprintf( self::TEXT_XML , rand() ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';

                //q
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'q' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );

                //从新 zc
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );

                //nickname
                $strategy = new Strategy( sprintf( self::TEXT_XML , rand() ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';

                //q
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'q' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );

                //身高
                $strategy = new Strategy( sprintf( self::TEXT_XML , 180 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';
                $height = $this->_context->get( 'height' );
                $this->assertTrue( $height == 180 );

                //体重
                $strategy = new Strategy( sprintf( self::TEXT_XML , 79 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';
                $weight = $this->_context->get( 'weight' );
                $this->assertTrue( $weight == 79 );

                //年龄
                $strategy = new Strategy( sprintf( self::TEXT_XML , 25 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';
                $age = $this->_context->get( 'age' );
                $this->assertTrue( $age == 25 );

                //qq
                $strategy = new Strategy( sprintf( self::TEXT_XML , 526573979 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';
                $weight = $this->_context->get( 'qq' );
                $this->assertTrue( $weight == 526573979 );

                //交友宣言
                $strategy = new Strategy( sprintf( self::TEXT_XML , 526573979 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'upload_image' );

                //到此时 其实数据已经提交服务器 而且获取了 user_id
                //断言 user_id 已经存在
                $user_id = $this->_context->get( 'user_id' );
                $this->assertTrue( is_numeric( $user_id ) );
                $is_reg = $this->_context->get( 'is_reg' );
                $this->assertTrue(  $is_reg == true );

                //尝试上传一张照片
                $strategy = new Strategy( self::IMAGE_XML );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo  $post_obj->Content;
                echo $image_count = $this->_context->get( 'image_count' );
                $this->assertTrue( $image_count == 1 );

                //再传一张 image
                $strategy = new Strategy( self::IMAGE_XML );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo '<hr/>';
                echo $circle = $this->_context->get( 'circle' );
                var_dump( $circle );
                $this->assertTrue( $circle == 'upload_image' );
                echo $image_count = $this->_context->get( 'image_count' );
                $this->assertTrue( $image_count == 2 );
                echo $post_obj->Content;

                //q 退出上传流程完成注册
                //但此时 q 直接就到 common 了 怎么破？
                //观察状态 似乎 last_circle 只有 common 为什么 从 reg 到
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'q' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );
        }

        //测试注册途中的退出操作
        public function test_reging_q_and_back() {
                $this->_context->set( 'circle' , 'common' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                echo $post_obj->Content . '<br />';
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );

                //需要输入 1 2 3 4

                //输入一个正确的 1 
                $strategy = new Strategy( sprintf( self::TEXT_XML , 1 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $post_obj->Content . '<br />';
                //断言 next_step 变成了 location
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'location' );
                //同时断言 sex , target_sex , index 都已经设置了
                $sex = $this->_context->get( 'sex' );
                $target_sex = $this->_context->get( 'target_sex' );
                $sex_and_target_sex_index = $this->_context->get( 'sex_and_target_sex_index' );
                $this->assertTrue( $sex == '男' );
                $this->assertTrue( $target_sex == '女' );
                $this->assertTrue( $sex_and_target_sex_index == 1 );

                //q
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'q' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );

                //back 
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo "<b>" . $post_obj->Content . '</b><br />';
                //之前已经正确的填写了 1234 应该直接提示用户填写用户名才对
                $this->assertTrue( stristr( $post_obj->Content , '用户名' ) );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );

                $this->_context->set( 'location' , '自贡' );

                //输入用户名
                $strategy = new Strategy( sprintf( self::TEXT_XML , rand() ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $username = $this->_context->get( 'username' );
                $this->assertTrue( !empty($username) );

                //q
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'q' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );

                //back  again

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo '<hr />';
                echo $post_obj->Content . '<br />';
                // 之前已经正确的填写了 用户名 应该直接提示用户填写 昵称 才对
                $this->assertTrue( stristr( $post_obj->Content , '昵称' ) );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );
        }

        //search 
        public function test_search() {
                $strategy = new Strategy( sprintf( self::TEXT_XML , 's' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , '1' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_height' );

                //按身高查询
                $strategy = new Strategy( sprintf( self::TEXT_XML , '160' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . '<br />';
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_height' );
        }

        //测试在注册已经成功的前提之下 输入 zc 将不会再进入注册模式
        //而是会提示用户已经注册成功 无需再注册
        public function test_user_input_zc_when_user_has_reg() {
                $this->_context->set( 'is_reg' , true );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump(  $post_obj );
        }
}
