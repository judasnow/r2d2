<?php
/**
 * 寻找附近的人 也就是按地理位置进行查询
 */

require_once 'sub_search_circle_handler_base.class.php';
require_once 'location_circle_handler.class.php';
require_once 'config.class.php';

class Look_around_circle_handler extends Sub_search_circle_handler_base{

        private $_city_name;

        private $_response;

        public function __construct( $post_obj ) {
        //{{{
                parent::__construct( $post_obj );
        }//}}}

        public function do_circle() {
        //{{{
                $last_search_cond = $this->_context->get( 'last_search_cond' );

                //判断是否为 n , 如果是的话 就不需要切换城市 只需要显示下一用户便可
                //但是只在 last_search_cond 不为空的情况下 才可用
                if( $this->_request_msg_type == 'text' ) {
                        if( $this->_request_content == 'n' && !empty( $last_search_cond ) ) {
                                $res = $this->make_search_result( array() , true );
                                $this->_response = $res[1];
                                return $this->_response;
                        }
                }

                //如果是 c 则需要进入查询选择模式 并显示相应的提示信息
                //前提条件是用户已经注册 
                if( $this->_request_content == 'c' ) {
                        if( $this->_is_reg == true ) {
                                //用户已经注册 可以进入查询选择模式
                                $this->_context->set( 'circle' , 'search_method_select' );
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        array( 'content' => Config::$response_msg['enter_search_method_selcet'] ) 
                                );
                                return $this->_response;
                        } else {
                                //用户还没有注册 提示之
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        array( 'content' => Config::$response_msg['enter_search_method_selcet'] ) 
                                );
                                return $this->_response;
                        }
                }

                //需要先陷入 location circle 获取最新的城市输入
                $location_circle_handler = new Location_circle_handler( $this->_post_obj );
                $res = $location_circle_handler->just_get_location();
                if( $res[0] == true ) {
                        $this->_city_name = $res[1];
                        $res = $this->make_search_result( array( 'location' => $this->_city_name ) );
                        if( $res[0] == true ) {
                                $this->_response = $res[1];
                                return $this->_response;
                        } else {
                                //查询失败
                                Debug::log( 'error.xml' , $res[1] );
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        array( 'content' => '查询失败了，等下再试试吧' )
                                );
                                return $this->_response;
                        }
                } else {
                        //获取城市信息失败
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                //此处不固定的原因是因为 错误的原因是不唯一的
                                array( 'content' => $res[1] )
                        );
                        return $this->_response;
                }
        }//}}}
}
