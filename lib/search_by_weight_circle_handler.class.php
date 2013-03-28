<?php
require_once 'sub_search_circle_handler_base.class.php';
require_once 'config.class.php';

class Search_by_weight_circle_handler extends Sub_search_circle_handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        private function _search_by_weight() {

                $cond = array( 'weight'=>$this->_weight );

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
                        $this->_search_by_weight();
                        return $this->_response;
                }

                if( $request_content > 70 && $request_content < 230 ) {
                        $this->_weight = $request_content;
                        $this->_search_by_weight();

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


