<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require( dirname(__FILE__) . '/../lib/msg_produce.class.php' );

class Test_of_msg_produce extends UnitTestCase{

        public function setUp(){

        }
        
        public function test_produce_text_with_wrong_type() {
                $this->expectException();
                $msg_produce = new Msg_produce( 'hahaha' , array( 'content' => 123 ) );
        }

        public function test_produce_text_msg() {
                $msg_produce = new Msg_produce( 'text' , array( 'content' => 'hello' ) );
        }
}

