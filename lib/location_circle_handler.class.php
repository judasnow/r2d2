<?php
require_once 'handler_base.class.php';

class Location_circle_handler extends Handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        /**
         * 判断是否可能包含地址信息
         */
        public function is_possible_location() {
        //{{{
                $request_content = $this->_request_content;
                return ( $this->_request_msg_type == 'location' ) || ( preg_match( '/[\x{4e00}-\x{9fa5}]{1,5}/u' , $request_content ) && Utility::valid_city( $request_content ) );
        }//}}}

        /**
         * 如果需要的话(为空) 设置用户的 location 信息
         */
        private function _set_user_location() {
        //{{{
                //判断用户信息中的 Location 是否已经被设置
                //若 没有被设置的话 第一次输入的地址信息就会被当作该用户的 location
                $location = $this->_context->get( 'location' );

                if( empty( $location ) ) {
                        $this->_context->set( 'location' , $this->_city_name );
                }
        }//}}}

        /**
         * 不是循环处理 仅仅只是根据用户输入获取 location
         * 注意 仅仅只在 location 为空时 才设置之
         * @return 设置成功则返回 array( true , $this->_city_name )
         *         错误的话就返回 array( false , 相应的错误信息 )
         */
        public function just_get_location() {
                if( !$this->is_possible_location() ) {
                        //格式不对( text 类型的话 ) 或者根本不是 location 类型的
                        return array( false , '请输入正确的二级城市名称，或直接发送地址信息' );
                }

                //如果发送的是 location 类型的信息
                if( $this->_request_msg_type == 'location' ) {
                        $label = $this->_post_obj->Label;
                        $x = $this->_post_obj->Location_x;
                        $y = $this->_post_obj->Location_y;

                        //注意其不为empty 而为 0
                        //@todo 返回的对象很奇葩 是一个数组
                        if( empty( $label[0] ) ) {
                                //label 中没有信息
                                return array( false , '额，发送的地址信息里面没有中文标签，请手动输入地级市的名称。' );
                        } else {
                                //从 label 中取出 地址信息
                                $this->_city_name = preg_split( '/省|区|市/u' , $label )[1];
                                $this->_set_user_location();

                                return array( true , $this->_city_name );
                        }
                }

                //如果直接发送的地级市的名称
                //因为进入本循环之已经经过了有效性的验证
                //所以直接使用便可
                if( $this->_request_msg_type == 'text' ) {
                        $this->_city_name = $this->_request_content;
                        $this->_set_user_location();

                        return array( true , $this->_city_name );
                }
        }

        public function do_circle() {
        //{{{
                
        }//}}}
}
