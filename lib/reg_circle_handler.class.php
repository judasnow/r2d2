<?php
require_once 'handler_base.class.php';
require_once 'config.class.php';

class Reg_circle_handler extends Handler_base {

        private $_response;

        public function __construct( $post_obj ) {
        //{{{
                $this->_post_obj = $post_obj;

                $this->_context = new Context( $post_obj->FromUserName );
                $this->_api = new Api();

                $this->_request_msg_type = $this->_post_obj->MsgType;
                $this->_request_content = $this->_post_obj->Content;

                $this->_msg_producer = new Msg_producer(
                        array(
                                'from'=>$this->_post_obj->ToUserName ,
                                'to'=>$this->_post_obj->FromUserName
                        )
                );
        }//}}}

        /**
         * 根据用户注册的步骤
         * 当用户反回注册流程时 (输入 zc)
         * 返回相应的消息
         */
        public function produce_msg_by_last_reg_step() {
        //{{{
                $next_step = $this->_context->get( 'reg_next_step' );

                if( !empty( $next_step ) ) {
                        $msg = Config::$response_msg['when_back_reg'][$next_step];
                } else {
                        $msg = Config::$response_msg['when_back_reg']['just_begin'];
                }

                return $msg;
        }//}}}

        public function do_circle() {
        //{{{
                //输入性别以及取向的选项
                $sex_and_target_sex_index = $this->_context->get( 'sex_and_target_sex_index' );

                //之所以放到前面是因为需要依据 location 是否已经设置来判断下一步行为
                $location = $this->_context->get( 'location' );

                if( empty( $sex_and_target_sex_index ) ) {
                //{{{
                        $user_input_sex_and_target_sex_index = (int)$this->_request_content;
                        if( is_numeric( $user_input_sex_and_target_sex_index ) && $user_input_sex_and_target_sex_index <= 4 && $user_input_sex_and_target_sex_index >= 1 ) {

                                $this->_context->set( 'sex_and_target_sex_index' , $user_input_sex_and_target_sex_index );

                                switch( $user_input_sex_and_target_sex_index ) {
                                        case 1:
                                                $this->_context->set( 'sex' , '男' );
                                                $this->_context->set( 'target_sex' , '女' );
                                                break;
                                        case 2:
                                                $this->_context->set( 'sex' , '男' );
                                                $this->_context->set( 'target_sex' , '男' );
                                                break;
                                        case 3:
                                                $this->_context->set( 'sex' , '女' );
                                                $this->_context->set( 'target_sex' , '男' );
                                                break;
                                        case 4:
                                                $this->_context->set( 'sex' , '女' );
                                                $this->_context->set( 'target_sex' , '女' );
                                                break;
                                }
                                if( empty( $location ) ) {
                                        //需要填写地址
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => Config::$response_msg['input_success_message']['sex_and_target_sex_index_without_location'] )
                                        );
                                } else {
                                        //用户之前已经填写了地址信息
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => Config::$response_msg['input_success_message']['sex_and_target_sex_index_with_location'] )
                                        );
                                }
                                $this->_context->set( 'reg_next_step' , 'location' );
                                return $this->_response;
                        } else {
                                //用户输入信息无效 下一步还是当前步骤
                                $this->_context->set( 'reg_next_step' , 'sex_and_target_sex_index' );
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => Config::$response_msg['input_invalid_message']['sex_and_target_sex_index'] )
                                );
                                return $this->_response;
                        }
                }//}}}

                //输入 location 信息
                if( empty( $location ) ) {
                //{{{
                        $location_circle_handler = new Location_circle_handler( $this->_post_obj );
                        $res = $location_circle_handler->just_get_location();
                        if( $res[0] == true ) {
                                //获取 location 成功
                                $city_name =  $res[1];
                                $this->_context->set( 'location' , $city_name );
                                $this->_context->set( 'reg_next_step' , 'username' );
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => Config::$response_msg['input_success_message']['location'] )
                                );
                                return $this->_response;
                        } else {
                                //获取地址信息错误 返回相应的错误信息
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => $res[1] )
                                );
                                return $this->_response;
                        }
                }
                //}}}

                //输入 username
                $username = $this->_context->get( 'username' );
                if( empty( $username ) ) {
                //{{{
                        //还没有成功输入用户名
                        //用户输入的一切都认为是用户名 并判断其有效性
                        //@todo 注入危险?
                        $user_input_username = $this->_request_content;

                        //先判断其是否符合相应的规则
                        if( preg_match( '/[a-zA-Z0-9\_]+$/' , $user_input_username ) ) {
                                //判断用户名是否唯一
                                $res_json = $this->_api->checkUserName( array( 'userName' => $user_input_username ) );
                                $res = json_decode( $res_json , true );
                                if( $res['type'] == 'success' ) {
                                        if( $res['info'] == 'true' ) {
                                                //已经被注册
                                                $this->_response = $this->_msg_producer->do_produce( 
                                                        'text' ,
                                                        array( 'content' => $user_input_username . '，' . Config::$response_msg['input_invalid_message']['username_has_reg'] )
                                                );
                                                return $this->_response;
                                        } else {
                                                //未被注册
                                                //在 context 中保存这个 username
                                                $this->_context->set( 'username' , $user_input_username );

                                                //保存 step 
                                                $this->_context->set( 'reg_next_step' , 'nickname' );

                                                $this->_response = $this->_msg_producer->do_produce(
                                                        'text' ,
                                                        array( 'content' => Config::$response_msg['input_success_message']['username'] )
                                                );
                                                return $this->_response;
                                        }
                                } else {
                                        //服务器不可用
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => '服务器不可用.' )
                                        );
                                        return $this->_response;
                                }
                        } else {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => Config::$response_msg['input_invalid_message']['username'] )
                                );
                                return $this->_response;
                        }
                }//}}}

                //输入 nickname
                //@todo 重复代码 必须重构
                $nickname = $this->_context->get( 'nickname' );
                if( empty( $nickname ) ) {
                //{{{
                        $user_input_nickname = $this->_request_content;

                        if( preg_match( '/.{4,}+$/' , $user_input_nickname ) ) {
                                //判断用户昵称是否可用
                                $res_json = $this->_api->checkNickName( array( 'nickName' => $user_input_nickname ) );
                                $res = json_decode( $res_json , true );
                                if( $res['type'] == 'success' ) {
                                        if( $res['info'] == 'true' ) {
                                                //已经被注册
                                                $this->_response = $this->_msg_producer->do_produce( 
                                                        'text' ,
                                                        array( 'content' => Config::$response_msg['input_invalid_message']['nickname_has_reg'] )
                                                );
                                                return $this->_response;
                                        } else {
                                                //未被注册
                                                //在 context 中保存这个 nickname
                                                $this->_context->set( 'nickname' , $user_input_nickname );
                                                $this->_context->set( 'reg_next_step' , 'qq' );
                                                $this->_response = $this->_msg_producer->do_produce( 
                                                        'text' , 
                                                        array( 'content' => Config::$response_msg['input_success_message']['nickname'] )
                                                );
                                                return $this->_response;
                                        }
                                }
                        } else {
                                //输入非法
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => Config::$response_msg['input_invalid_message']['nickname'] )
                                );
                                return $this->_response;
                        }
                }//}}}

                $qq = $this->_context->get( 'qq' );
                if( empty( $qq ) ) {
                //{{{
                        $user_input_qq = $this->_request_content;
                        if( preg_match( '/^\s*[.0-9]{5,10}\s*$/' , $user_input_qq ) ) {
                                $this->_context->set( 'qq' , $user_input_qq );
                                $this->_context->set( 'reg_next_step' , 'height' );

                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        array( 'content' => Config::$response_msg['input_success_message']['qq'] )
                                );
                                return $this->_response;
                        } else {
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        array( 'content' => Config::$response_msg['input_invalid_message']['qq'] )
                                );
                                return $this->_response;
                        }
                }//}}}

                //输入 height 
                $height = $this->_context->get( 'height' );
                if( empty( $height ) ) {
                //{{{
                        $user_input_height = ceil( $this->_request_content );
                        if( $user_input_height <= 150 || $user_input_height >= 230 ) {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => Config::$response_msg['input_invalid_message']['height'] )
                                );
                                return $this->_response;
                        } else {
                                //符合要求
                                $this->_context->set( 'height' , $user_input_height );
                                $this->_context->set( 'reg_next_step' , 'weight' );
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => Config::$response_msg['input_success_message']['height'] )
                                );
                                return $this->_response;
                        }
                }//}}}

                $weight = $this->_context->get( 'weight' );
                if( empty( $weight ) ) {
                //{{{
                        $user_input_weight = ceil( $this->_request_content );
                        if( !is_numeric( $user_input_weight ) && ( $user_input_weight < 70 || $user_input_weight > 230 ) ) {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => Config::$response_msg['input_invalid_message']['weight'] )
                                );
                                return $this->_response;
                        } else {
                                $this->_context->set( 'weight' , $user_input_weight );
                                $this->_context->set( 'reg_next_step' , 'age' );
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => Config::$response_msg['input_success_message']['weight'] )
                                );
                                return $this->_response;
                        }
                }//}}}

                $age = $this->_context->get( 'age' );
                if( empty( $age ) ) {
                //{{{
                        $user_input_age = ceil( $this->_request_content );

                        if( $user_input_age < 18 || $user_input_age > 60 ) {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => Config::$response_msg['input_invalid_message']['age'] )
                                );
                                return $this->_response;
                        } else {
                                $this->_context->set( 'age' , $user_input_age );
                                $this->_context->set( 'reg_next_step' , 'zwms' );

                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => Config::$response_msg['input_success_message']['age'] )
                                );
                                return $this->_response;
                        }
                }//}}}

                //填写交友宣言
                $zwms = $this->_context->get( 'zwms' );
                if( empty( $zwms ) ) {
                //{{{
                        $user_input_zwms = $this->_request_content;

                        if( !empty( $user_input_zwms  ) ) {
                                //只要不为空
                                $this->_context->set( 'zwms' , $user_input_zwms );

                                //触发注册事件
                                if( $this->do_reg() ) {
                                        $this->_context->set( 'is_reg' , true );
                                        //清楚 search_count 
                                        $this->_context->set( 'search_count' , 0 );
                                        $this->_context->set( 'reg_next_step' , 'upload_image' );

                                        //到这里已经注册成功 但是
                                        //还需要进入 upload_image circle 至少要上传一张照片才行
                                        $this->_context->set( 'circle' , 'upload_image' );

                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' ,
                                                array( 'content' => Config::$response_msg['input_success_message']['zwms'] )
                                        );
                                        return $this->_response;
                                } else {
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' ,
                                                array( 'content' => '注册失败了 orz。 稍后再试一试吧。' )
                                        );
                                        return $this->_response;
                                }
                        } else {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => Config::$response_msg['input_invalid_message']['zwms'] )
                                );
                                return $this->_response;
                        }
                }//}}}

                //上传照片
                $image_count = $tais->_context->get( 'image_count' );
                if( $image_count > 0 ) {
                        $this->_context->exit_current_circle();
                } else {
                        $this->_context->set( 'circle' , 'upload_image' );
                }

        }//}}}

        private function do_reg() {
        //{{{
                $res_json = $this->_api->Reg(
                        array(
                                'username' => $this->_context->get( 'username' ),
                                'nickname' => $this->_context->get( 'nickname' ),
                                'height' => $this->_context->get( 'height' ),
                                'weight' => $this->_context->get( 'weight' ),
                                'age' => $this->_context->get( 'age' ),
                                'gender' => $this->_context->get( 'sex' ),
                                'qq' => $this->_context->get( 'qq' ),
                                'zwms' => $this->_context->get( 'zwms' )
                        )
                );

                $res = json_decode( $res_json , true );

                if( $res['type'] == 'success' ) {
                        $user_id = json_decode( $res["info"] , true )['info'];
                        $this->_context->set( 'user_id' , $user_id );

                        return true;
                } else {
                        return false;
                }
        }//}}}
}
