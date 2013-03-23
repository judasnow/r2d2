<?php
// debug 辅助函数
class Debug{
        public static function log( $file_name , $string ){
                $fp = fopen( $file_name , "a" );
                return fwrite( $fp , $string . "\n" );
        }
}
