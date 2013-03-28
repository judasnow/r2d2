<?php
/**
 * 根据用户的输入 运行相关策略 返回相应的信息
 */

//requires
//{{{
require_once 'context.class.php';
require_once 'api.class.php';
require_once 'debug.class.php';
require_once 'store.class.php';
require_once 'msg_producer.class.php';
require_once 'utility.class.php';

require_once 'common_circle_handler.class.php';
require_once 'reg_circle_handler.class.php';
require_once 'look_around_circle_handler.class.php';
require_once 'search_method_select_circle_handler.class.php';
require_once 'search_by_height_circle_handler.class.php';
require_once 'search_by_weight_circle_handler.class.php';
require_once 'search_by_age_circle_handler.class.php';
require_once 'location_circle_handler.class.php';
require_once 'upload_image_circle_handler.class.php';
//}}}

class Strategy {

        /**
         * 由当前用户输入 xml 解析而来的对象
         */
        private $_post_obj;

        /**
         * 当前 weixin 帐号的上下文对象
         */
        private $_context;

        /**
         * 和 huaban123.com 进行通信的 api
         */
        private $_api;

        /**
         * 最终回复给用户的消息
         */
        private $_response;

        /**
         * @param $post_xml 是 weixin 直接发送来的 xml 信息
         */
        public function __construct( $post_xml ) {
        //{{{
                //获取用户输入
                if( empty( $post_xml ) ) {
                        throw new Exception( '$post_xml is empty.' );
                }
                $this->_post_obj = simplexml_load_string( $post_xml , "SimpleXMLElement" , LIBXML_NOCDATA );

                $this->_request_msg_type = $this->_post_obj->MsgType;
                //全角转换 去除空格 以及 大小写转换
                $this->_request_content = Utility::format_user_input( $this->_post_obj->Content );

                $this->_context = new Context( $this->_post_obj->FromUserName );

                $this->_api = new Api();

                $this->_msg_producer = new Msg_producer( 
                        array( 
                                'from'=>$this->_post_obj->ToUserName ,
                                'to'=>$this->_post_obj->FromUserName 
                        )
                );
        }//}}}

        /**
         * 根据当前 上下文信息 以及用户的输入信息 产生相应的结果
         */
        public function make_res() {

                //获取当前 circle
                $circle = $this->_context->get( 'circle' );

                //获取用户是否已经注册
                $is_reg = $this->_context->get( 'is_reg' );

                //通用操作 和 circle 无关 也就是说在所有的 circle 中都会其作用
                //{{{

                //输入 [help|?] 显示帮助信息
                if( $this->_request_content == '?' || $this->_request_content == 'help' ) {
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' ,
                                array( 'content' => Config::$response_msg['help'] )
                        );
                        return $this->_response;
                }

