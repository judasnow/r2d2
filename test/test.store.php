<?php
$base_path = dirname(__FILE__);
require_once( $base_path . '/simpletest/autorun.php' );
require_once( $base_path . '/../lib/Store.class.php' );

class Test_of_store extends UnitTestCase {

        function setUp() {
                $this->_store = new Store( '123' );
        }
        function tearDown() {
                $this->_store->del( '123' );
        }

        function test_sample_set_get() {
                $this->_store->set( 'foo' , 'bar' );
        }
}

