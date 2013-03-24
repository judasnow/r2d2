<?php
/**
 * 从制定的笑话集合中随机的返回一条笑话信息
 */
require_once 'store.class.php';
//require_once 'msg_producer.class.php';

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
         */
        public function rand_produce() {
                $joke = $this->_pick_a_text_joke();
                $joke_xml = $this->_msg_producer->do_produce( 
                                'text' ,
                                array( 'content' => $joke[0] )
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
