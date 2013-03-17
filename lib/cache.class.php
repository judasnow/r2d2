<?php
/**
 * 缓存类 暂时使用 redis
 */
require_once 'third_party/Predis/Autoloader.php';

class Cache{
        private $_redis;

        const HASH_NAME = 'weixin_robot_context';
        const REDIS_SERVER = 'tcp://172.17.0.46:6379';

        public function __construct(){
                Predis\Autoloader::register();
                $this->_redis = new Predis\Client( self::REDIS_SERVER );
        }

        public function __CALL( $method , $param_array ){
                return call_user_func_array( array( $this->_redis , $method ) , $param_array );
        }
}
