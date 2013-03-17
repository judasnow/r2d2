<?php
/**
 * 根据用户的输入 运行相关策略 返回相应的信息
 */
require_once( 'xml_tpl.class.php' );
require_once( 'context.class.php' );
require_once( 'api.class.php' );
require_once( 'debug.class.php' );
require_once( 'location.class.php' );
require_once( 'cache.class.php' );

class Strategy {

        const SEARCH_COUNT_LIMIT_WITHOUT_REG = 3;
        const SEARCH_COUNT_LIMIT_AFTER_REG = 3;

        //由当前用户输入 xml 解析而来的对象
        private $_post_obj;

        //当前的上下文对象
        private $_context;

        //和 huaban123.com 进行通信的 api
        private $_api;

        //默认消息
        private $_default_response;

        //最终回复的消息
        private $_response;
        
        public function __construct( $post_xml ) {
        //{{{
                //获取用户输入
                $this->_post_obj = simplexml_load_string( $post_xml , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->_request_msg_type = $this->_post_obj->MsgType;
                $this->_request_content = $this->_post_obj->Content;

                $this->_context = new Context( $this->_post_obj->FromUserName );
                $this->_api = new Api();

                $this->_cache = new Cache();

                //默认消息
                $this->_default_response = array(
                        'from_user_name' => $this->_post_obj->FromUserName,
                        'to_user_name' => $this->_post_obj->ToUserName,
                        'time' => $_SERVER['REQUEST_TIME'],

                        'res_msg_type' => 'text',
                        'extra_info' => '看到这条消息，就表示我出错了'
                );
        }//}}}

        private function full2half(){
        //{{{
                $arr = array(
                        //数字  
                        '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4', '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',   
                        //大写字母  
                        'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E', 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J', 
                        'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O', 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T', 
                        'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y', 'Ｚ' => 'Z',  
                        //小写字母  
                        'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd', 'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j', 
                        'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n', 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't', 
                        'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x', 'ｙ' => 'y', 'ｚ' => 'z',  
                        //括号  
                        '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[', '】' => ']', '〖' => '[', '〗' => ']', '《' => ' < ','》' => ' > ','｛' => ' {', '｝' => '} ',  
                        //其它
                        '％' => '%', '＋' => ' + ', '—' => '-', '－' => '-', '～' => '-','．'=>'.','：' => ':', '。' => '.', '，' => ',', '、' => '\\', '；' => ':', '？' => '?', '！' => '!',
                        '…' => '-', '‖' => '|','“' => "\"", '”' => "\"", '‘' => '`','’' => '`', '｜' => '|', '〃' => "\"",'　' => ' ' 
                );
                return strtr( $str , $arr );
        }//}}}

        /**
         * 生成文本信息
         */
        private function _produce_text_res() {
        //{{{
                extract( $this->_default_response );

                return sprintf(
                        Xml_tpl::$text_xml_tpl ,
                        $from_user_name ,
                        $to_user_name ,
                        $time ,
                        $res_msg_type ,
                        $extra_info
                );
        }//}}}

        /**
         * 生成图文信息
         */
        private function _produce_news_res() {
                extract( $this->_default_response );
                //首先生成 items
                //组合成完整的消息
                return sprintf(
                        Xml_tpl::$news_xml_tpl ,
                        $from_user_name ,
                        $to_user_name ,
                        $time ,
                        '1' ,
                        $extra_info
                );
        }

        private function _location_handler( $no ) {
        //{{{
                $when_set_location_ok = '位置设置 ok; 1. 我是男,查看女; 2. 我是女,查看男; 3. 我是女,查看女 4. 我是男,查看男';
                $is_set_location_ok = false;

                $request_msg_type = $this->_post_obj->MsgType;
                $location = new Location();

                if( $request_msg_type == 'text' ) {
                        $request_content = $this->_post_obj->Content;
                        //检查用户输入的是不是合法的城市名

                        //有 市 字则去除
                        $city_name = preg_replace( "/市$/u" , "" , $request_content );

                        //对照城市列表看是否有效
                        if( $location->valid_city( $city_name ) ) {
                                $this->_default_response['extra_info'] = $when_set_location_ok;
                                $is_set_location_ok = true;
                        } else {
                                $this->_default_response['extra_info'] = '请输入合法的位置信息';
                        }
                        $this->_response = $this->_produce_text_res();
                }

                if( $request_msg_type == 'location' ) {
                        $label = $this->_post_obj->Label;
                        $x = $this->_post_obj->Location_x;
                        $y = $this->_post_obj->Location_y;

                        //注意其不为empty 而为 0
                        if( $label === 0 ) {
                                $this->_default_response['extra_info'] = '额 你发的地址没有中文标签啊 改用手动输入的吧';
                                $this->_response = $this->_produce_text_res();
                        } else {
                                //从 label 中取出 地址信息
                                $city_name = preg_split( '/省|区|市/u' , $label )[1];
                                $this->_default_response['extra_info'] = $when_set_location_ok;
                                $is_set_location_ok = true;
                        }
                        $this->_response = $this->_produce_text_res();
                }

                if( $is_set_location_ok ) {
                        $this->_context->set( 'location' , $city_name );
                        //设置 位置 ok 进入到搜索 circle
                        $this->_circle_search_handeler();
                }
        }//}}}

        private function _circle_search_handeler() {
        //{{{
                $this->_context->set( 'circle' , 'search' );
                $this->_post_obj = null;
                $this->make_res();
        }//}}}

        //根据用户输入 设置性别以及性取向
        private function _sex_and_orientation_handler( $no ) {
        //{{{
                $input_num = $this->_request_content;
                if( $input_num < 5 && $input_num > 0 ) {
                        switch( $input_num ) {
                        case '1':
                                //男女
                                $this->_context->set( 'sex' , '男' );
                                $this->_context->set( 'orientation' , '女' );
                                break;
                        case '2':
                                //女男
                                $this->_context->set( 'sex' , '女' );
                                $this->_context->set( 'orientation' , '男' );
                                break;
                        case '3':
                                //女女
                                $this->_context->set( 'sex' , '女' );
                                $this->_context->set( 'orientation' , '女' );
                                break;
                        case '4':
                                //男男
                                $this->_context->set( 'sex' , '男' );
                                $this->_context->set( 'orientation' , '男' );
                                break;
                        }
                        //屏蔽匹配 1 2 3 4 的正则
                        $this->_context->set( 'ignore_ruler' , $no );
                        $this->_default_response['extra_info'] = '设置性别及取向ok 输入年龄.';
                        $this->_response = $this->_produce_text_res();
                }
                return $this->_response;
        }//}}}

        private function _search_handler() {
        //{{{
                $input_age = $this->_request_content;

                //需要检测匹配的次数 search_count
                $search_count = $this->_context->get( 'search_count' );
                if( empty( $search_count ) ) {
                        $search_count = 0;
                }

                //判断该用户是否已经注册

                if( $search_count < self::SEARCH_COUNT_LIMIT_WITHOUT_REG ) {
                        //没有达到限制的查询次数还
                        $location = $this->_context->get( 'location' );
                        $sex = $this->_context->get( 'orientation' );

                        $search_cond_hash = sha1( $input_age . $location );
                        $result_in_cache = $this->_cache->hget( 'search_user_result' , $search_cond_hash );
                        if( empty( $result_in_cache ) ) {
                                //执行查询操作
                                $res_json = $this->_api->search( array( 'age' => $input_age , 'location' => $location , 'sex' => $sex ) );

                                $res = json_decode( $res_json , true );
                                if( $res['type'] == 'success' ) {
                                        $this->_cache->hset( 'search_user_result' , $search_cond_hash , $res_json );
                                        $user_infos = json_decode( $res['info'] , true );
                                }
                        } else {
                                $user_infos = json_decode( json_decode( $result_in_cache , true )['info'] , true );
                        }

                        //显示查询结果
                        //指示当前显示的结果
                        $cursor = $this->_context->get( 'search_user_result_cursor' );
                        if( !isset( $cursor ) || $cursor >= count( $user_infos ) ) {
                                $cursor = 0;
                        }
                        $this->_context->set( 'search_user_result_cursor' , $cursor );
                        
                        //生成图文信息
                        $user_info = $user_infos[$cursor];
                        $cursor++;
                        $items = sprintf( Xml_tpl::$news_item_tpl , $user_info['NickName'] , $user_info['ZWMS'] , 'http://www.huaban123.com/UploadFiles/UHP/MIN/' . $user_info['HeadPic'] );
                        $this->_default_response['extra_info'] = $items;
                        $this->_response = $this->_produce_news_res();
                } else {
                        //提示用户注册
                }
        }//}}}

        //若匹配成功 进行的处理
        private function _when_success_match( $no , $cond , $response ) {
        //{{{
                //解析匹配结果
                $response_array = explode( '!' , $response , 2 );

                //确定返回类型
                $response_msg_type = $response_array[0];
                $response_extra_info = $response_array[1];

                if( $response_msg_type == 'circle' ) {
                        $this->{"_circle_{$response_array[1]}_handler"}( $no );
                }

                if( $response_msg_type == 'handler' ) {
                        $this->{"_{$response_extra_info}_handler"}( $no );
                }

                if( $response_msg_type == 'text' ) {
                        //覆盖默认消息
                        $this->_default_response['extra_info'] = $response_extra_info;

                        //生成真正的返回消息
                        $this->_response = $this->{"produce_{$response_msg_type}_res"}( $no );
                }
        }//}}}

        //根据当前 上下文信息 以及用户的输入信息 产生相应的结果
        public function make_res() {
        //{{{
                if( empty( $this->_post_obj ) ) {
                        return;
                }

                $request_msg_type = $this->_post_obj->MsgType;
                $request_content = $this->_post_obj->Content;

                //获取当前 circle
                $circle = $this->_context->get( 'circle' );
                if( empty( $circle ) ) {
                        $circle = 'common';
                        $this->_context->set( 'circle' , 'common' );
                }

                //加载相应的 ruler
                require( dirname(__FILE__) . "/../ruler/$circle.php" );

                //加载相应的 ignore_ruler
                $ignore_ruler_str = $this->_context->get( 'ignore_ruler' );
                if( isset( $ignore_ruler_str ) ) {
                        $ignore_ruler = str_split( "$ignore_ruler_str" );
                } else {
                        $ignore_ruler = array();
                }

                //进行循环匹配
                foreach( $ruler as $no => $item ) {

                        //剔除标记为 ignore 的 ruler
                        if( in_array( $no , $ignore_ruler ) ) {
                                continue;
                        }

                        //cond = behavior
                        $cond = key( $item );
                        $behavior = $item[$cond];

                        //对 ruler 中的 cond 进行解析
                        $cond_array = explode( '!' , $cond , 2 );
                        $cond_type = $cond_array[0];
                        $cond_content = $cond_array[1];

                        switch( $cond_type ) {
                        case 'text':
                                if( $request_content == $cond_content ) {
                                        $this->_when_success_match( $no , $cond , $behavior );
                                        return $this->_response;
                                }
                                break;
                        case 're':
                                if( preg_match( $cond_content , $request_content ) ) {
                                        $this->_when_success_match( $no , $cond , $behavior );
                                        return $this->_response;
                                }
                                break;
                        case 'type':
                                if( $cond_content == 'location' ) {
                                        $this->_when_success_match( $no , $cond , $behavior );
                                        return $this->_response;
                                }
                                break;
                        }
                }
                $this->_response = $this->_produce_text_res();
                return $this->_response;
        }//}}}
}
