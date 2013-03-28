<?php
/**
 * 对于用户搜索结果的缓存
 */
require_once 'store.class.php';

class Search_result_cache {

        public function __construct() {
                $this->_cache_key = 'search_result_cache';
                $this->_store = new Store();
        }

        public function set( $cond_hash , $val ) {
                return $this->_store->hset( $this->_cache_key , $cond_hash , $val );
        }

        public function get( $cond_hash ) {
                return $this->_store->hget( $this->_cache_key , $cond_hash );
        }
}
