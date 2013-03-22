<?php
/**
 * 执行用户的注册流程
 */
require_once 'api.class.php';
require_once 'context.class.php';
require_once 'msg_producer.class.php';

class Reg_circle_handler {

        private $_response;

        private $_input_success_message = array(
                'just_begin' => '欢迎注册,请输入一个用户名,可以由字母数字以及下划线组成',
                'username' => '用户名输入成功,请输入昵称:',
                'nickname' => '昵称输入成功,请输入身高(公分cm):',
                'height' => '身高输入成功,请输入体重(斤)',
                'weight' => '体重输入成功,请输入年龄',
                'age' => '年龄输入成功,请上传照片(第一张将作为您的头像)',
                'zwms' => '请填写交友宣言,完成最后一步'
        );

        public function __construct( $post_obj ) {
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
        }

        /**
         * 根据用户注册的步骤 
         * 当用户反回注册流程时 (输入 zc)
         * 返回相应的消息
         */
        public function produce_msg_by_reg_step() {
                $next_step = $this->_context->get( 'reg_next_step' );

                if( !empty( $next_step ) ) {
                        $msg = $this->_input_success_message[$next_step];
                } else {
                        $msg = $this->_input_success_message['just_begin'];
                }

                return $msg;
        }

        public function do_circle() {
        //{{{
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
                                                        array( 'content' => "用户名: $user_input_username 已经被占用，换一个吧." )
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
                                                        array( 'content' => $this->_input_success_message['username'] )
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
                                        array( 'content' => '注意用户名的格式,只能由"数字","字母"以及"下划线"组成,比如 "_Hahaha_"' )
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

                        if( preg_match( '/[a-zA-Z0-9\_]+$/' , $user_input_nickname ) ) {
                                //判断用户昵称是否可用
                                $res_json = $this->_api->checkNickName( array( 'nickName' => $user_input_nickname ) );
                                $res = json_decode( $res_json , true );
                                if( $res['type'] == 'success' ) {
                                        if( $res['info'] == 'true' ) {
                                                //已经被注册
                                                $this->_response = $this->_msg_producer->do_produce( 
                                                        'text' ,
                                                        array( 'content' => "昵称: $user_input_username 已经被占用，换一个吧." )
                                                );
                                                return $this->_response;
                                        } else {
                                                //未被注册
                                                //在 context 中保存这个 nickname
                                                $this->_context->set( 'nickname' , $user_input_nickname );
                                                $this->_context->set( 'reg_next_step' , 'height' );
                                                $this->_response = $this->_msg_producer->do_produce( 
                                                        'text' , 
                                                        array( 'content' => $this->_input_success_message['nickname'] )
                                                );
                                                return $this->_response;
                                        }
                                }
                        }
                }//}}}

                //输入 height 
                $height = $this->_context->get( 'height' );
                if( empty( $height ) ) {
                //{{{
                        $user_input_height = $this->_request_content;

                        if( $user_input_height <= 100 || $user_input_height >= 250 ) {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => "身高需要是 100-250 之间的数字" )
                                );
                                return $this->_response;
                        } else {
                                //符合要求
                                $this->_context->set( 'height' , $user_input_height );
                                $this->_context->set( 'reg_next_step' , 'weight' );
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => $this->_input_success_message['height'] )
                                );
                                return $this->_response;
                        }
                }//}}}

                $weight = $this->_context->get( 'weight' );
                if( empty( $weight ) ) {
                //{{{
                        $user_input_weight = $this->_request_content;
                        if( $user_input_weight < 60 || $user_input_weight > 200 ) {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => "体重需要是 60-200 之间的数字" )
                                );
                                return $this->_response;
                        } else {
                                $this->_context->set( 'weight' , $user_input_weight );
                                $this->_context->set( 'reg_next_step' , 'age' );
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => $this->_input_success_message['weight'] )
                                );
                                return $this->_response;
                        }
                }//}}}

                $age = $this->_context->get( 'age' );
                if( empty( $age ) ) {
                //{{{
                        $user_input_age = $this->_request_content;

                        if( $user_input_age < 18 || $user_input_age > 60 ) {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => "年龄需要是 18-60 之间的数字" )
                                );
                                return $this->_response;
                        } else {
                                $this->_context->set( 'age' , $user_input_age );
                                $this->_context->set( 'reg_next_step' , 'zwms' );

                                //手动进入 uploading_image circle
                                $this->_context->set( 'circle' , 'uploading_image' );

                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => $this->_input_success_message['age'] )
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
                                        $this->_context->exit_current_circle();
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => '注册完毕!输入 "s" 可以查询附近的人' )
                                        );
                                        return $this->_response;
                                } else {
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => '注册失败了 orz.' )
                                        );
                                        return $this->_response;
                                }
                        } else {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => '交友宣言不能为空' )
                                );
                                return $this->_response;
                        }
                }//}}}

        }//}}}

        private function do_reg() {
                //选择性别 1 2 3 4

                $res_json = $this->_api->Reg(
                        array(
                                'username' => $this->_context->get( 'username' ),
                                'nickname' => $this->_context->get( 'nickname' ),
                                'height' => $this->_context->get( 'height' ),
                                'weight' => $this->_context->get( 'weight' ),
                                'age' => $this->_context->get( 'age' ),
                                'gender' => '女',
                                'qq' => '123'
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
        }
}
