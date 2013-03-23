<?php
require_once 'handler_base.class.php';

class Common_circle_handler extends Handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        public function do_circle() {
                //输入 location 类型的消息 或者输入的是有效的二级地址的名称
                //则进入到查询附近的人 look around circle
                $location_circle_handler = new Location_circle_handler( $this->_post_obj );
                if( $location_circle_handler->is_possible_location() ) {
                        if( $circle != 'look_around' ) {
                                $this->_context->set( 'circle' , 'look_around' );
                                $this->make_res();
                        }
                }
        }
}
