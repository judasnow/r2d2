<?php
/**
 * 定义保存当前用户上下文信息的对象
 * 每一个用户对应一个 hash key
 */
require_once( 'store.class.php' );

class Context {

        //用户 weixin 帐号
        private $_weixin_id;

        private $_store;

        public function __construct( $weixin_id ) {
                $this->_weixin_id = $weixin_id;
                $this->_store = new Store( $weixin_id );
        }

        public function get( $key ) {
                return $this->_store->hget( $this->_weixin_id , $key );
        }

        public function set( $key , $value ) {
                return $this->_store->hset( $this->_weixin_id , $key , $value );
        }

        public function del( $key ) {
                return $this->_store->del( $key );
        }
}
