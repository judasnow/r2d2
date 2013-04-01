<?php
/**
 * 封装 curl 操作
 */
class Curl {
        static public function post($url, array $post = NULL, array $options = array()) {
        //{{{
                if( !empty( $post['upload'] ) ) {
                        $post['upload'] = '@' . $file_full_path = dirname( __file__ ) . '/../temp/' . $post['upload'];
                }
                $defaults = array( 
                        CURLOPT_POST => 1, 
                        CURLOPT_HEADER => 0, 
                        CURLOPT_URL => $url, 
                        CURLOPT_FRESH_CONNECT => 1, 
                        CURLOPT_RETURNTRANSFER => 1, 
                        CURLOPT_FORBID_REUSE => 1, 
                        CURLOPT_TIMEOUT => 5, 
                        CURLOPT_POSTFIELDS => $post
                );

                $ch = curl_init(); 
                curl_setopt_array($ch, ($options + $defaults)); 
                if( !$result = curl_exec( $ch ) ) {
                        throw new Exception( 'curl error: ' . curl_error( $ch ) . ', and url is : ' . $url );
                } 
                curl_close($ch); 
                return $result;
        }//}}}

        static public function get($url, array $get = NULL, array $options = array() ){
        //{{{
                $defaults = array( 
                        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get), 
                        CURLOPT_HEADER => 0, 
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_TIMEOUT => 5
                ); 

                $ch = curl_init(); 
                curl_setopt_array($ch, ($options + $defaults)); 
                if( !$result = curl_exec( $ch ) ) { 
                        throw new Exception( 'curl error: ' . curl_error( $ch ) . ', and url is : ' . $url );
                } 
                curl_close($ch); 
                return $result; 
        }//}}}

        static public function download_file( $source_url , $target_file_name ) {
        //{{{
                if( empty( $source_url ) ) {
                        return false;
                } else {

                        if( empty( $target_file_name ) ) {
                                $target_file_name = date( 'Ymdhis' ) . '.jpg';
                        }

                        $curl = curl_init( $source_url );

                        curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
                        $image_data = curl_exec( $curl );
                        curl_close( $curl );

                        $tp = @fopen( dirname( __FILE__ ) . '/../temp/' . $target_file_name , 'a' );
                        fwrite( $tp , $image_data );
                        fclose( $tp );

                        return true;
                }
        }//}}}
}


