<?php
//通过发送给 strategy 不同的已经解析好的 xml 文件
//测试其是否按预期返回内容
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
        }

        public function tearDown(){
                $this->_context->del( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk' );
        }

        //身高
        public function _test_search_method_select_height() {
        //{{{
                //未注册 c
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'c' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $post_obj->Content . "<br />";
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );

                $this->_context->set( 'is_reg' , true );
                $this->_context->set( 'location' , '自贡' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'c' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $post_obj->Content . "<br />";
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_method_select' );

                //输入 非法信息
                $strategy = new Strategy( sprintf( self::TEXT_XML , '阿斯顿简爱空手道' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $post_obj->Content . "<br />";
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_method_select' );

                //输入有效信息
                $strategy = new Strategy( sprintf( self::TEXT_XML , '1' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $post_obj->Content . "<br />";
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_height' );

                //输入身高信息
                $strategy = new Strategy( sprintf( self::TEXT_XML , '160' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //var_dump( $post_obj );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_height' );

                //输入 n
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //var_dump( $post_obj );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_height' );

                //输入 n
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //var_dump( $post_obj );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_height' );

                //输入 n
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //var_dump( $post_obj );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_height' );

                //输入 n
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //var_dump( $post_obj );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_height' );

                //输入 n
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //var_dump( $post_obj );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_height' );

                //7 输入 n
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'n' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                echo $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );

        }//}}}

        //体重
        public function _test_search_weight() {
                $this->_context->set( 'is_reg' , true );
                $this->_context->set( 'location' , '成都' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'c' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_method_select' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , '2' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_weight' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , '110' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_weight' );
        }

        //年龄
        public function _test_search_age() {
                $this->_context->set( 'is_reg' , true );
                $this->_context->set( 'location' , '成都' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'c' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_method_select' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , '3' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content . "<br />";
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_age' );

                $strategy = new Strategy( sprintf( self::TEXT_XML , '22' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                var_dump( $post_obj );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search_by_age' );
        }
}
