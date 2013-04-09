<?php
require_once 'handler_base.class.php';
require_once 'config.class.php';
require_once 'city_list.class.php';

class Bind_account_circle_handler extends Handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        /**
         * @todo 重构
         * 判断用户发送的是否为文本类型的消息
         * 不是的话直接返回错误消息
         */
        private function is_text_msg() {
                if( $this->_request_msg_type != 'text' ) {
                        
                }
        }

        /**
         * 需要注意的就是 要区分用户在weixin上注册时的 username 
         * 以及用户 bind 时的 username，因为用户有可能在注册到一半
         * 的时候尝试进行帐号的绑定 这样的情况发生的话 会产生矛盾
         */
        public function do_circle() {
                //先获取用户名 username
                $username_for_bind = $this->_context->get( 'username_for_bind' );
                if( empty( $username_for_bind ) ) {
                        //用户还没有输入用户名 断言用户目前的输入为用户名
                        if( $this->_request_msg_type != 'text' ) {
                                //只有 text 类型的信息才有可能包含用户名信息
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        array( 'content' => Language_config::$user_input_username_for_bind_invalid )
                                );
                                return $this->_response;
                        } else {
                                //@todo 此处为了减少对对服务器的访问次数 因该尽可能的屏蔽一些
                                //非法的用户名
                                $username_for_bind = $this->_request_content;
                                if( empty( $username_for_bind ) ) {
                                        $this->_response = $this->_msg_producer->do_produce(
                                                'text' ,
                                                array( 'content' => Language_config::$user_input_username_for_bind_invalid )
                                        );
                                        return $this->_response;
                                } else {
                                        //输入得到确认
                                        $this->_context->set( 'username_for_bind' , $username_for_bind );
                                        $this->_response = $this->_msg_producer->do_produce(
                                                'text' ,
                                                array( 'content' => Language_config::$user_input_username_for_bind_ok )
                                        );
                                        return $this->_response;
                                }
                        }
                }

                //再获取用户密码
                $password_for_bind = $this->_context->get( 'password_for_bind' );
                if( empty( $password_for_bind ) ) {
                        if( $this->_request_msg_type != 'text' ) {
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        array( 'content' => Language_config::$user_input_password_for_bind_invalid )
                                );
                                return $this->_response;
                        } else {
                                $password_for_bind = $this->_request_content;
                                if( empty( $password_for_bind ) ) {
                                        $this->_response = $this->_msg_producer->do_produce(
                                                'text' ,
                                                array( 'content' => Language_config::$user_input_password_for_bind_invalid )
                                        );
                                        return $this->_response;
                                } else {
                                        //输入 ok 尝试绑定
                                        $this->_context->set( 'password_for_bind' , $password_for_bind );

                                        $username_for_bind = $this->_context->get( 'username_for_bind' );

                                        $this->_api = new Api();
                                        $res_json = $this->_api->authUser( 
                                                array( 
                                                        'username' => $username_for_bind,
                                                        'password' =>strtoupper( md5( $password_for_bind ) )
                                                ) 
                                        );
                                        //@todo null invlaid json
                                        $res = json_decode( $res_json , true );
                                        if( $res['type'] == 'success' ) {
                                                //认证成功 开始绑定操作
                                                $res_info = json_decode( $res['info'] , true );
                                                $user_info = $res_info[0];
                                                $image_count = $res_info[1]['image_count'];
                                                $this->_context->set( 'user_id' , $user_info['UserId'] );
                                                $this->_context->set( 'username' , $user_info['UserName'] );
                                                $this->_context->set( 'nickname' , $user_info['NickName'] );

                                                //根据 AreaId 获取城市信息
                                                if( isset( City_list::$list_with_id_flip[$user_info['AreaId']] ) ) {
                                                        $location = City_list::$list_with_id_flip[$user_info['AreaId']];
                                                } else {
                                                        $location = '';
                                                }
                                                $this->_context->set( 'location' , $user_info['AreaId'] );

                                                $this->_context->set( 'qq' , $user_info['QQ'] );

                                                //根据用户性别默认性取向
                                                $sex = $user_info['UserSex'];
                                                $this->_context->set( 'sex' , $user_info['UserSex'] );
                                                if( $sex == '男' ) {
                                                        $this->_context->set( 'target_sex' , '女' );  
                                                } else {
                                                        $this->_context->set( 'target_sex' , '男' );  
                                                }

                                                $this->_context->set( 'height' , $user_info['SG'] );
                                                $this->_context->set( 'weight' , $user_info['TZ'] );
                                                $this->_context->set( 'age' , $user_info['Age'] );
                                                $this->_context->set( 'zwms' , $user_info['ZWMS'] );

                                                $this->_context->set( 'image_count' , $image_count );
                                                //@todo 图片个数统计

                                                $this->_context->set( 'is_reg' , true );

                                                //一直退到 common
                                                while( $this->_context->get( 'circle' ) != 'common' ) {
                                                        $this->_context->exit_current_circle();
                                                }

                                                $this->_response = $this->_msg_producer->do_produce(
                                                        'text' ,
                                                        array( 'content' => Language_config::$bind_success )
                                                );
                                                return $this->_response;
                                        } else {
                                                //认证失败 提示用户名或者密码错误 并且推到
                                                //common circle

                                                //一直退到 common
                                                while( $this->_context->get( 'circle' ) != 'common' ) {
                                                        $this->_context->exit_current_circle();
                                                }

                                                $this->_response = $this->_msg_producer->do_produce(
                                                        'text' ,
                                                        array( 'content' => Language_config::$bind_auth_fail )
                                                );
                                                return $this->_response;
                                        }
                                }
                        }
                }
        }
}