                //关注时自动发送的欢迎信息
                //也可以作为整个应用的 初始化函数
                if( $this->_post_obj->MsgType == 'event' && $this->_post_obj->Event == 'subscribe' ) {
                        //进行一系列的初始化操作

                        //初始化用户查找对象的性别信息
                        $this->_context->set( 'target_sex' , '女' );

                        //初始化用户查找次数
                        $this->_context->set( 'search_count' , 0 );

                        //初始化用户是否已经注册
                        $this->_context->set( 'is_reg' , false );

                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' ,
                                array( 'content' => Config::$response_msg['hello2bizuser'] )
                        );
                        return $this->_response;
                }

                //输入 q 返回到上一级循环
                if( $this->_request_content == 'q' ) {
                        //处理几种特殊的返回
                        //记录是从哪个 circl 退出的
                        $is_quit_from = $circle;
                        if( $this->_context->exit_current_circle() ) {

                                $circle = $this->_context->get( 'circle' );

                                if( $is_quit_from == 'upload_image' &&  $circle == 'reg' ) {
                                        //从 upload_image 返回 reg , 如果用户已经上传至少一张照片了，就提示用户注册成功
                                        $image_count = $this->_context->get( 'image_count' );
                                        if( $image_count > 0 ) {
                                                $user_info = $this->_context->get_user_info();
                                                //显示成功注册的信息
                                                $content = sprintf(
                                                        Config::$response_msg['reg_success'] ,
                                                        $user_info['username'] , $user_info['nickname'] , $user_info['qq'] , $user_info['height'] , $user_info['weight'] , $user_info['email'] , $user_info['zwms'] 
                                                );
                                        } else {
                                                //从注册模式退出但是还没有上传图片
                                                $content = Config::$response_msg['quit_circle']['reg'];
                                        }

                                        //一直退到 common
                                        while( $this->_context->get( 'circle' ) != 'common' ) {
                                                $this->_context->exit_current_circle();
                                        }
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' ,
                                                array( 'content' => $content )
                                        );
                                        return $this->_response;
                                }

                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        array( 'content' => Config::$response_msg['quit_circle'][$is_quit_from] )
                                );
                                return $this->_response;
                        } else {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => '不能再退了，亲。' )
                                );
                                return $this->_response;
                        }
                }

                //输入 zc 进入注册流程
                if( $this->_request_content == 'zc' ) {
                        //还需要保证用户没有注册 $is_reg = false
                        //而且需要照片数不为 0
                        $image_count = $this->_context->get( 'image_count' );
                        if( $is_reg == true && $image_count > 0 ) {
                                //用户已经注册
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => '您已经注册了啊，亲。去我们的网站看看吧: http://huaban123.com' )
                                );
                                return $this->_response;
                        }
                        if( $circle != 'reg' ) {
                                $this->_context->set( 'circle' , 'reg' );
                                
                                $reg_next_step = $this->_context->get( 'reg_next_step' );
                                if( $reg_next_step == 'upload_image' ) {
                                        $this->_context->set( 'circle' , 'upload_image' );
                                }

                                $reg_circle_handelr = new Reg_circle_handler( $this->_post_obj );
                                $responce_content = $reg_circle_handelr->produce_msg_by_last_reg_step();

                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => $responce_content )
                                );
                                return $this->_response;
                        }
                }

                //更换查询对象性别
                if( $this->_request_content == 'h' ) {
                        $target_sex = $this->_context->get( 'target_sex' );
                        //@todo 可由 lua 脚本完成
                        if( $target_sex == '女' ) {
                                $target_sex = '男';
                                $this->_context->set( 'target_sex' , '男' );
                        } elseif ( $target_sex == '男' ) {
                                $target_sex = '女';
                                $this->_context->set( 'target_sex' , '女' );
                        }

                        //若当前所处的 circle 为 任何一种 search 则在最后需要提示用户 如需继续 输入 n
                        $all_search_circle_name = array( 'look_around' , 'search_by_age' , 'search_by_weight' , 'search_by_height' );
                        if( in_array( $circle , $all_search_circle_name ) ) {
                                $content = "您已经更改查询性别为：$target_sex 。继续查询请按 “n”，或重新输入查询条件。";
                        } else {
                                $content = "您已经更改查询性别为：$target_sex 。您可以发送位置信息查找附近的人，也可以注册之后输入“c”进行详细查询。";
                        }
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' ,
                                array( 'content' => $content )
                        );
                        return $this->_response;
                }

                //输入 location 类型的消息 或者输入的是有效的二级地址的名称
                //则进入到查询附近的人 look around circle
                //在 reg circle 下要屏蔽这种行为
                $location_circle_handler = new Location_circle_handler( $this->_post_obj );
                if( $location_circle_handler->is_possible_location() && $circle != 'reg' ) {
                        if( $circle != 'look_around' ) {
                                $this->_context->set( 'circle' , 'look_around' );
                                return $this->make_res();
                        }
                }

                if( $this->_request_content == 'debug' ) {
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' ,
                                array( 'content' =>  '当前循环: ' . $circle )
                        );
                        return $this->_response;
                }
                //}}}

                //对于各种 circle 的判断
                //最外层
                if( $circle == 'common' ) {
                //{{{
                        $common_circle_handler = new Common_circle_handler( $this->_post_obj );
                        $this->_response = $common_circle_handler->do_circle();
                        return $this->_response;
                }//}}}

                //注册
                if( $circle == 'reg' ) {
                //{{{
                        $reg_circle_handler = new Reg_circle_handler( $this->_post_obj );
                        $this->_response = $reg_circle_handler->do_circle();
                        return $this->_response;
                }//}}}

                //上传照片
                if( $circle == 'upload_image' ) {
                //{{{
                        $upload_image_circle_hander = new Upload_image_circle_hander( $this->_post_obj );
                        $this->_response = $upload_image_circle_hander->do_circle();
                        return $this->_response;
                }//}}}

                //按条件搜索
                if( $circle == 'search_method_select' ) {
                //{{{
                        $search_method_select_circle_handler = new Search_method_select_circle_handler( $this->_post_obj );
                        $this->_response = $search_method_select_circle_handler->do_circle();
                        return $this->_response;
                }//}}}

                //按身高查询
                if( $circle == 'search_by_height' ) {
                //{{{
                        $search_by_height_circle_handler = new Search_by_height_circle_handler( $this->_post_obj );
                        $this->_response = $search_by_height_circle_handler->do_circle();
                        return $this->_response;
                }//}}}

                //按体重查询
                if( $circle == 'search_by_weight' ) {
                //{{{
                        $search_by_weight_circle_handler = new Search_by_weight_circle_handler( $this->_post_obj );
                        $this->_response = $search_by_weight_circle_handler->do_circle();
                        return $this->_response;
                }//}}}

                //按年龄查询
                if( $circle == 'search_by_age' ) {
                //{{{
                        $search_by_age_circle_handler = new Search_by_age_circle_handler( $this->_post_obj );
                        $this->_response = $search_by_age_circle_handler->do_circle();
                        return $this->_response;
                }//}}}

                //搜索附近的人
                if( $circle == 'look_around' ) {
                //{{{
                        $look_around_circle_handler = new Look_around_circle_handler( $this->_post_obj );
                        $this->_response = $look_around_circle_handler->do_circle();
                        return $this->_response;
                }//}}}
        }
}
