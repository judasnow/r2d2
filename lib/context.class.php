<?php
/**
 * 定义保存当前用户上下文信息的对象
 * 每一个用户对应一个 hash key
 */
require_once( 'cache.class.php' );

class Context {

        //用户 weixin 帐号
        private $_weixin_id;

        private $_cache;

        public function __construct( $weixin_id ) {
                $this->_weixin_id = $weixin_id;
                $this->_cache = new Cache();
        }

        public function get( $key ) {
                return $this->_cache->hget( $this->_weixin_id , $key );
        }

        public function set( $key , $value ) {
                if( $key == 'ignore_ruler' ) {
                        return $this->set_ignore_ruler( $value );
                } else {
                        return $this->_cache->hset( $this->_weixin_id , $key , $value );
                }
        }

        private function set_ignore_ruler( $value ) {
                $old_ignore_ruler = $this->_cache->hget( $this->_weixin_id , 'ignore_ruler' );
                if( empty( $old_ignore_ruler ) ) {
                        $new_ignore_ruler = $value;
                } else {
                        $new_ignore_ruler = $old_ignore_ruler . $value;
                }
                return $this->_cache->hset( $this->_weixin_id , 'ignore_ruler' , $value );
//                $this->_cache->eval( "
//                        local new_ignore_ruler;
//                        local old_ignore_ruler = redis.call( 'hget' , ARGV[1] , ARGV[2] );
//                        if ( old_ignore_ruler == false ) then
//                                new_ignore_ruler = ARGV[3]
//                        else
//                                new_ignore_ruler = old_ignore_ruler .. ',' .. ARGV[3];
//                        end
//                        redis.call( 'hset' , ARGV[1] , ARGV[2] , new_ignore_ruler );
//                " , sprintf( "1 %s 2 %s 3 %s" , $this->_weixin_id , 'ignore_ruler' , $value ) );
        }
}
