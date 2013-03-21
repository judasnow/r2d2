<?php
//通过发送给 strategy 不同的已经解析好的 xml 文件
//测试其是否按预期返回内容
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );
require( dirname(__FILE__) . '/../lib/strategy.class.php' );

class Test_of_strategy extends UnitTestCase{

        //普通的 xml 请求
        const TEXT_XML = '<xml><ToUserName><![CDATA[gh3_cbb742f45d8f]]></ToUserName>
                <FromUserName><![CDATA[oJenljo3-kzzUDI8SK0fcNfFoFlQk]]></FromUserName>
                <CreateTime>1363395638</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <MsgId>5855739676719055765</MsgId>
                </xml>';

        //图片请求
        const IMAGE_XML = '<xml><ToUserName><![CDATA[gh_cbb742f45d8f]]></ToUserName>
<FromUserName><![CDATA[oJenljo-kzzUDI8SK0fcNfFoFlQk]]></FromUserName>
<CreateTime>1363799720</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<PicUrl><![CDATA[http://mmsns.qpic.cn/mmsns/8GtV9x92iasGVARp3U9FWQz1JzasqMNiausANniciaiaFR0EmsOeQTeKZRQ/0]]></PicUrl>
<MsgId>5857475195693958630</MsgId>
</xml>';

        //位置信息
        const LOCATION_XML = '<xml><ToUserName><![CDATA[gh3_cbb742f45d8f]]></ToUserName>
                <FromUserName><![CDATA[oJenljo3-kzzUDI8SK0fcNfFoFlQk]]></FromUserName>
                <CreateTime>1362624002</CreateTime>
                <MsgType><![CDATA[location]]></MsgType>
                <Location_X>29.353037</Location_X>
                <Location_Y>104.770287</Location_Y>
                <Scale>20</Scale>
                <Label><![CDATA[%s]]></Label>
                <MsgId>5852425525334639049</MsgId>
                </xml>';

        public function setUp(){
                $this->_strategy = new Strategy( self::TEXT_XML );
                $this->_context = new Context( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk' );
        }

        public function tearDown(){
                $this->_context->del( 'oJenljo3-kzzUDI8SK0fcNfFoFlQk' );
        }

        //测试普通文本回复信息 ping->pong
        public function _test_make_res_simple(){
                //初始状态用户应该处于 common circle
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'help' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
        }

        //测试 h 命令
        public function _test_h(){
                $this->_context->set( 'target_sex' , '男' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'h' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //断言为女
                $target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $target_sex , '女' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'h' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $target_sex = $this->_context->get( 'target_sex' );
                $this->assertTrue( $target_sex , '男' );
        }

        //测试用户尝试输入地址信息的情况
        public function _test_input_location_info(){
                //输入汉字地名 最常规的情况
                $strategy = new Strategy( sprintf( self::TEXT_XML , '自贡市' ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $post_obj->Content;
                $this->assertTrue( $post_obj->Content == '位置设置 ok' );
        }

        public function _test_input_location_by_location(){
                $this->_context->set( 'circle' , 'common' );
                //输入location的情况
                $strategy = new Strategy( sprintf( self::LOCATION_XML , "中国四川省自贡市自流井区新民街" ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $location = $this->_context->get( 'location' );
                $this->assertTrue( $location == '自贡' );

                //设置 1 2 3 4
                $strategy = new Strategy( sprintf( self::TEXT_XML , "1" ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $post_obj->Content;
                echo $sex = $this->_context->get( 'sex' );
                echo $ori = $this->_context->get( 'orientation' );
                $this->assertTrue( !empty( $sex ) && !empty( $ori ) );
                $circle = $this->_context->get( 'circle' );
                $this->assertTrue( $circle == 'search' );

                //搜索年龄
                $strategy = new Strategy( sprintf( self::TEXT_XML , "19" ) );
                //$post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $post_obj->Content;
                var_dump( $strategy->make_res() );
        }

        //label 为空的情况
        public function _test_input_location_by_location_with_empty_lable(){
                //输入location的情况
                $strategy = new Strategy( sprintf( self::LOCATION_XML , "" ) );
                $post_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $post_obj->Content;
        }

        //测试 由 common 进入 circle  zc->circle:reg
        public function _test_make_res_when_need_goto_a_new_circle(){
                $this->_context->set( 'circle' , 'common' );
                //断言之前的 circle 因该是 connon 
                $this->assertTrue( $this->_context->get( 'circle' ) == 'common' );
                //用户进入 注册流程
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'zc' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $res_obj->Content;

                //断言当前 circle = reg
                $context = new Context( 'oJenljo-kzzUDI8SK0fcNfFoFlQk' );
                $circle = $context->get( 'circle' );
                $this->assertTrue( $circle == 'reg' );
        }

        //测试用户在 zc 循环下输入 username 
        function test_user_input_username() {
                $this->_context->set( 'circle' , 'reg' );
                //数据库中没有的名字
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'uuuuuuutest' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $res_obj->Content;
        }

        function _test_user_input_username_when_the_username_is_not_avaliable() {
                $this->_context->set( 'circle' , 'reg' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'judas' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $res_obj->Content;
        }

        //测试输入身高
        function _test_input_height(){
                $this->_context->set( 'circle' , 'reg' );
                $this->_context->set( 'username' , 'reg' );
                $this->_context->set( 'nickname' , 'reg' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , '1`50' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $res_obj->Content;
        }

        function test_upload_img(){
                $this->_context->set( 'circle' , 'reg' );
                $this->_context->set( 'username' , 'reg' );
                $this->_context->set( 'nickname' , 'reg' );
                $this->_context->set( 'age' , '18' );
                $this->_context->set( 'weight' , '18' );
                $this->_context->set( 'height' , '18' );
                $strategy = new Strategy( self::IMAGE_XML  );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                //echo $res_obj->Content;
        }

        //测试 q 操作
        function _test_q(){
                $this->_context->set( 'circle' , 'reg' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'q' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $circle = $this->_context->get( 'circle' );

                $this->assertTrue( $circle == 'common' );
        }

        //测试注册流程
        function _test_input_username(){
                //输入了一个已经存在了的用户名
                $this->_context->set( 'circle' , 'reg' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'admin' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $res_obj->Content == '此用户名太受欢迎，已有人抢注啦！换一个吧，亲！' );

                //测试没有被注册的情况
                $this->_context->set( 'circle' , 'reg' );
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'test' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $res_obj->Content == '请输入昵称' );
                //断言 context 中已经保存 username 信息
                $username = $this->_context->get( 'username' );
                $this->assertTrue( $username == 'test' );

                //输入昵称之后 断言提示输入年龄
                $strategy = new Strategy( sprintf( self::TEXT_XML , 'test2' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                echo $res_obj->Content;
                $this->assertTrue( $res_obj->Content == '请输入年龄' );

                //输入年龄之后 断言提示输入qq
                $strategy = new Strategy( sprintf( self::TEXT_XML , '22' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $res_obj->Content == '请输入QQ' );

                //输入qq之后 断言提示输入身高
                $strategy = new Strategy( sprintf( self::TEXT_XML , '222222' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $res_obj->Content == '请输入身高' );

                //输入身高之后 断言提示输入体重
                $strategy = new Strategy( sprintf( self::TEXT_XML , '165' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $res_obj->Content == '请输入体重' );

                //输入体重之后 断言提示上传照片
                $strategy = new Strategy( sprintf( self::TEXT_XML , '45' ) );
                $res_obj = simplexml_load_string( $strategy->make_res() , "SimpleXMLElement" , LIBXML_NOCDATA );
                $this->assertTrue( $res_obj->Content == '是否上传照片' );
        }
}
