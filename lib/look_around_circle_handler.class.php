<?php
require_once 'handler_base.class.php';
require_once 'location_circle_handler.class.php';
require_once 'config.class.php';

class Look_around_circle_handler extends Handler_base{

        private $_city_name;

        private $_response;

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        /**
         * 查找附近的人
         * @param $just_next 如果该参数为 true 则仅仅只是显示上次查询结果的下一条记录 而不重新进行查询
         */
        private function _look_around( $just_next = false ) {
        //{{{
                $target_sex = $this->_context->get( 'target_sex' );
                $search_count = $this->_context->get( 'search_count' );
                $is_reg = $this->_context->get( 'is_reg' );

                if( $is_reg == true ) {
                        if( $search_count > Config::$max_search_count_with_reg ) {
                                //达到注册后允许的上限 仅仅提示用户查询次数达到了上限
                                //@todo news msg
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        array( 'content' => '额 查询的次数达到了上限啊 亲。去我们的网站看看吧: http://huaban123.com 。' )
                                );
                                return $this->_response;
                        }
                } else {
                        if( $search_count > Config::$max_search_count_without_reg ) {
                                //达到未注册时允许的上限 提示用户注册
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        array( 'content' => '额 没有注册的你 查询的次数达到了上限 。输入 "zc" 注册一下吧 可以获得更多的查询机会哦。' )
                                );
                                return $this->_response;
                        }
                }

                //查询次数还没有用尽
                if( $just_next == false ) {
                        //需要重新查询
                        $res_json = $this->_api->search( array( 'location' => $this->_city_name , 'sex' => $target_sex ) );
                        $res = json_decode( $res_json , true );

                        if( $res['type'] == 'success' ) {
                                //查询成功
                                //缓存查询结果 注意这个缓存时针对于每一个用户的
                                $this->_context->set( 'search_result' , $res['info'] );
                                $user_infos = json_decode( $res['info'] , true );
                        } else {
                                //查询失败 显示笑话 log 之
                                return;
                        }

                } else {
                        $user_infos = json_decode( $this->_context->get( 'search_result' ) , true );
                }

                $user_infos_count = count( $user_infos );
                if( $user_infos_count > 0 ) {
                        $user_info = $user_infos[ rand( 0 , $user_infos_count - 1 ) ];
                        //构造 news 信息
                        $items = array( 
                                array(
                                        'title' => $user_info['NickName'] ,
                                        'description' => $user_info['ZWMS'] . '[小提示：输入 "n" 可以看下一个，也可以重新输入地址信息]' ,
                                        //@todo 判断头像是否存在
                                        'pic_url' => Config::$huaban123_server . 'UploadFiles/UHP/' . $user_info['HeadPic'] ,
                                        //用户详细信息页面
                                        'url' => Config::$huaban123_server . 'Action/WeixinUserInfoDetail.aspx?weixin_id=' . $this->_post_obj->FromUserName . '&&user_id=' . $user_info['UserId']
                                )
                        );
                        $this->_response = $this->_msg_producer->do_produce(
                                'news' , 
                                array( 'items' => $items ) 
                        );
                        //对查询次数执行加一操作
                        $this->_context->incr( 'search_count' );
                } else {
                        //结果为空
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '额 暂时没有符合你要求的用户。你可以换个城市试试，也可以去我们的网站看看: http://huaban123.com' )
                        );
                }
        }//}}}

        public function do_circle() {
        //{{{
                $location_circle_handler = new Location_circle_handler( $this->_post_obj );
                //需要先陷入 location circle 获取最新的城市输入
                //$this->_context->set( 'circle' , 'location' );
                $res = $location_circle_handler->just_get_location();
                if( $res[0] == true ) {
                        $this->_city_name = $res[1];
                        if( $this->_request_msg_type == 'text' ) {
                                //判断是否为 n , 如果是的话 就不需要切换城市 只需要显示下一张便可
                                if( $this->_request_content == 'n' ) {
                                        $this->_look_around( true );
                                } else {
                                        $this->_look_around();
                                }
                                return $this->_response;
                        }     
                }

        }//}}}
}
