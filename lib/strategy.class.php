<?php
/**
 * 根据用户的输入 运行相关策略 返回相应的信息
 */
require_once( 'context.class.php' );
require_once( 'api.class.php' );
require_once( 'debug.class.php' );
require_once( 'store.class.php' );
require_once( 'msg_producer.class.php' );
require_once( 'utility.class.php' );

class Strategy {

        const SEARCH_COUNT_LIMIT_WITHOUT_REG = 3;
        const SEARCH_COUNT_LIMIT_AFTER_REG = 3;

        //由当前用户输入 xml 解析而来的对象
        private $_post_obj;

        //当前的上下文对象
        private $_context;

        //和 huaban123.com 进行通信的 api
        private $_api;

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

                $this->_msg_producer = new Msg_producer( 
                        array( 
                                'from'=>$this->_post_obj->ToUserName ,
                                'to'=>$this->_post_obj->FromUserName )
                        );
        }//}}}

        private function _location_handler() {
                $when_set_location_ok = '位置设置成功 1. 我是男,查看女; 2. 我是女,查看男; 3. 我是女,查看女 4. 我是男,查看男';
                $set_location_ok = false;

                //如果发送的是 location 信息
                if( $this->_request_msg_type == 'location' ) {
                        $label = $this->_post_obj->Label;
                        $x = $this->_post_obj->Location_x;
                        $y = $this->_post_obj->Location_y;

                        //注意其不为empty 而为 0
                        if( empty( $label ) || $label === 0 ) {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => '额 你发的地址没有中文标签啊 改用手动输入的吧' )
                                );
                        } else {
                                //从 label 中取出 地址信息
                                $city_name = preg_split( '/省|区|市/u' , $label )[0];
                                $set_location_ok = true;
                        }
                }

                //如果直接发送的地级市的名称
                if( $this->_request_msg_type == 'text' && preg_match( '/[\x{4e00}-\x{9fa5}]{1,5}/u' , $this->_request_content ) ) {
                        $city_name = preg_replace( "/[市|区|州]$/u" , "" , $this->_request_content );

                        //对照城市列表看是否有效
                        if( Utility::valid_city( $city_name ) ) {
                                $set_location_ok = true;
                        }
                }

                if( $set_location_ok == true ) {
                        $this->_context->set( 'location' , $city_name );
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => $when_set_location_ok )
                        );
                }
        }

        //根据当前 上下文信息 以及用户的输入信息 产生相应的结果
        public function make_res() {

                if( empty( $this->_post_obj ) ) {
                        return;
                }

                //获取当前 circle
                $circle = $this->_context->get( 'circle' );
                if( empty( $circle ) ) {
                        $circle = 'common';
                        $this->_context->set( 'circle' , 'common' );
                }
                
                //通用操作
                //{{{
                $request_content = $this->_request_content;
                if( $request_content == 'help' || $request_content == 'Hello2BizUser' ) {
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '1 输入"zc"进行注册,2 发送地址信息(或直接输入地级市名称),3 输入"C"进行条件查询,4 输入"h"更换查询对象的性别' )
                        );
                }

                //输入 SCZP 可以进行照片上传
                if( $request_content == 'sczp' ) {
                        $this->_context->set( 'circle' , 'uploading_image' );
                        $this->make_res();
                }

                if( $request_content == 'debug' ) {
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => "当前循环:$circle" )
                        );
                }

                //返回到上一级循环
                if( $request_content == 'q' ) {
                        $last_circle = $this->_context->get( 'last_circle' );
                        $circle_now = $this->_context->get( 'circle' );

                        if( empty( $last_circle ) ) {
                                $last_circle = 'common'; 
                        }
                        
                        if( $circle_now == 'common' ) {
                                //当前处于最外层循环
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => '不能再退了' )
                                );
                                return $this->_response;
                        }
                        
                        $this->_context->set( 'circle' , $last_circle );
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' , 
                                array( 'content' => '已经退出当前循环' )
                        );
                        return $this->_response;
                }
                //}}}

                //对于各种 circle 的判断
                //{{{
                if( $circle == 'common' ) {

                        $location = $this->_context->get( 'location' );
                        if( empty( $location ) ) {
                                //没有设置城市信息
                                $this->_location_handler();
                        }

                        $target_sex = $this->_context->get( 'target_sex' );
                        if( empty( $target_sex ) ) {
                                //默认作为查询条件的性别 为女
                                $target_sex = '女';
                                $this->_context->set( 'target_sex' , '女' );
                        }

                        //更换查询性别
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
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => "更换查询性别为:$target_sex" )
                                );
                                return $this->_response;
                        }

                        //进行注册
                        if( $this->_request_content == 'zc' ) {
                                //开始注册的提示消息
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => "请输入用户名" )
                                );
                                $this->_context->set( 'circle' , 'reg' );
                                $this->make_res();
                        }
                }

                //注册流程
                if( $circle == 'reg' ) {

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
                                                        $this->_response = $this->_msg_producer->do_produce( 
                                                                'text' , 
                                                                array( 'content' => "用户名输入成功,请输入昵称:" )
                                                        );
                                                        return $this->_response;
                                                }
                                        }
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
                                                        $this->_response = $this->_msg_producer->do_produce( 
                                                                'text' , 
                                                                array( 'content' => "昵称输入成功,请输入身高(公分cm):" )
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

                                if( $user_input_height >= 100 && $user_input_height <= 250 ) {
                                        //符合要求
                                        $this->_context->set( 'height' , $user_input_height );
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => '身高输入成功,请输入体重(斤)' )
                                        );
                                        return $this->_response;
                                } else {
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => "身高需要是 100-250 之间的数字" )
                                        );
                                        return $this->_response;
                                }
                        }//}}}

                        $weight = $this->_context->get( 'weight' );
                        if( empty( $weight ) ) {
                                //{{{
                                $user_input_weight = $this->_request_content;
                                if( $user_input_weight > 60 && $user_input_weight < 200 ) {
                                        $this->_context->set( 'weight' , $user_input_weight );
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' ,
                                                array( 'content' => '体重输入成功,请输入年龄' )
                                        );
                                        return $this->_response;
                                } else {
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => "体重需要是 60-200 之间的数字" )
                                        );
                                        return $this->_response;
                                }
                        }//}}}

                        $age = $this->_context->get( 'age' );
                        if( empty( $age ) ) {
                                //{{{
                                $user_input_age = $this->_request_content;

                                if( $user_input_age > 18 || $user_input_age < 60 ) {
                                        $this->_context->set( 'age' , $user_input_age );

                                        //手动进入 uploading_image circle
                                        $this->_context->set( 'circle' , 'uploading_image' );

                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' ,
                                                array( 'content' => '年龄输入成功,请上传照片' )
                                        );
                                        return $this->_response;
                                } else {
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => "年龄需要是 18-60 之间的数字" )
                                        );
                                        return $this->_response;
                                }
                        }//}}}

                        //填写交友宣言
                        $zwms = $this->_context->get( 'zwms' );
                        if( empty( $zwms ) ) {
                                $user_input_zwms = $this->_request_content;

                                if( !empty( $user_input_zwms  ) ) {
                                        //只要不为空
                                        $this->_context->set( 'zwms' , $user_input_zwms );

                                        //触发注册事件

                                } else {
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => '交友宣言不能为空' )
                                        );
                                        return $this->_response;
                                }
                        }
                }

                //上传照片的流程
                if( $circle == 'uploading_image' ) {
                        if( $this->_request_msg_type == 'image' ) {
                                $this->_contex->set( 'circle' , 'uploading_image' );

                                //获取 $url
                                $img_url = $this->_post_obj->PicUrl;

                                //下载到本地 temp
                                if( Curl::download_file( $img_url , "{$this->_post_obj->FromUserName}{$_SERVER['REQUEST_TIME']}.jpg" ) ) {
                                        //post 到 huaban123.com
                                        //$res = Curl::post( 
                                        //        'http://172.17.0.20:1979/action/WeixinMpApi.aspx?action=uploadImg' ,
                                        //        array( 'action'=>'uploadImg' , 'user_id'=>'534' , 'upload'=>'test.jpg' )
                                        //);
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => '上传成功，可以继续上传图片也可以 输入 "N" 退出' )
                                        );
                                        return $this->_response;
                                } else {
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => '上传失败' )
                                        );
                                        return $this->_response;
                                }
                        }
                }

                //搜索流程
                if( $circle == 'search' ) {
                        // 1 身高 2 体重 3 年龄 
                        if( $request_content == '1' ) {
                                //按身高进行查询
                                $this->_context->set( 'circle' , 'search_by_height' );
                                
                                //提示用户输入身高信息
                        }

                        if( $request_content == '2' ) {
                                //按体重进行查询
                        }

                        if( $request_content == '3' ) {
                                //按年龄进行查询
                        }
                }

                //按身高搜索的流程
                if( $circle == 'search_by_height' ) {
                        
                }

                //}}}                

                return $this->_response;
        }
}
