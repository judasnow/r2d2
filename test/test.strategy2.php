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
        }

        public function tearDown(){
                $this->_context->del( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk' );
        }

        //测试普通文本回复信息
        public function test_make_res_simple(){
                //初始状态用户应该处于 common circle
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'help' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                //返回的内容不能确定 因此断言其为非空
                $this->assertTrue( !empty( $post_obj->Content ) );
        }

        //测试关注之后的首条回应
        public function test_when_follow(){
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'Hello2BizUser' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                $this->assertTrue( !empty( $post_obj->Content ) );
        }

        //测试在还没有设置 location 信息的时候 提交任何信息 都会被定位到 location circle 
        //除了几个优先级特别高的 关键词
        function test_when_location_is_not_set_the_circle_will_be_location_whatever_input_is(){
                //断言 context location = null
                $location = $this->_context->get( 'location' );
                $this->assertTrue( empty( $location ) );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'qwe' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'location' );
        }

        //测试主动提交地址信息 location ,且 Label 不为空的情况
        public function test_set_location_by_location_msg(){
                $strategy = new Strategy( sprintf( self::LOCATION_XML , '四川省自贡市新民街' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $post_obj->Content;
                $this->assertTrue( $post_obj->Content == '位置设置成功' );
        }

        //测试主动提交地址信息 location ,但 Label 为空的情况
        public function test_set_location_by_location_msg_but_lable_is_null(){
                $strategy = new Strategy( sprintf( self::LOCATION_XML , '' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                $this->assertTrue( $post_obj->Content == '额 你发的地址没有中文标签啊,手动输入地级市名称吧.' );
        }

        //测试输入有效地级市名称的情况
        public function test_set_location_by_input_valid_city_name(){
                $strategy = new Strategy( sprintf( self::TEXT_XML , '自贡' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                $this->assertTrue( $post_obj->Content == '位置设置成功' );
                //回到 common circle
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );
        }

        //测试输入无效地级市名称的情况
        public function test_set_location_by_input_invalid_city_name(){
                $strategy = new Strategy( sprintf( self::TEXT_XML , '那美克星' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                $this->assertTrue( $post_obj->Content == '输入的地址无效' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'location' );
        }

        //测试 在 common 下输入 zc 进入 circle reg
        public function test_input_zc_when_circle_is_common(){
                $this->_context->set( 'circle' , 'common' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                $this->assertTrue( $post_obj->Content == '请输入一个用户名,可以由字母数字以及下划线组成' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );
        }
}
