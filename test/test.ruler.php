<?php
$base_path = dirname(__FILE__);
require_once( $base_path . '/simpletest/autorun.php' );
require_once( $base_path . '/../lib/ruler.class.php' );

class Test_of_ruler extends UnitTestCase {

        function setUp(){

        }
        function tearDown(){

        }

        function test_load(){
                $ruler = new Ruler();
                //测试读取指定的规则配置信息
                $ruler->load( 'common' );
                echo "<pre>";
                foreach( $ruler as $no => $item ){
                        var_dump( $item );
                }
        }
}

