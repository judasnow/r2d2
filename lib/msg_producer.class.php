<?php
/**
 * 各种类型信息的生成器
 */
require_once( 'xml_tpl.class.php' );

class Msg_producer {

        static private $_valid_msg_types = array( 'text' , 'news' , 'music' );

        /**
         * 临时存放构造中的结果
         */
        private $_msg_produceing;

        /**
         * 最终返回的信息 xml 格式
         */
        private $_msg_result;

        /**
         * $base_info 指代的就是 from , to 对于任何一种
         */
        public function __construct( $base_info = array() ) {
        //{{{
                if( empty( $base_info['from'] ) || empty( $base_info['to'] ) ) {
                        throw new Exception( 'not set "from" or "to" correct.' );
                }

                $from = $base_info['from'];
                $to = $base_info['to'];

                $this->_msg_produceing['from'] = $from;
                $this->_msg_produceing['to'] = $to;
        }//}}}

        /**
         * 其中 $msg_extra_info 是构造当前消息所需要的额外信息
         */
        public function do_produce( $msg_type , $msg_extra_info ) {
        //{{{
                if( !in_array( $msg_type , self::$_valid_msg_types ) ) {
                        throw new Exception( 'try produce a message with invalid type:' . $msg_type );
                }

                //给 msg 添加基本的信息
                $this->_msg_produceing['time'] = $_SERVER['REQUEST_TIME'];

                $this->_msg_extra_info = $msg_extra_info;

                //按类型进行特殊处理
                $this->{"_do_produce_{$msg_type}_msg"}();

                return $this->_msg_result;
        }//}}}

        /**
         * 绑定 3 个基本的信息元素 to from time 并返回一个统一的数组
         * 便于 explore 出这 3 个变量 其实是为了缩减重复代码
         */
        private function _three_base_info() {
        //{{{
                $to = $this->_msg_produceing['to'];
                $from = $this->_msg_produceing['from'];
                $time = $this->_msg_produceing['time'];

                return array( 'to'=>$to , 'from'=>$from , 'time'=>$time );
        }//}}}

        /**
         * 生成 text 类型的消息
         */
        private function _do_produce_text_msg() {
        //{{{
                if( empty( $this->_msg_extra_info['content'] ) ) {
                        throw new Exception( 'msg_type is text but msg_info["content"] is empty.' );
                }

                extract( $this->_three_base_info() );
                $content = $this->_msg_extra_info['content'];

                //将数据补充完整
                $this->_msg_result = sprintf(
                        Xml_tpl::$text_xml_tpl ,
                        $to , 
                        $from , 
                        $time , 
                        $content 
                );
        }//}}}

        /**
         * 生成 items 对应的 xml
         */
        private function _do_produce_items_of_news_msg( $items ) {
        //{{{
                $items_xml_produceing = '';
                foreach( $items as $no=>$item ) {
                        if( empty( $item['title'] ) ||
                                empty( $item['description'] ) || 
                                empty( $item['pic_url'] ) || empty( $item['url'] ) ) {

                                throw new Exception( 'item info is invalid.' );
                        }
                        $items_xml_produceing .= sprintf( 
                                Xml_tpl::$news_item_tpl ,
                                $item['title'] ,
                                $item['description'] ,
                                $item['pic_url'] ,
                                $item['url']
                        );
                }

                return $items_xml_produceing;
        }//}}}

        /**
         * 生成 news 类型的消息
         */
        private function _do_produce_news_msg() {
        //{{{
                //items 必须不能是空 至少都需要有一条消息
                //而且需要是一个 二位数组
                $msg_extra_info = $this->_msg_extra_info;
                if( empty( $msg_extra_info['items'] ) || !is_array( $msg_extra_info['items'][0] ) ) {
                        throw new Exception( 'msg_type is news but items are empty.' );
                }

                $items = $msg_extra_info['items'];

                extract( $this->_three_base_info() );

                //渲染 items 
                $items_xml_result = $this->_do_produce_items_of_news_msg( $items );

                //渲染 news 
                $this->_msg_result = sprintf( 
                        Xml_tpl::$news_xml_tpl ,
                        $to,
                        $from,
                        $time,
                        count( $items ),
                        $items_xml_result
                );
        }//}}}

        /**
         * 生成 music 类型的消息
         */
        private function _do_produce_music_msg() {
        //{{{
                $msg_extra_info = $this->_msg_extra_info;
                if( empty( $msg_extra_info['title'] ) || 
                        empty( $msg_extra_info['description'] ) || 
                        empty( $msg_extra_info['music_url'] ) ) {

                        throw new Exception( 'msg_type is music but title or description or music_url is empty.' );
                }

                $title = $msg_extra_info['title'];
                $description = $msg_extra_info['description'];
                $music_url = $msg_extra_info['music_url'];

                extract( $this->_three_base_info() );

                //渲染 news 
                $this->_msg_result = sprintf( 
                        Xml_tpl::$music_xml_tpl ,
                        $to,
                        $from,
                        $time,
                        $title,
                        $description,
                        $music_url,
                        //高保真 url 不考虑
                        ''
                );
        }//}}}
}
