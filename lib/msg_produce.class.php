<?php
/**
 * 各种类型信息的生成器
 */
class Msg_produce {

        static private $valid_msg_types = array( 'text' , 'news' , 'music' );

        private $_msg_type;

        /**
         * 生成消息所需要的额外信息
         * 对于不同种类的消息 包含的具体内容也不相同
         */
        private $_msg_extra_info;

        public function __construct( $msg_type , $msg_extra_info ) {
                if( !in_array( $msg_type , self:: $valid_msg_types ) ) {
                        throw new Exception( 'try produce msg with invlalid type : ' . $msg_type );
                }
                $this->_msg_type = $msg_type;
                $this->_msg_extra_info = $msg_extra_info;
        }

        public function do_produce() {
                
        }
}
