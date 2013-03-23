<?php
class Config {
        static public $store_server = 'tcp://172.17.0.46:6379/';
        static public $huaban123_server = 'http://172.17.0.20:1979/';

        //没有注册时最多查询次数 注意是从 0 开始的
        static public $max_search_count_without_reg = 2;

        //注册之后最多查询次数
        static public $max_search_count_with_reg = 5;
}
