<?php
/**
 * 处理用户上传图片
 */
require_once 'handler_base.class.php';
require_once 'config.class.php';

class Upload_image_circle_hander extends Handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        public function do_circle() {
                if( $this->_request_msg_type == 'image' ) {
                        //获取 $url
                        $img_url = $this->_post_obj->PicUrl;
                        $image_name = "{$this->_post_obj->FromUserName}{$_SERVER['REQUEST_TIME']}.jpg";

                        //下载到本地 temp
                        if( Curl::download_file( $img_url , $image_name ) ) {

                                //被下载图片的完整路径
                                $image_full_path = dirname( __FILE__ ) . "/../temp/$image_name";

                                $user_id = $this->_context->get( 'user_id' );
                                //尝试 post 到 huaban123.com
                                $res_json = Curl::post(
                                        Config::$huaban123_server . '/action/WeixinMpApi.aspx?action=uploadImg' ,
                                        array( 'action'=>'uploadImg' , 'user_id'=>$user_id , 'upload'=>$image_full_path )
                                );
                                $res = json_decode( $res_json , true );
                                if( $res['type'] == false ) {
                                        //上传失败了
                                }

                                //设置一个标志位 标志用户已经上传的照片的张数
                                $image_count = $this->_context->get( 'image_count' );
                                if( empty( $image_count ) || !is_numeric( $image_count ) ) {
                                        $this->_context->set( 'image_count' , 0 );
                                        $image_count = 0;
                                }
                                //上传成功 对于照片的数量执行加 1 操作
                                $this->_context->incr( 'image_count' );

                                $this->_response = $this->_msg_producer->do_produce(
                                        'text' , 
                                        array( 'content' => '上传成功，可以继续上传图片也可以 输入 "q" 退出' )
                                );
                                return $this->_response;
                        } else {
                                $this->_response = $this->_msg_producer->do_produce( 
                                        'text' ,
                                        array( 'content' => '上传失败，可以请稍后再试一试。' )
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
}
