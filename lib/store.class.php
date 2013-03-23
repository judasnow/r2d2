<?php
/**
 * 持久化类 暂时使用 mongodb
 */
require_once 'config.class.php';
require_once 'third_party/Predis/Autoloader.php';
Predis\Autoloader::register();

class Store{

        private $_redis;

        public function __construct() {
                $this->_redis = new Predis\Client( Config::$store_server );
        }

        public function __CALL( $method , $param_array ) {
                return call_user_func_array( array( $this->_redis , $method ) , $param_array );
        }
}
