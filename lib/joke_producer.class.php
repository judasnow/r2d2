<?php
/**
 * 从制定的笑话集合中随机的返回一条笑话信息
 */
require_once 'store.class.php';
require_once 'msg_producer.class.php';
require_once 'debug.class.php';

class Joke_producer {

        private $_store;

        private $_msg_producer;

        public function __construct( $msg_producer ) {

                $this->_store = new Store();
                $this->_msg_producer = $msg_producer;
        }

        /**
         * 随机产生一个笑话
         * 目前只能返回 text 类型的笑话
         *
         * @param $extra_info 笑话之外的信息 比如用户未注册的时候就提示用户
         */
        public function rand_produce( $extra_info ) {
                $joke = $this->_pick_a_text_joke();
                if( !empty( $joke[0] ) ) {
                        $joke_content = $joke[0];
                } else {
                        Debug::log( 'error.xml' , 'joke pool is empty.' );
                        $joke_content = '';
                }
                $joke_xml = $this->_msg_producer->do_produce(
                        'text' ,
                        array( 'content' => $joke_content . $extra_info )
                );
                return $joke_xml;
        }

        /**
         * 添加一个笑话
         */
        public function add() {
                //SADD
        }

        private function _pick_a_text_joke() {
                return $this->_store->SRANDMEMBER( 'joke:text' , 1 );
        }
}
