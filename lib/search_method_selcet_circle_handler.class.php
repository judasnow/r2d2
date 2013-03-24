<?php
require_once 'handler_base.class.php';
require_once 'search_by_height_circle_handler.class.php';

class Search_method_selcet_circle_handler extends Handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        public function do_circle() {
                $request_content = $this->_request_content;

                if( $request_content == 's' ) {
                        //提示用户输入身高信息
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '请按照提示输入相应的数字： 1 按身高查询。2 按体重查询。3。按年龄查询' )
                        );
                        return $this->_response;
                }

                // 1 身高 2 体重 3 年龄
                if( $request_content == '1' ) {
                        //进入身高查询流程
                        $this->_context->set( 'circle' , 'search_by_height' );

                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '请输入身高信息:' )
                        );
                        return $this->_response;
                }

                if( $request_content == '2' ) {
                        //按体重进行查询
                        $this->_context->set( 'circle' , 'search_by_weight' );

                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '请输入体重信息:' )
                        );
                        return $this->_response;
                }

                if( $request_content == '3' ) {
                        //按年龄进行查询
                        $this->_context->set( 'circle' , 'search_by_age' );

                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '请输入年龄信息:' )
                        );
                        return $this->_response;
                }
        }
}
