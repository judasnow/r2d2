<?php
$ruler[] = array( 'text!ping' => 'text!pong' );
$ruler[] = array( 'text!Hello2BizUser' => 'text![微笑]' );
$ruler[] = array( 'text!zc' => 'circle!reg' );
//地址信息
//{{{
//直接输入城市名称 1-5 个汉字 算上最后有市字的情况
$ruler[] = array( 're!/^[\x{4e00}-\x{9fa5}]{1,5}$/u' => 'handler!location' );
//输入 location 类型的信息
$ruler[] = array( 'type!location' => 'handler!location' );
//}}}
