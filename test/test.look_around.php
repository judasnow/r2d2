<?php
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
                <FromUserName><![CDATA[oJenljo-kzzUDI8SK0fcNfFoFlQk]]></FromUserName>
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
                $this->_context->set( 'search_count' , 0 );
        }

        public function tearDown(){
                $this->_context->del( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk' );
        }

        //测试用户直接输入 地址信息
        public function _test_user_input_city_name(){
                $strategy = new Strategy( sprintf( self::TEXT_XML , '自贡' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );
        }

        //测试用户直接输入没有label 信息的 location 信息
        public function _test_user_send_location_info_but_label_is_null() {
        //{{{
                $strategy = new Strategy( sprintf( self::LOCATION_XML , '' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );
                $last_search_cond = $this->_context->get( 'last_search_cond' );
                $this->assertTrue( !empty( $last_search_cond ) );

                //测试用户输入 h
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'h' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );
                $target_sex = $this->_target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $target_sex == '男' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
               // var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );
                $target_sex = $this->_target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $target_sex == '男' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'h' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );
                $target_sex = $this->_target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $target_sex == '女' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );
                $target_sex = $this->_target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $target_sex == '女' );

                //这次再发送就要出错 why?
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'h' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );
                $target_sex = $this->_target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $target_sex == '男' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $circle = $this->_context->get( 'circle' );
                var_dump( $post_obj );
                $this->assertTrue( $circle == 'common' );
        }//}}}

        //测试用户在 查找周围人的时候 输入 c 
        //期待的反映是 进入 search_mothed_select circle
        public function _test_user_input_c_when() {
        //{{{
                $strategy = new Strategy( sprintf( self::LOCATION_XML , '' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );
                $last_search_cond = $this->_context->get( 'last_search_cond' );
                $this->assertTrue( !empty( $last_search_cond ) );

                //测试用户输入 h
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'h' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );
                $target_sex = $this->_target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $target_sex == '男' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );
                $target_sex = $this->_target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $target_sex == '男' );


                $strategy = new Strategy( sprintf( self::TEXT_XML , 'h' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );
                $target_sex = $this->_target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $target_sex == '女' );

                //输入 c 未注册的情况 
                //断言应该提示用户注册之
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'c' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                //已经注册的情况 
                //断言会进入到 search_method_select
                $this->_context->set( 'is_reg' , true );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'c' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_method_select' );

                //输入1
                $strategy = new Strategy( sprintf( self::TEXT_XML , '1' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_height' );

                $this->_context->set( 'location' , '自贡' );

                $strategy = new Strategy( sprintf( self::TEXT_XML ,  '160' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_height' );

                //测试q
                //断言跳到最外层
                $strategy = new Strategy( sprintf( self::TEXT_XML ,  'q' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                echo $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );
        }//}}}

        //测试未注册 查询次数限制
        //期待的反映是 达到3次（包括）提示用户注册 而且跳转到 最外层循环
        public function test_max_search_count_without_reg() {
        //{{{
                //1
                $strategy = new Strategy( sprintf( self::LOCATION_XML , '' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                $last_search_cond = $this->_context->get( 'last_search_cond' );
                $this->assertTrue( !empty( $last_search_cond ) );

                //2
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                //3
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                //4
                //期待提示用户 zc 而且返回 common
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );
        }//}}}

        //测试注册状态 查询次数限制
        public function _test_max_search_count_with_reg() {
        //{{{
                $this->_context->set( 'is_reg' , true );
                //1
                $strategy = new Strategy( sprintf( self::LOCATION_XML , '' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                $last_search_cond = $this->_context->get( 'last_search_cond' );
                $this->assertTrue( !empty( $last_search_cond ) );

                //2
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                //3
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                //4
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                //5
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                //6
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                //7
                //期待提示用户 zc 而且返回 common
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );
        }//}}}

        //测试注册之后会清空之前的查询次数
        public function _test_reg_will_reset_search_count() {
        //{{{
                //1
                $strategy = new Strategy( sprintf( self::LOCATION_XML , '' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                $last_search_cond = $this->_context->get( 'last_search_cond' );
                $this->assertTrue( !empty( $last_search_cond ) );

                //2
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->MsgType == 'news' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'look_around' );

                //do_reg
                //{{{
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
   
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );

                //需要输入 1 2 3 4
                //先输入一个不符合的 
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'sdada' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'sex_and_target_sex_index' );

                //再输入一个符合要求的 没有提前设置地址信息的情况
                $strategy = new Strategy( sprintf( self::TEXT_XML , 3 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'location' );

                //输入一个错误地址
                $strategy = new Strategy( sprintf( self::TEXT_XML , '错误地址' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'location' );

                //再输入一个空的 location 信息
                $strategy = new Strategy( sprintf( self::LOCATION_XML , '' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'username' );

                //输入无效的 username 
                $strategy = new Strategy( sprintf( self::TEXT_XML , '哈哈哈' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'username' );

                //输入ok的 但是已经被占用的 username 
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'uutest' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'username' );

                //输入 ok 的 username 
                $strategy = new Strategy( sprintf( self::TEXT_XML , rand(1000000,999999999999) ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'nickname' );

                //输入正确的 nickname
                $strategy = new Strategy( sprintf( self::TEXT_XML , rand(1000000,999999999999) ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'qq' );

                //输入无效 qq
                $strategy = new Strategy( sprintf( self::TEXT_XML , '哈哈' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'qq' );

                //有效qq
                $strategy = new Strategy( sprintf( self::TEXT_XML , '526533979' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'height' );

                //无效身高
                $strategy = new Strategy( sprintf( self::TEXT_XML , '哈哈' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'height' );

                //有效身高
                $strategy = new Strategy( sprintf( self::TEXT_XML , '160' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'weight' );

                //无效体重
                $strategy = new Strategy( sprintf( self::TEXT_XML , '90000' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'weight' );

                //有效体重
                $strategy = new Strategy( sprintf( self::TEXT_XML , '200' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'age' );

                //无效年龄
                $strategy = new Strategy( sprintf( self::TEXT_XML , '200' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'age' );

                //有效年龄
                $strategy = new Strategy( sprintf( self::TEXT_XML , '20' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'zwms' );

                //有效zwms
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zwms' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );

                //成功注册
                $is_reg =$this->_context->get( 'is_reg' );
                $this->assertTrue( $is_reg == true );
                //}}}
                
                //断言 search_count = 0
                $search_count = $this->_context->get( 'search_count' );
                $this->assertTrue( $search_count == 0 );
        }//}}}

        //测试在达到了最大查询次数的时候输入 c 提示用户
        public function _test_reach_max_resarch_count_user_input_c_will_notice_user() {
        //{{{
                //未注册
                $this->_context->set( 'search_count' , 6 );
                $this->_context->set( 'is_reg' , true );
                $this->_context->set( 'circle' , 'common' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'c' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' ); 
        }//}}}
}
