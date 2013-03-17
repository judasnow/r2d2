<?php
$base_path = dirname(__FILE__);
require_once( $base_path . '/simpletest/autorun.php' );
require_once( $base_path . '/../lib/cache.class.php' );

class Test_of_cache extends UnitTestCase {

        function setUp(){}
        function tearDown(){
                $cache = new Cache();
                $cache->del( 'foo' );
                $cache->hdel( 'test' , 'foo' );
        }

        function test_sample_set_get() {
                $this->cache = new Cache();
                $this->assertTrue( $this->cache->set( 'foo' , 'bar' ) );
                $this->assertEqual( $this->cache->get( 'foo' ) , 'bar' );
        }

        function test_hset_hget(){
                $this->cache = new Cache();
                $this->assertTrue( $this->cache->hset( 'test' , 'foo' , 'bar' ) );
                $this->assertEqual( $this->cache->hget( 'test' , 'foo' ) , 'bar' );
        }
}

