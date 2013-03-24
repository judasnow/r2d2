<?php
require_once 'sub_search_circle_handler_base.class.php';
require_once 'config.class.php';

class Search_by_height_circle_handler extends Sub_search_circle_handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        private function _search_by_height( $just_next = false ) {
                $res = $this->make_search_result( array( 'location'=>$this->_location , 'target_sex'=>$this->_target_sex , 'height'=>$this->_height ) , $just_next );
                if( $res[0] ) {
                        $this->_response = $res[1];
                } else {
                        //@todo 查询失败的处理
                }
        }

        public function do_circle() {
                //判断是否为 n , 如果是的话 就不需要切换身高信息 只需要显示下一张便可
                if( $this->_request_msg_type == 'text' ) {
                        if( $this->_request_content == 'n' && !empty( $this->_search_result ) ) {
                                $this->_search_by_height( true );
                                return $this->_response;
                        }
                }

                $request_content = $this->_request_content;

                if( $request_content > 140 && $request_content < 250 ) {
                        //调用 api 使用身高 目标性别 以及当前的地址信息进行查询
                        //因为目前的需求是在注册之后才有这个功能 location 一定已经被设置了
                        //如果需获取城市信息的话 参考 look_around
                        $this->_location = $this->_context->get( 'location' );
                        $this->_target_sex = $this->_context->get( 'target_sex' );
                        $this->_height = $request_content;
                        $this->_search_by_height();

                        return $this->_response;
                } else {
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '请输入正确格式的身高' )
                        );
                        return $this->_response;
                }
        }
}

