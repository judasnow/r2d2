<?php
require_once 'sub_search_circle_handler_base.class.php';
require_once 'config.class.php';

class Search_by_age_circle_handler extends Sub_search_circle_handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        private function _search_by_age() {

                $cond = array( 'location'=>$this->_location , 'target_sex'=>$this->_target_sex , 'age'=>$this->_age );

                $res = $this->make_search_result( $cond );

                if( $res[0] ) {
                        $this->_response = $res[1];
                } else {
                        //@todo 查询失败的处理 直接返回结果
                        $this->_response = $res[1];
                }
        }

        public function do_circle() {
                $request_content = $this->_request_content;

                //判断是否为 n , 如果是的话 就不需要切换身高信息 只需要显示下一张便可
                if( $request_content == 'n' ) {
                        $this->_search_by_age();
                        return $this->_response;
                }

                if( $request_content > 18 && $request_content < 60 ) {
                        $this->_location = $this->_context->get( 'location' );
                        $this->_target_sex = $this->_context->get( 'target_sex' );
                        $this->_age = $request_content;
                        $this->_search_by_age();

                        return $this->_response;
                } else {
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '请输入正确格式的年龄' )
                        );
                        return $this->_response;
                }
        }
}


