<?php
/**
 * 入口文件
 */
$BASE_PATH = dirname(__FILE__);

require_once( $BASE_PATH . '/lib/strategy.class.php' );
require_once( $BASE_PATH . '/lib/debug.class.php' );

$post_xml = @$GLOBALS["HTTP_RAW_POST_DATA"];

if( empty( $post_xml ) ){
        echo 'post xml is empty.';
        //die;
}

//记录请求信息
Debug::log( 'request.xml' , $post_xml );

try {
        //根据策略获取回复内容并显示之
        $strategy = new Strategy( $post_xml );
        $response_xml = $strategy->make_res();
        echo $response_xml;

        //记录回复信息
        Debug::log( 'response.xml' , $response_xml );

} catch( Exception $e ) {

        Debug::log( 'error.xml' , $e->getMessage() );
}


