<?php
/**
 * 寻找附近的人 也就是按地理位置进行查询
 */

require_once 'sub_search_circle_handler_base.class.php';
require_once 'location_circle_handler.class.php';

class Look_around_circle_handler extends Sub_search_circle_handler_base{

        private $_city_name;

        private $_response;

        public function __construct( $post_obj ) {
        //{{{
                parent::__construct( $post_obj );
                $this->_last_search_cond_json = $this->_context->get( 'last_search_cond' );
        }//}}}

        /**
         * 查找附近的人
         */
        private function _look_around( $use_last_search_cond = false ) {
        //{{{
                $res = $this->make_search_result( array( 'location' => $this->_city_name ) , $use_last_search_cond );
                if( $res[0] ) {
                        $this->_response = $res[1];
                } else {
                        //查询失败
                        $this->_response = $res[1];
                }
        }//}}}

        public function do_circle() {
        //{{{
                //判断是否为 n , 如果是的话 就不需要切换城市 只需要显示下一用户便可
                if( $this->_request_msg_type == 'text' ) {
                        if( $this->_request_content == 'n' && !empty( $this->_last_search_cond_json ) ) {
                                $this->_look_around( true );
                                return $this->_response;
                        }
                }

                //需要先陷入 location circle 获取最新的城市输入
                $location_circle_handler = new Location_circle_handler( $this->_post_obj );
                $res = $location_circle_handler->just_get_location();
                if( $res[0] == true ) {
                        $this->_city_name = $res[1];
                        $this->_look_around();

                        return $this->_response;
                } else {
                        //获取城市信息失败
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => $res[1] )
                        );
                        return $this->_response;
                }
        }//}}}
}
