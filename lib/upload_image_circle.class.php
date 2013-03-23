<?php
/**
 * 处理用户上传图片
 */
require_once 'handler_base.class.php';

class Upload_image_circle_hander extends Handler_base {

        public function __construct( $post_obj ) {

                parent::__construct( $post_obj );
        }

        public function do_circle() {
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
}
