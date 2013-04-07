<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require_once( '../lib/strategy.class.php' );

class Test_sex_and_target_sex_selcet extends UnitTestCase {

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

        //我是男查看女
        function test_input_1(){
                $this->_context->set( 'circle' , 'common' );
                $this->_context->set( 'location' , '自贡' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
   
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );

                //再输入一个符合要求的 没有提前设置地址信息的情况
                $strategy = new Strategy( sprintf( self::TEXT_XML , 1 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //断言结果
                $sex = $this->_context->get( 'sex' );
                $target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $sex == '男' && $target_sex == '女' );
        }

        //我是女查看男
        function test_input_2(){
                $this->_context->set( 'circle' , 'common' );
                $this->_context->set( 'location' , '自贡' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
   
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );

                //再输入一个符合要求的 没有提前设置地址信息的情况
                $strategy = new Strategy( sprintf( self::TEXT_XML , 2 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //断言结果
                $sex = $this->_context->get( 'sex' );
                $target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $sex == '女' && $target_sex == '男' );
        }

        //我是男查看男
        function test_input_3(){
                $this->_context->set( 'circle' , 'common' );
                $this->_context->set( 'location' , '自贡' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
   
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );

                //再输入一个符合要求的 没有提前设置地址信息的情况
                $strategy = new Strategy( sprintf( self::TEXT_XML , 3 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //断言结果
                $sex = $this->_context->get( 'sex' );
                $target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $sex == '男' && $target_sex == '男' );
        }

        //我是女查看女
        function test_input_4(){
                $this->_context->set( 'circle' , 'common' );
                $this->_context->set( 'location' , '自贡' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
   
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );

                //再输入一个符合要求的 没有提前设置地址信息的情况
                $strategy = new Strategy( sprintf( self::TEXT_XML , 4 ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //断言结果
                $sex = $this->_context->get( 'sex' );
                $target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $sex == '女' && $target_sex == '女' );
        }
}

