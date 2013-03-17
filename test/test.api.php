<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require( dirname(__FILE__) . '/../lib/api.class.php' );

class Test_of_api extends UnitTestCase{

        public function setUp(){
                $this->_api = new Api();
        }

        //测试不存在的方法
        public function test_call_a_api_that_no_exists(){
                $res_json = $this->_api->fooBar( array( 'userName' => 'judas' ) );
                $this->assertTrue( json_decode( $res_json )->type == 'fail' );
        }

        public function test_check_username(){
                $res_json = $this->_api->checkUserName( array( 'userName' => 'judas' ) );
                $this->assertTrue( json_decode( $res_json )->type == 'success' );
        }

        public function test_check_nickname(){
                $res_json = $this->_api->checkNickName( array( 'nickName' => 'judas' ) );
                $this->assertTrue( json_decode( $res_json )->type == 'success' );
        }

        public function test_search(){
                //需要发送的是 地区以及年龄
                $age = 22;
                $city = '北京';
                $sex = '女';
                $res_json  = $this->_api->search( array( 'age' => $age , 'city' => $city , 'sex' => $sex ) );
                var_dump( count( json_decode( $res_json , true ) ) );
        }

        public function _test_do_reg(){
                $res_json = $this->_api->doReg( array( 'userName' => 'judas' ) );
                $this->assertTrue( json_decode( $res_json )->type == 'success' );
        }
}

