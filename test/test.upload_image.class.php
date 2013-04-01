<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require( dirname(__FILE__) . '/../lib/curl.class.php' );
require( dirname(__FILE__) . '/../lib/strategy.class.php' );

class Test_of_curl extends UnitTestCase{

        //图片请求
        const IMAGE_XML = '<xml><ToUserName><![CDATA[gh_cbb742f45d8f]]></ToUserName>
                <FromUserName><![CDATA[oJenljo3-kzzUDI8SK0fcNfFoFlQk]]></FromUserName>
                <CreateTime>1363799720</CreateTime>
                <MsgType><![CDATA[image]]></MsgType>
                <PicUrl><![CDATA[http://mmsns.qpic.cn/mmsns/8GtV9x92iasGVARp3U9FWQz1JzasqMNiausANniciaiaFR0EmsOeQTeKZRQ/0]]></PicUrl>
                <MsgId>5857475195693958630</MsgId></xml>';

        //普通的 xml 请求
        const TEXT_XML = '<xml><ToUserName><![CDATA[gh3_cbb742f45d8f]]></ToUserName>
                <FromUserName><![CDATA[oJenljo3-kzzUDI8SK0fcNfFoFlQk]]></FromUserName>
                <CreateTime>1363395638</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <MsgId>5855739676719055765</MsgId></xml>';

        public function setup(){
                $this->_context = new Context( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk' );
        }

        public function tearDown(){
                $this->_context->del( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk' );
                $this->_context->del( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk:circle_stack' );
        }

        public function test_uploading_a_lot_of_image(){
                $this->_context->set( 'user_id' , '544' );
                $this->_context->set( 'is_reg' , true );
                $this->_context->set( 'circle' , 'upload_image' );

                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
                $strategy = new Strategy( self::IMAGE_XML );
                var_dump( $strategy->make_res() );
        }
}

