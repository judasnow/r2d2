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

require_once 'reg_circle_handler.class.php';

class Strategy {

        const SEARCH_COUNT_LIMIT_WITHOUT_REG = 3;
        const SEARCH_COUNT_LIMIT_AFTER_REG = 3;

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
                $this->_request_content = $this->_post_obj->Content;

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
                if( empty( $circle ) ) {
                        $circle = 'common';
                        $this->_context->set( 'circle' , 'common' );
                }

                //通用操作
                //{{{
                //输入 help 显示帮助信息
                $request_content = $this->_request_content;
                if( strtolower( $request_content ) == 'help' || $request_content == 'Hello2BizUser' ) {
                        $this->_response = $this->_msg_producer->do_produce( 
                                'text' ,
                                array( 'content' => '你可以: 1 输入"zc"进行注册,2 发送地址信息(或直接输入地级市名称),3 输入"C"进行条件查询,4 输入"h"更换查询对象的性别' )
                        );
                        return $this->_response;
                }

                //输入 q 返回到上一级循环
                if( strtolower( $request_content ) == 'q' ) {
                        if( $this->_context->exit_current_circle() ) {
                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' , 
                                        array( 'content' => '退出成功' )
                                );
                        } else {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => '不能再退了' )
                                );
                        }
                }

                //输入 zc 进入注册流程
                if( strtolower( $request_content ) == 'zc' ) {
                        if( $circle != 'reg' ) {
                                $this->_context->set( 'circle' , 'reg' );
                                $reg_circle_handelr = new Reg_circle_handler( $this->_post_obj );
                                $responce_content = $reg_circle_handelr->produce_msg_by_reg_step();

                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => $responce_content )
                                );
                                return $this->_response;
                        }
                }

                //输入 s 进入 search 流程
                if( strtolower( $request_content ) == 's' ) {
                        if( $circle != 'search' ) {
                                $this->_context->set( 'circle' , 'search' );
                                return $this->make_res();
                        }
                }

                //输入 sczp 可以进行照片上传
                if( strtolower( $request_content ) == 'sczp' ) {
                        if( $circle != 'uploading_image' ) {
                                $this->_context->set( 'circle' , 'uploading_image' );
                                return $this->make_res();
                        }
                }

                if( strtolower( $request_content ) == 'debug' ) {
                        $this->_response = $this->_msg_producer->do_produce(
                                'text' ,
                                array( 'content' => "当前循环:$circle" )
                        );
                        return $this->_response;
                }
                //}}}

                //对于各种 circle 的判断

                //最外层的流程
                if( $circle == 'common' ) {
                        //{{{
                        $location = $this->_context->get( 'location' );
                        if( empty( $location ) ) {
                                //没有设置城市信息
                                $this->_context->set( 'circle' , 'location' );
                                $this->make_res();
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
                }//}}}

                /**
                 * 输入地址信息流程
                 * 可以有两种选择
                 * 1    发送 location 类型的信息
                 * 2    由于有些 手机/版本 没有这个功能 或 location 类型的信息中 label 信息为空
                 *      所以也可以选择发送 地级市的名称 ( 有没有最后的 市|州 均可)
                 */
                if( $circle == 'location' ) {
                //{{{
                        $when_set_location_ok = '位置设置成功';

                        //如果发送的是 location 类型的信息
                        if( $this->_request_msg_type == 'location' ) {
                                $label = $this->_post_obj->Label;
                                $x = $this->_post_obj->Location_x;
                                $y = $this->_post_obj->Location_y;

                                //注意其不为empty 而为 0
                                //@todo 返回的对象很奇葩 是一个数组
                                if( empty( $label[0] ) ) {
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' ,
                                                array( 'content' => '额 你发的地址没有中文标签啊,手动输入地级市名称吧.' )
                                        );
                                        return $this->_response;
                                } else {
                                        //从 label 中取出 地址信息
                                        //获取地址信息成功
                                        $city_name = preg_split( '/省|区|市/u' , $label )[0];
                                        $this->_context->set( 'location' , $city_name );
                                        $this->_context->exit_current_circle();
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' ,
                                                array( 'content' => $when_set_location_ok )
                                        );
                                        return $this->_response;
                                }
                        }

                        //如果直接发送的地级市的名称
                        if( $this->_request_msg_type == 'text' &&
                                preg_match( '/[\x{4e00}-\x{9fa5}]{1,5}/u' , $this->_request_content ) && 
                                Utility::valid_city( $this->_request_content )
                        ) {
                                //获取地址信息成功
                                $this->_context->set( 'location' , $this->_request_content );
                                $this->_context->exit_current_circle();
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => $when_set_location_ok )
                                );
                                return $this->_response;
                        } else {
                                //视之为无效信息
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => '输入的地址无效' )
                                );
                                return $this->_response;
                        }
                }//}}}

                //注册流程
                if( $circle == 'reg' ) {
                        $reg_circle_handler = new Reg_circle_handler( $this->_post_obj );
                        return $reg_circle_handler->do_circle();
                }

                //上传照片的流程
                if( $circle == 'uploading_image' ) {
                        if( $this->_request_msg_type == 'image' ) {
                                $this->_context->set( 'circle' , 'uploading_image' );

                                //获取 $url
                                $img_url = $this->_post_obj->PicUrl;
                                $image_name = "{$this->_post_obj->FromUserName}{$_SERVER['REQUEST_TIME']}.jpg";

                                //下载到本地 temp
                                if( Curl::download_file( $img_url , $image_name ) ) {
                                        //post 到 huaban123.com
                                        //$res_json = Curl::post( 
                                        //        'http://172.17.0.20:1979/action/WeixinMpApi.aspx?action=uploadImg' ,
                                        //        array( 'action'=>'uploadImg' , 'user_id'=>'534' , 'upload'=>$image_name )
                                        //);
                                        $this->_response = $this->_msg_producer->do_produce(
                                                'text' , 
                                                array( 'content' => '上传成功，可以继续上传图片也可以 输入 "q" 退出' )
                                        );
                                        return $this->_response;
                                } else {
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'text' , 
                                                array( 'content' => '上传失败' )
                                        );
                                        return $this->_response;
                                }
                        } else {
                                //所有的其他行为均被视为无效行为
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => '上传失败,确定您上传的是"图片"哦亲.可以继续上传图片也可以 输入 "q" 退出' )
                                );
                                return $this->_response;
                        }
                }

                //搜索流程
                if( $circle == 'search' ) {
                        // 1 身高 2 体重 3 年龄 
                        if( $request_content == '1' ) {
                                //进入身高查询流程
                                $this->_context->set( 'circle' , 'search_by_height' );

                                //提示用户输入身高信息
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' , 
                                        array( 'content' => '请输入身高信息:' )
                                );
                                return $this->_response;
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
                        if( $request_content > 140 && $request_content < 250 ) {
                                //调用 api 使用身高 目标性别 以及当前的地址信息进行查询
                                $location = $this->_context->get( 'location' );
                                $target_sex = $this->_context->get( 'target_sex' );
                                $height = $request_content;

                                //注意 web 版本上的奇葩变量名称
                                $res_json = $this->_api->search( array( 'height' => $height , 'location' => $location , 'sex' => $target_sex ) );
                                $res = json_decode( $res_json , true );

                                if( $res['type'] == 'success' ) {
                                        $user_infos = json_decode( $res['info'] , true );
                                        //获取当前 cursor
                                        $cursor = $this->_context->get( 'search_by_height_cursor' );
                                        if( empty( $cursor ) || $cursor >= count( $user_infos ) ) {
                                                $cursor = 0;
                                        }
                                        //作为结果返回
                                        $search_result_user_info = $user_infos[$cursor];
                                        $cursor++;

                                        $items = array(
                                                array( 
                                                        'title'=>$search_result_user_info['NickName'] , 
                                                        'description'=>$search_result_user_info['ZWMS'] , 
                                                        'pic_url'=>'http://huaban123.com/UploadFiles/UHP/MIN/' . $search_result_user_info['HeadPic'] , 
                                                        'url'=>'http://www.huaban123.com/Action/WeixinUserInfoDetail.aspx?user_id=' . $search_result_user_info['UserId']
                                                )
                                        );
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'news' , 
                                                array( 'items' => $items )
                                        );
                                        return $this->_response;
                                }
                        }
                        }

                        //非法输入 或者无任何匹配信息 返回一条笑话
                        return $this->_response;
        }
}
