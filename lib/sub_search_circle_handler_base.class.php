<?php
/**
 * 各种 search 相关的一些操作
 * 需要完成的功能
 *
 * 1 判断是否达到了 查询次数的上限
 * 2 产生查询结果
 * 3 对查询结果的优化
 */
require_once 'handler_base.class.php';
require_once 'config.class.php';
require_once 'search_result_cache.class.php';

class Sub_search_circle_handler_base extends Handler_base {

        protected $_search_count;

        protected $_is_reg;

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
                $this->_search_count = $this->_context->get( 'search_count' );
                $this->_location = $this->_context->get( 'location' );
                $this->_target_sex = $this->_context->get( 'target_sex' );
                $this->_is_reg = $this->_context->get( 'is_reg' );
                $this->_search_result = $this->_context->get( 'search_result' );
                $this->_search_result_cache = new Search_result_cache();
        }

        /**
         * 判断查询次数是否已经用
         * @return 用尽返回 true 否则 返回 false
         */
        public function is_search_count_outrange() {
                $search_count = $this->_context->get( 'search_count' );
                if( $this->_is_reg == true ) {
                        //已经注册的情况下
                        if( $search_count > Config::$max_search_count_with_reg ) {
                                //达到注册后允许的上限 仅仅提示用户查询次数达到了上限
                                $this->_context->set( 'circle' , 'common' );
                                return array( true , '额，查询的次数达到每天' . (Config::$max_search_count_with_reg + 1) . '次的上限了啊 亲。去我们的网站看看吧，不限制查询次数哦: http://huaban123.com 。' );
                        }
                } else {
                        //还没注册的情况下
                        if( $this->_search_count > Config::$max_search_count_without_reg ) {
                                //达到未注册时允许的上限 提示用户注册
                                $this->_context->set( 'circle' , 'common' );
                                return array( true , '咦，您还未注册，已达到查询' . (Config::$max_search_count_without_reg + 1) . '次的上限。赶快输入“ZC”开始注册吧，获得更多的查询机会哦。注册后，你也可以出现在附近被推荐哦。' );
                        }
                }

                //还没有用尽
                return array( false , '还可以继续查询' );
        }

        /**
         * 对查询次数进行一次加一操作
         */
        public function incr_search_count() {
                return $this->_context->incr( 'search_count' );
        }

        /**
         * 构造查询结果
         *
         * @param $cond 查询的条件
         * @param $use_last_search_cond 是否使用上次的查询条件
         */
        public function make_search_result( $cond = array() , $use_last_search_cond ) {
        //{{{
                //对查询次数执行加一操作
                $this->incr_search_count();

                $target_sex = $this->_context->get( 'target_sex' );
                $location = $this->_context->get( 'location' );
                $is_reg = $this->_context->get( 'is_reg' );

                //完善 $cond
                $cond['sex'] = $target_sex;
                $cond['location'] = $location;

                //判断是否已经超过了最大的可查询次数
                $res = $this->is_search_count_outrange();
                if( $res[0] ) {
                        $search_result_xml = $this->_msg_producer->do_produce(
                                'text' ,
                                array( 'content' => $res[1] )
                        );
                        return array( false , $search_result_xml );
                }

                //查询次数还没有用尽的情况下
                //判断是否已经缓存 是否需要重新查询
                $cond_hash = sha1( json_encode( $cond ) );
                $res_json = $this->_search_result_cache->get( $cond_hash );
                $res = json_decode( $res_json , true );
                if( empty( $res ) ) {
                        //缓存未命中
                        $res_json = $this->_api->search( $cond );
                        $res = json_decode( $res_json , true );
                        if( $res['type'] == 'success' ) {
                                //查询成功
                                //缓存查询结果
                                $this->_search_result_cache->set( $cond_hash , $res['info'] );
                                $user_infos = json_decode( $res['info'] , true );
                        } else {
                                //查询失败
                                return array( false , '查询失败了' );
                        }
                } else {
                        //缓存命中
                        $user_infos = $res;
                }

                $user_infos_count = count( $user_infos );
                if( $user_infos_count > 0 ) {
                        $user_info = $user_infos[ rand( 0 , $user_infos_count - 1 ) ];
                        if( $this->_is_reg == true ) {
                                $tips = Config::$response_msg['search_result_tips_after_reg'];
                        } else {
                                $tips = Config::$response_msg['search_result_tips_before_reg'];
                        }
                        //构造 news 信息
                        $items = array(
                                array(
                                        'title' => $user_info['NickName'] ,
                                        'description' => $user_info['ZWMS'] . ' ' . $tips ,
                                        //@todo 判断头像是否存在
                                        'pic_url' => Config::$huaban123_server . 'UploadFiles/UHP/' . $user_info['HeadPic'] ,
                                        //用户详细信息页面
                                        'url' => Config::$huaban123_server . 'Action/WeixinUserInfoDetail.aspx?weixin_id=' . $this->_post_obj->FromUserName . '&&user_id=' . $user_info['UserId']
                                )
                        );

                        $search_result_xml = $this->_msg_producer->do_produce(
                                'news' , 
                                array( 'items' => $items ) 
                        );

                        return array( true , $search_result_xml );
                } else {
                        //查询结果为空
                        $search_result_xml = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '额 暂时没有符合你要求的用户。你可以换个城市试试，也可以去我们的网站看看: http://huaban123.com。' )
                        );

                        return array( true , $search_result_xml );
                }
        }//}}}

        public function do_circle() {

        }
}
