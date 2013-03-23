<?php
require_once 'handler_base.class.php';

class Reg_circle_handler extends Handler_base {

        private $_response;

        /**
         * 对应条目成功输入时 返回的消息
         */
        private $_input_success_message = array(
                'just_begin' => '欢迎注册，请在下列 4 个选项中选择一个与您相符的，输入列表编号。1 我是男，查看女。2 我是男，查看男。3 我是女，查看男。4 我是女，查看女。',
                'sex_and_target_sex_index_without_location' => '性别以及取向输入成功,请输入你所在的城市',
                'sex_and_target_sex_index_with_location' => '性别以及取向输入成功，请输入一个用户名，可以由字母数字以及下划线组成',
                'location' => '城市信息输入成功，请输入一个用户名，可以由字母数字以及下划线组成',
                'username' => '用户名输入成功，请输入昵称',
                'nickname' => '昵称输入成功，请输入身高（公分cm）',
                'height' => '身高输入成功，请输入体重（斤）',
                'weight' => '体重输入成功，请输入年龄',
                'age' => '年龄输入成功，输入 qq 号',
                'qq' => 'qq 输入成功，请填写交友宣言',
                'zwms' => '宣言输入成功，上传几张照片吧 （第一张将作为您的头像）'
        );

        /**
         * 重新返回 reg circle 根据用户以前所在位置返回的信息
         */
        private $_when_user_back = array(
                'just_begin' => '欢迎注册，请在下列 4 个选项中选择一个与您相符的，输入列表编号。1 我是男，查看女。2 我是男，查看男。3 我是女，查看男。4 我是女，查看女。',
                'sex_and_target_sex_index' => '欢迎注册，请在下列 4 个选项中选择一个与您相符的，输入列表编号。1 我是男，查看女。2 我是男，查看男。3 我是女，查看男。4 我是女，查看女。',
                'location' => '上次注册时，性别以及取向已经输入成功了，请输入一个用户名，可以由字母数字以及下划线组成',
                'username' => '上次注册时，城市信息已经输入成功了，请输入一个用户名，可以由字母数字以及下划线组成',
                'nickname' => '上次注册时，用户名已经输入成功了，请输入昵称',
                'height' => '上次注册时，昵称已经输入成功了，请输入身高（公分cm）',
                'weight' => '上次注册时，身高已经输入成功了，请输入体重（斤）',
                'age' => '上次注册时，体重已经输入成功了，请输入年龄',
                'qq' => '上次注册时，年龄已经输入成功了，输入 qq 号',
                'zwms' => '上次注册时，qq 已经输入成功了，请填写交友宣言',
                'upload_image' => '上次注册时，宣言已经输入成功了，作为注册的最后一步，上传几张照片吧 （第一张将作为您的头像）'
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
                        //需要定位到 next_step 的上一步
                        
                        $msg = $this->_when_user_back[$next_step];
                } else {
                        $msg = $this->_when_user_back['just_begin'];
                }

                return $msg;
        }

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
                                $this->_context->set( 'reg_next_step' , 'location' );

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
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => $this->_input_success_message['sex_and_target_sex_index_without_location'] )
                                        );
                                } else {
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => $this->_input_success_message['sex_and_target_sex_index_with_location'] )
                                        );
                                }
                                return $this->_response;
                        } else {
                                //下一步还是当前步骤
                                $this->_context->set( 'reg_next_step' , 'sex_and_target_sex_index' );
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => '请按照提示输入每一个选项前面的数字，（1 或 2 或 3 或 4）' )
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
                                        array( 'content' => $this->_input_success_message['location'] )
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
                } else {
                        //已经设置了 location 信息,直接跳过这一步
                        $this->_context->set( 'reg_next_step' , 'username' );
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
                                $this->_context->set( 'reg_next_step' , 'qq' );

                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => $this->_input_success_message['age'] )
                                );
                                return $this->_response;
                        }
                }//}}}

                $qq = $this->_context->get( 'qq' );
                if( empty( $qq ) ) {
                //{{{
                        $user_input_qq = (int)$this->_request_content;
                        if( !is_numeric( $user_input_qq ) || $user_input_qq < 999999 ) {
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' , 
                                        array( 'content' => 'qq 号的格式不对哦 亲' )
                                );
                                return $this->_response;
                        } else {
                                $this->_context->set( 'qq' , $user_input_qq );
                                $this->_context->set( 'reg_next_step' , 'zwms' );

                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' ,
                                        array( 'content' => $this->_input_success_message['qq'] )
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
                                        $this->_context->set( 'is_reg' , 'true' );
                                        //到这里已经注册成功 但是
                                        //还需要进入 upload_image circle 至少要上传一张照片才行
                                        $upload_image_circle_handler = new Upload_image_circle_handler( $this->_post_obj );

                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => '注册完毕~ 密码是 "huaban123"，欢迎到我们的网站逛逛: http://huaban123.com。 输入 "s" 可以按条件查找你附近的人。' )
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
                                        array( 'content' => '交友宣言不能为空啊' )
                                );
                                return $this->_response;
                        }
                }//}}}

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
                                'qq' => $this->_context->get( 'qq' )
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
