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

        //@todo 需要对各种信息在获取的时候进行一次检测
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

                //设置默认的查询对象性别
                if( $key == 'target_sex' && empty( $value ) ) {
                        $value = '女';
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

        public function incr( $key ) {
                 return $this->_store->HINCRBY( $this->_weixin_id , $key , 1 );
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

        /**
         * 获取全部用户注册信息
         */
        public function get_user_info() {
                $res = $this->_store->hmget( $this->_weixin_id , 'sex' , 'target_sex' , 'username' , 'nickname' , 'qq' , 'height' , 'weight' , 'zwms' );
                $user_info['sex'] = $res[0];
                $user_info['target_sex'] = $res[1];
                $user_info['username'] = $res[2];
                $user_info['nickname'] = $res[3];
                $user_info['qq'] = $res[4];
                $user_info['height'] = $res[5];
                $user_info['weight'] = $res[6];
                $user_info['zwms'] = $res[7];
                $user_info['email'] = $user_info['qq'] . '@qq.com';

                return $user_info;
        }
}
