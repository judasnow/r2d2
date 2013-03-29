<?php
//主要测试注册流程
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

        //先错后对
        public function _test_do_reg() {
        //{{{
                $this->_context->set( 'circle' , 'common' );
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

                //输入无效的 username 
                $strategy = new Strategy( sprintf( self::TEXT_XML , '1' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'username' );

                //输入无效的 username 
                $strategy = new Strategy( sprintf( self::TEXT_XML , '12' ) );
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

                //无效qq
                $strategy = new Strategy( sprintf( self::TEXT_XML , '01234567' ) );
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

                //上传照片
                $strategy = new Strategy( self::IMAGE_XML );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );
                $image_count = $this->_context->get( 'image_count' );
                $this->assertTrue( $image_count == 1 );

                $strategy = new Strategy( self::IMAGE_XML );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );
                $image_count = $this->_context->get( 'image_count' );
                $this->assertTrue( $image_count == 2 );

                $strategy = new Strategy( self::IMAGE_XML );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );
                $image_count = $this->_context->get( 'image_count' );
                $this->assertTrue( $image_count == 3 );

                //q
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'q' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );

                //断言已经退出到了 common
                echo $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );

        }//}}}
        
        //注册时奇怪查找
        public function _test_do_reg_spec() {
        //{{{
                $this->_context->set( 'circle' , 'common' );
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

                //不传照片直接退出 q
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'q' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );

                //再 zc 进去
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                echo $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'upload_image' );

                //上传照片
                $strategy = new Strategy( self::IMAGE_XML );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );
                $image_count = $this->_context->get( 'image_count' );
                $this->assertTrue( $image_count == 1 );

                $strategy = new Strategy( self::IMAGE_XML );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );
                $image_count = $this->_context->get( 'image_count' );
                $this->assertTrue( $image_count == 2 );

                $strategy = new Strategy( self::IMAGE_XML );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );
                $image_count = $this->_context->get( 'image_count' );
                $this->assertTrue( $image_count == 3 );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'q' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );
                echo $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );
        }//}}}

        //测试从 lookaround 进入 reg 之后的情况
        public function test_do_reg() {
        //{{{
                $this->_context->set( 'circle' , 'common' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , '自贡' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                
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

                //再输入一个符合要求的 提前设置地址信息的情况
                $strategy = new Strategy( sprintf( self::TEXT_XML , 3 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                echo $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'username' );

                //输入无效的 username 
                $strategy = new Strategy( sprintf( self::TEXT_XML , '哈哈哈' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                echo $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'username' );

                //输入无效的 username 
                $strategy = new Strategy( sprintf( self::TEXT_XML , '1' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'username' );

                //输入无效的 username 
                $strategy = new Strategy( sprintf( self::TEXT_XML , '12' ) );
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

                //无效qq
                $strategy = new Strategy( sprintf( self::TEXT_XML , '01234567' ) );
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

                //上传照片
                $strategy = new Strategy( self::IMAGE_XML );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );
                $image_count = $this->_context->get( 'image_count' );
                $this->assertTrue( $image_count == 1 );

                $strategy = new Strategy( self::IMAGE_XML );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );
                $image_count = $this->_context->get( 'image_count' );
                $this->assertTrue( $image_count == 2 );

                $strategy = new Strategy( self::IMAGE_XML );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );
                $image_count = $this->_context->get( 'image_count' );
                $this->assertTrue( $image_count == 3 );

                //q
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'q' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $next_step = $this->_context->get( 'reg_next_step' );
                $this->assertTrue( $next_step == 'upload_image' );

                //断言已经退出到了 common
                echo $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );
        
        }//}}}
}
