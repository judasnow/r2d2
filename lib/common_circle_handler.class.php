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

                //未注册的情况之下，输入 bd 会进入 绑定帐号流程 
                //会尝试将 web 端注册的帐号绑定到本地
                if( $this->_request_content == 'bd' ) {
                        if( $is_reg == true ) {
                                //已经注册的用户不能进行这个操作
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' , 
                                        //提示用户注册之后不能进行绑定操作
                                        array( 'content' => Language_config::$enter_bind_circle_with_reg )
                                );
                                return $this->_response;
                        } else {
                                //进入 bind circle
                                //清空之前的输入
                                $this->_context->set( 'username_for_bind' , '' );
                                $this->_context->set( 'password_for_bind' , '' );

                                $this->_context->set( 'circle' , 'bind' );
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        //提示用户输入用户名
                                        array( 'content' => Language_config::$enter_bind_circle )
                                );
                                return $this->_response;
                        }
                }

                //在已经注册的情况下，输入 sczp 可以进行照片上传
                if( $this->_request_content == 'sczp' ) {
                        if( $is_reg == true ) {
                                if( $circle != 'upload_image' ) {
                                        $this->_context->set( 'circle' , 'upload_image' );
                                        $this->_response = $this->_msg_producer->do_produce(
                                                'text' ,
                                                //@todo 显示用户已经上传照片的数量
                                                array( 'content' => Language_config::$enter_upload_image_circle )
                                        );
                                        return $this->_response;
                                }
                        } else {
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        //@todo 显示用户已经上传照片的数量
                                        array( 'content' => Language_config::$enter_upload_image_circle_without_reg )
                                );
                                return $this->_response;
                        }
                }

                //输入 s 进入 search_method_selcet 流程 
                //同样必须注册之后执行这个操作
                if( $this->_request_content == 'c' ) {
                        if( $is_reg == true ) {
                                $search_count = $this->_context->get( 'search_count' );
                                if( $search_count >= Search_config::$max_search_count_with_reg ) {
                                        $this->_response =  $this->_msg_producer->do_produce(
                                                'text' ,
                                                array(
                                                        'content' => sprintf( Language_config::$search_count_outrange_after_reg , Language_config::$max_search_count_with_reg )
                                                )
                                        );
                                        return $this->_response;
                                }

                                if( $circle != 'search_method_selcet' ) {
                                        $this->_context->set( 'circle' , 'search_method_select' );
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' ,
                                                array( 'content' => Language_config::$enter_search_method_selcet )
                                        );
                                        return $this->_response;
                                }
                        } else {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => Language_config::$enter_search_method_selcet_without_reg )
                                );
                                return $this->_response;
                        }
                }

                //非法输入 或者无任何匹配信息 返回一条笑话
                //但是笑话的内容需要按用户是否已经注册而不同
                if( $is_reg == true ) {
                        $extra_info = Language_config::$joke_extra_info_after_reg;
                } else {
                        $extra_info = Language_config::$joke_extra_info_before_reg;
                }
                $this->_response = $this->_joke_producer->rand_produce( $extra_info );
                return $this->_response;
        }
}
