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
                if( $key == 'last_circle' ) {
                        $value = $this->_get_last_circle();
                } else {
                        $value = $this->_store->hget( $this->_weixin_id , $key );
                        if( $key == 'circle' ) {
                                if( empty( $value ) ) {
                                        $value = 'common';
                                }
                        }
                }
                return $value;
        }

        public function set( $key , $value , $is_from_last_circle = false ) {
                if( $key == 'circle' ) {
                        $last_circle = $this->get( 'circle' );

                        if( empty( $last_circle ) ) {
                                $last_circle = 'common';
                        }

                        //目前想到的是两种情况 不需要 push 
                        //1 当前需要更改的和上一个 circle 一样
                        //2 本来就是从 last_circle 恢复的
                        if ( $last_circle != $value && $is_from_last_circle != true ){
                                $this->_store->RPUSH( "{$this->_weixin_id}:circle_stack" , $last_circle );
                        }
                }
                return $this->_store->hset( $this->_weixin_id , $key , $value );
        }

        public function del( $key ) {
                return $this->_store->del( $key );
        }

        //退出当前循环
        public function exit_current_circle() {
        //{{{
                $circle = $this->get( 'circle' );
                if( $circle == 'common' ) {
                        //当前处于最外层循环
                        return false;
                } else {
                        $last_circle = $this->get( 'last_circle' );

                        //注意这里是从 last_circle 恢复的 因此不需要 push 操作 加个 true 参数
                        $this->set( 'circle' , $last_circle , true );
                        return true;
                }
        }//}}}

        /**
         * 获取上一个 circle 只要从 stack 中 RPOP 一个元素便可
         */
        private function _get_last_circle() {
                $last_circle = $this->_store->RPOP( "{$this->_weixin_id}:circle_stack" );

                if( empty( $last_circle ) ) {
                        $last_circle = 'common';
                }

                return $last_circle;
        }
}
