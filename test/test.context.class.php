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
                $this->_context->del( $this->_weixin_id );
                $this->_context->del( $this->_weixin_id . ':' . 'circle_stack' );
        }

        //测试获取当前 circle 信息 未初始化
        function _test_get_circle_info_about_now() {
                $circle = $this->_context->get( 'circle' );
                //断言 默认 为 common 
                $this->assertTrue( $circle == 'common' );
        }

        //测试未 初始化获取last_circle 的情况
        function _test_get_last_circle() {
                $last_circle = $this->_context->get( 'last_circle' );
                $this->assertTrue( $last_circle == 'common' );
        }

        //测试由 common -> reg 之后 last_circle 的情况 
        //断言应该为 common
        public function _test_get_last_circle_when_circle_from_common_to_reg() {
                $this->_context->set( 'circle' , 'common' );
                $this->_context->set( 'circle' , 'reg' );

                $last_circle = $this->_context->get( 'last_circle' );
                $this->assertTrue( $last_circle == 'common' );
        }

        //测试四层的情况
        public function test_4_level() {
                $this->_context->set( 'circle' , 'common' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'common' );

                $this->_context->set( 'circle' , 'reg' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );
                $last_circle = $this->_context->get( 'last_circle' );
                $this->assertTrue( $last_circle == 'common' );

                $this->_context->set( 'circle' , 'uploading_image' );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'uploading_image' );
                $last_circle = $this->_context->get( 'last_circle' );
                $this->assertTrue( $last_circle == 'reg' );
        }

        //测试多层退出时的情况 对于超过 stack 大小的 pop 操作 断言
        //永远返回 common
        public function test_pop_times_gt_then_stack_size() {
                $this->_context->set( 'circle' , 'common' );
                $this->_context->set( 'circle' , 'reg' );
                $this->_context->set( 'circle' , 'uploading_image' );
                $this->_context->set( 'circle' , 'search' );
                $this->_context->set( 'circle' , 'search_by_height' );

                $last_circle = $this->_context->get( 'last_circle' );
                $this->assertTrue( $last_circle == 'search' );
                $last_circle = $this->_context->get( 'last_circle' );
                $this->assertTrue( $last_circle == 'uploading_image' );
                $last_circle = $this->_context->get( 'last_circle' );
                $this->assertTrue( $last_circle == 'reg' );
                $last_circle = $this->_context->get( 'last_circle' );
                $this->assertTrue( $last_circle == 'common' );

                //超过 stack 大小了
                $last_circle = $this->_context->get( 'last_circle' );
                $this->assertTrue( $last_circle == 'common' );
        }

        //测试特殊的循环 common -> location -> reg -> location ...
        public function test_reg_location() {
                $this->_context->set( 'circle' , 'common' );
                $this->_context->set( 'circle' , 'location' );
                $this->_context->set( 'circle' , 'reg' );

                $last_circle = $this->_context->get( 'last_circle' );
                $this->assertTrue( $last_circle == 'location' );
                $last_circle = $this->_context->get( 'last_circle' );
                $this->assertTrue( $last_circle == 'common' );
        }
}

