<?php
/**
 * 持久化类 暂时使用 mongodb
 */
require_once 'third_party/Predis/Autoloader.php';
Predis\Autoloader::register();

class Store{

        private $_weixin_identity;
        private $_redis;

        const HASH_NAME = 'weixin_robot';
        const REDIS_SERVER = 'tcp://127.0.0.1:6379';

        public function __construct( $weixin_id ) {
                $this->_weixin_identity = $weixin_id ;
                $this->_redis = new Predis\Client( self::REDIS_SERVER );
        }

        public function __CALL( $method , $param_array ) {
                return call_user_func_array( array( $this->_redis , $method ) , $param_array );
        }
}
