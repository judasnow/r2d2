<?php
require_once 'handler_base.class.php';

class Common_circle_handler extends Handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        public function do_circle() {
                //非法输入 或者无任何匹配信息 返回一条笑话
                $this->_response = $this->_joke_producer->rand_produce();
                return $this->_response;
        }
}
