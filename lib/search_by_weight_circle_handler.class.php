<?php
require_once 'sub_search_circle_handler_base.class.php';
require_once 'config.class.php';

class Search_by_weight_circle_handler extends Sub_search_circle_handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        private function _search_by_height( $just_next = false ) {
                $res = $this->make_search_result( array( 'location'=>$this->_location , 'target_sex'=>$this->_target_sex , 'weight'=>$this->_weight ) , $just_next );
                if( $res[0] ) {
                        $this->_response = $res[1];
                } else {
                        //查询失败
                }
        }

        public function do_circle() {
                //判断是否为 n , 如果是的话 就不需要切换weight信息 只需要显示下一张便可
                if( $this->_request_msg_type == 'text' ) {
                        if( $this->_request_content == 'n' && !empty( $this->_search_result ) ) {
                                $this->_search_by_height( true );
                                return $this->_response;
                        }
                }

                $request_content = $this->_request_content;

                if( $request_content > 50 && $request_content < 200 ) {
                        //@see search_by_height_circle_handler
                        $this->_location = $this->_context->get( 'location' );
                        $this->_target_sex = $this->_context->get( 'target_sex' );
                        $this->_weight = $request_content;
                        $this->_search_by_height();

                        return $this->_response;
                } else {
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '请输入正确格式的体重' )
                        );
                        return $this->_response;
                }
        }
}


