<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require( dirname(__FILE__) . '/../lib/msg_producer.class.php' );

class Test_of_msg_produce extends UnitTestCase{

        public function setUp(){

        }
        
        public function _test_produce_text_msg() {
                $msg_producer = new Msg_producer( array( 'from'=>'f' , 'to'=>'t' ) );
                $post_obj = simplexml_load_string( $msg_producer->do_produce( 'text' , array( 'content' => 'hahaha' ) ) , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $post_obj->ToUserName == 't'  );
                $this->assertTrue( $post_obj->FromUserName == 'f'  );
        }

        public function _test_produce_news_msg_with_one_item() {
                //测试一个 item 的情a
                $msg_producer = new Msg_producer( array( 'from'=>'f' , 'to'=>'t' ) );
                $items = array( array( 'title'=>'titleee' , 'description'=>'desc' , 'pic_url'=>'pic' , 'url'=>'url' ) );
                $obj = simplexml_load_string( $msg_producer->do_produce( 'news' , array( 'items' => $items ) ) , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $obj->ArticleCount == 1 );
        }

        public function _test_produce_news_msg_with_3_item() {
                //测试一个 item 的情况
                $msg_producer = new Msg_producer( array( 'from'=>'f' , 'to'=>'t' ) );
                $items = array( 
                        array( 'title'=>'titleee' , 'description'=>'desc' , 'pic_url'=>'pic' , 'url'=>'url' ),
                        array( 'title'=>'titleee' , 'description'=>'desc' , 'pic_url'=>'pic' , 'url'=>'url' ),
                        array( 'title'=>'titleee' , 'description'=>'desc' , 'pic_url'=>'pic' , 'url'=>'url' )
                );
                $obj = simplexml_load_string( $msg_producer->do_produce( 'news' , array( 'items' => $items ) ) , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $obj->ArticleCount == 3 );
        }

        //测试音乐信息
        public function test_produce_music_msg() {
                $msg_producer = new Msg_producer( array( 'from'=>'f' , 'to'=>'t' ) );
                $msg_producer->do_produce( 'music' , array( 'title' => 'title' , 'description'=>'desc' , 'music_url'=>'123' ) );
        }
}

