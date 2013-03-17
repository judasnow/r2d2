<?php
/**
 * 对于上下文信息的测试
 */
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require( dirname(__FILE__) . '/../lib/context.class.php' );

class Test_of_context extends UnitTestCase {

        function setUp(){
                $this->_weixin_id = base64_encode( rand() );
                $this->_context = new Context( $this->_weixin_id );
        }

        function tearDown(){
                
        }

        //测试获取当前 circle 信息 未初始化
        function test_get_circle_info_about_now() {
                $this->_context->get( 'circle' );
        }
}

