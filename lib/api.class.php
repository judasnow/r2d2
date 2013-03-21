<?php
/**
 * 调用 huaban123.com 的相应 api
 */
require_once "curl.class.php";

class Api{

        const API_URL = 'http://172.17.0.20:1979/action/WeixinMpApi.aspx';

        public function __call( $method , $arg_array ){
                //传递过来的这个参数很奇葩啊 ..
                $data = $arg_array[0];
                $data['action'] = $method;

                $res = Curl::post( self::API_URL , $data );

                return $res;
        }
}

