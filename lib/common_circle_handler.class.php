<?php
require_once 'handler_base.class.php';
require_once 'config.class.php';

class Common_circle_handler extends Handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        public function do_circle() {

                $is_reg = $this->_context->get( 'is_reg' );
                $circle = $this->_context->get( 'circle' );

                //在已经注册的情况下，输入 sczp 可以进行照片上传
                if( $this->_request_content == 'sczp' ) {
                        if( $is_reg == true ) {
                                if( $circle != 'upload_image' ) {
                                        $this->_context->set( 'circle' , 'upload_image' );
                                        $this->_response = $this->_msg_producer->do_produce(
                                                'text' ,
                                                //@todo 显示用户已经上传照片的数量
                                                array( 'content' => Config::$response_msg['enter_upload_image_circle'] )
                                        );
                                        return $this->_response;
                                }
                        } else {
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        //@todo 显示用户已经上传照片的数量
                                        array( 'content' => Config::$response_msg['enter_upload_image_circle_without_reg'] )
                                );
                                return $this->_response;
                        }
                }

                //输入 s 进入 search_method_selcet 流程 
                //同样必须注册之后执行这个操作
                if( $this->_request_content == 'c' ) {
                        if( $is_reg == true ) {
                                $search_count = $this->_context->get( 'search_count' );
                                if( $search_count >= Config::$max_search_count_with_reg ) {
                                        $this->_response =  $this->_msg_producer->do_produce( 
                                                'text' ,
                                                array( 'content' => sprintf( Config::$response_msg['search_count_outrange_after_reg'] , Config::$max_search_count_with_reg ) )
                                        );
                                        return $this->_response;
                                }

                                if( $circle != 'search_method_selcet' ) {
                                        $this->_context->set( 'circle' , 'search_method_select' );
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' ,
                                                array( 'content' => Config::$response_msg['enter_search_method_selcet'] )
                                        );
                                        return $this->_response;
                                }
                        } else {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => Config::$response_msg['enter_search_method_selcet_without_reg'] )
                                );
                                return $this->_response;
                        }
                }

                //非法输入 或者无任何匹配信息 返回一条笑话
                //但是笑话的内容需要按用户是否已经注册而不同
                if( $is_reg == true ) {
                        $extra_info = Config::$response_msg['joke_extra_info_after_reg'];
                } else {
                        $extra_info = Config::$response_msg['joke_extra_info_before_reg'];
                }
                $this->_response = $this->_joke_producer->rand_produce( $extra_info );
                return $this->_response;
        }
}
