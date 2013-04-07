<?php
require_once 'handler_base.class.php';
require_once 'config.class.php';

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
         * 通过 baidu 地图 api 获取地理位置
         */
        private function _get_label_by_xy( $x , $y ) {
        //{{{
                $url = 'http://api.map.baidu.com/geocoder?';
                $data = array(
                        'location' => $x . ',' . $y ,
                        'output' => 'json' ,
                        'key' => '134e824172278240f60e47bbe3dd6c24'
                );
                $res_json = Curl::get( $url , $data );

                $res = json_decode( $res_json , true );
                if( $res['status'] == 'OK' ) {
                        $address = $res['result']['addressComponent'];
                }

                if( !empty( $address ) ) {
                        return $address;
                } else {
                        return false;
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
                        return array( false , Language_config::$location_input_valid );
                }

                //如果发送的是 location 类型的信息
                if( $this->_request_msg_type == 'location' ) {
                        $label = $this->_post_obj->Label;
                        $x = $this->_post_obj->Location_X;
                        $y = $this->_post_obj->Location_Y;

                        $address = $this->_get_label_by_xy( $x , $y );
                        if( empty( $address ) ) {
                                //api 失败了
                                return array( false , Language_config::$input_invalid_message['location_fetch_label_fail'] );
                        } else {
                                //为了和手动输入的统一
                                //删除最右的单位 市|州 等
                                //因为市肯定市多余的 先删除 市
                                $city_name = $address['city'];
                                $city_name = Utility::valid_city( $city_name );
                                if( $city_name != false ) {
                                        return array( true , $city_name );
                                }

                        }
                }

                //如果直接发送的地级市的名称
                //因为进入本循环之已经经过了有效性的验证
                //所以直接使用便可
                if( $this->_request_msg_type == 'text' ) {
                        $this->_city_name =  Utility::valid_city( $this->_request_content );
                        return array( true , $this->_city_name );
                }
        }

        public function do_circle() {

        }
}
