<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require_once( dirname(__FILE__) . '/../lib/bind_account_circle_handler.class.php' );
require_once( dirname(__FILE__) . '/../lib/strategy.class.php' );

class Test_of_bind_account extends UnitTestCase{

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

        //成功输入的情况
        public function _test_input_bd_will_inside_circle_link() {
                // circle common
                $this->_context->set( 'circle' , 'common' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'bd' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->Content == '请输入花瓣网已注册用户名：' );

                //输入用户名之后 断言会提示用户输入密码
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'username' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->Content == '请输入花瓣网登录密码（原花瓣网登录密码）：' );

                //输入密码成功之后 判断用户绑定是否成功
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'password' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                $username_for_bind = $this->_context->get( 'username_for_bind' );
                $password_for_bind = $this->_context->get( 'password_for_bind' );
                $this->assertTrue( $username_for_bind == 'username' );
                $this->assertTrue( $password_for_bind == 'password' );
        }

        //测试输入非 text情况
        public function _test_bind_fail_with_no_text_msg(){
                $this->_context->set( 'circle' , 'common' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'bd' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->Content == '请输入花瓣网已注册用户名：' );

                //输入用户名之后 断言会提示用户输入密码
                $strategy = new Strategy( sprintf( self::IMAGE_XML ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->Content == '你输入的可不是用户名哦，请输入花瓣网已注册用户名：' );

        }

        //测试绑定失败的情况
        public function _test_bind_fail(){
                $this->_context->set( 'circle' , 'common' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'bd' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->Content == '请输入花瓣网已注册用户名：' );

                //输入用户名之后 断言会提示用户输入密码
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'username' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->Content == '请输入花瓣网登录密码（原花瓣网登录密码）：' );

                //输入密码成功之后 判断用户绑定是否成功
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'password' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
        }

        //测试成功绑定
        public function test_bind_ok(){
                $this->_context->set( 'circle' , 'common' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'bd' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->Content == '请输入花瓣网已注册用户名：' );

                //输入用户名之后 断言会提示用户输入密码
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'uuuutest' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->Content == '请输入花瓣网登录密码（原花瓣网登录密码）：' );

                //输入密码成功之后 判断用户绑定是否成功
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'huaban123' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                $is_reg = $this->_context->get( 'is_reg' );
                $this->assertTrue( $is_reg == true );

                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );

                echo $circle = $this->_context->get( 'circle' );

                echo $post_obj->Content;
        }
}
