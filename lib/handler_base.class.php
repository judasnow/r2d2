<?php
require_once 'api.class.php';
require_once 'context.class.php';
require_once 'msg_producer.class.php';
require_once 'joke_producer.class.php';

abstract class Handler_base {

        protected $_post_obj;

        protected $_api;

        protected $_context;

        //依赖于 $_post_obj
        protected $_request_content;
        protected $_request_msg_type;

        protected $_msg_producer;

        /**
         * @param $post_obj 用户输入 xml 解析而来的 object
         */
        public function __construct( $post_obj ){
                if( empty( $post_obj )) {
                        throw new Exception( 'try create a handler but post_obj is empty' );
                }

                $this->_post_obj = $post_obj;

                $this->_context = new Context( $post_obj->FromUserName );
                $this->_api = new Api();

                $this->_request_msg_type = $this->_post_obj->MsgType;
                $this->_request_content = Utility::format_user_input( $this->_post_obj->Content );

                $this->_msg_producer = new Msg_producer( 
                        array( 
                                'from'=>$this->_post_obj->ToUserName ,
                                'to'=>$this->_post_obj->FromUserName
                        )
                );
                $this->_joke_producer = new Joke_producer( $this->_msg_producer );
        }

        abstract public function do_circle();

}
