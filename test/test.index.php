<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );

class Test_of_index extends UnitTestCase {

        function setUp(){

        }

        function tearDown(){
                
        }

        function test_index_with_ping() {
                $GLOBALS["HTTP_RAW_POST_DATA"] = "<xml><ToUserName><![CDATA[gh_cbb742f45d8f]]></ToUserName>
<FromUserName><![CDATA[oJenljo-kzzUDI8SK0fcNfFoFlQk]]></FromUserName>
<CreateTime>1363413080</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[p]]></Content>
<MsgId>5855814589538632748</MsgId>
</xml>";
                require_once( dirname(__FILE__) . '/../index.php' );
        }
}

