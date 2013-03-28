<?php
// debug 辅助函数
class Debug{
        public static function log( $file_name , $string ){
                $fp = fopen( $file_name , "a" );
                return fwrite( $fp , date( 'Y-m-d H:i:s' , $_SERVER['REQUEST_TIME'] ) . ' : '  . $string . "\n" );
        }
}
