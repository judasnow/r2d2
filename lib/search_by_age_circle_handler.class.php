<?php
/**
 * 按年龄进行查询
 */
require_once 'sub_search_circle_handler_base.class.php';
require_once 'config.class.php';

class Search_by_age_circle_handler extends Sub_search_circle_handler_base {

        public function __construct( $post_obj ) {
        //{{{
                parent::__construct( $post_obj );
        }//}}}

        public function do_circle() {
                $last_search_cond = $this->_context->get( 'last_search_cond' );

                //判断是否为 n , 如果是的话 就不需要切换查询条件 只需要显示下一用户便可
                //但是只在 last_search_cond 不为空的情况下 才可用
                if( $this->_request_msg_type == 'text' ) {
                        if( $this->_request_content == 'n' && !empty( $last_search_cond ) ) {
                                $res = $this->make_search_result( array() , true );
                                $this->_response = $res[1];
                                return $this->_response;
                        }
                }

                $age = $this->_request_content;
                if( $age >= 18 && $age <= 60 ) {
                        $res =$this->make_search_result( array( 'age'=>$age ) );
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
                        //年龄格式错误
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '年龄输入不合法，请输入18-60范围内的数字' )
                        );
                        return $this->_response;
                }
        }
}


