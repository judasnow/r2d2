<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require( dirname(__FILE__) . '/../lib/joke_producer.class.php' );

class Test_of_msg_produce extends UnitTestCase{

        public function setUp(){

        }
        
        public function test_produce_text_msg() {
                $joke_producer = new Joke_producer();
        }
}


