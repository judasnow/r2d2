<?php
$base_path = dirname(__FILE__);
require_once( $base_path . '/simpletest/autorun.php' );
require_once( $base_path . '/../lib/Store.class.php' );

class Test_of_store extends UnitTestCase {

        function setUp(){
        
        }
        function tearDown(){
        }

        function test_sample_set_get() {
                $store = new Store( '123' );
        }
}

