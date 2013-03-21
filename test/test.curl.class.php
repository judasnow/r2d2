<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require( dirname(__FILE__) . '/../lib/curl.class.php' );

class Test_of_curl extends UnitTestCase{

        public function setUp(){}

        public function _test_check_username() {
                $res = Curl::post( 'http://localhost:1979/action/WeixinMpApi.aspx' , array( 'action'=>'checkUserName' , 'username' => 'judas' ) );
                var_dump( $res );
        }

        public function _test_check_nickname() {
                $res = Curl::post( 'http://localhost:1979/action/WeixinMpApi.aspx' , array( 'action'=>'checkNickName' , 'nickname' => 'feisky007' ) );
                var_dump( $res );
        }

        public function test_down_img_from_url() {
                $res = Curl::download_file( 'http://img3.douban.com/icon/ul1314003-22.jpg' , rand() . '.jpg' );
                var_dump( $res );
        }

        public function _test_upload_img_file() {
                $res = Curl::post( 'http://localhost:1979/action/WeixinMpApi.aspx?action=uploadImg' , array( 'action'=>'uploadImg' , 'user_id'=>'534' , 'upload'=>'test.jpg' ));
                var_dump( $res );
        }
}

