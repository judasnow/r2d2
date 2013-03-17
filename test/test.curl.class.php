<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require( dirname(__FILE__) . '/../curl.class.php' );

class Test_of_curl extends UnitTestCase{

        public function setUp(){}

        public function test_check_username(){
                $res = Curl::post( 'http://localhost:1979/action/WeixinMpApi.aspx' , array( 'action'=>'checkUserName' , 'username' => 'judas' ) );
                var_dump( $res );
        }

        public function test_check_nickname(){
                $res = Curl::post( 'http://localhost:1979/action/WeixinMpApi.aspx' , array( 'action'=>'checkNickName' , 'nickname' => 'feisky007' ) );
                var_dump( $res );
        }
}

