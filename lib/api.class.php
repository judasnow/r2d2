<?php
/**
 * 调用 huaban123.com 的相应 api
 */
require_once 'config.class.php';
require_once "curl.class.php";

class Api{

        public function __call( $method , $arg_array ){
                $api_url = Config::$huaban123_server . 'action/WeixinMpApi.aspx';

                //传递过来的这个参数很奇葩啊 ..
                $data = $arg_array[0];
                $data['action'] = $method;

                $res = Curl::post( $api_url , $data );

                return $res;
        }
}

