<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require_once( dirname(__FILE__) . '/../lib/strategy.class.php' );
require_once( dirname(__FILE__) . '/../lib/config.class.php' );

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
                $this->_context->del( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk:circle_stack' );
        }

        //基本的信息返回 help 
        public function test_help_cmd() {
                $this->_context->set( 'circle' , 'common' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'help' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() );
                var_dump($post_obj  );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );
        }
 
        //输入非指令 显示笑话 未注册的情况
        public function test_show_joke_when_cmd_invalid() {
                $this->_context->set( 'circle' , 'common' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'invalid' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $this->assertTrue( preg_match( '/注册/' , $post_obj->Content ) );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );
        }

        //输入非指令 显示笑话 已经注册的情况
        public function test_show_joke_when_cmd_invalid_after_reg() {
                $this->_context->set( 'is_reg' , true );
                $this->_context->set( 'circle' , 'common' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'invalid' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                $this->assertTrue( preg_match( '/查询/' , $post_obj->Content ) );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );
        }

        //测试 h 
        public function test_h() {
                $this->_context->set( 'circle' , 'common' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'h' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() );
                $this->assertTrue( $post_obj->MsgType == 'text' );
                echo $post_obj->Content;
        }
}
