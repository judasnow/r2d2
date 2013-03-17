<?php
/**
 * 封装 curl 操作
 */
class Curl{

        static public function post($url, array $post = NULL, array $options = array()) {
                $defaults = array( 
                        CURLOPT_POST => 1, 
                        CURLOPT_HEADER => 0, 
                        CURLOPT_URL => $url, 
                        CURLOPT_FRESH_CONNECT => 1, 
                        CURLOPT_RETURNTRANSFER => 1, 
                        CURLOPT_FORBID_REUSE => 1, 
                        CURLOPT_TIMEOUT => 4, 
                        CURLOPT_POSTFIELDS => http_build_query($post) 
                ); 

                $ch = curl_init(); 
                curl_setopt_array($ch, ($options + $defaults)); 
                if( ! $result = curl_exec($ch)) { 
                        echo curl_error($ch); 
                } 
                curl_close($ch); 
                return $result; 
        }

        static public function get($url, array $get = NULL, array $options = array() ){
                $defaults = array( 
                        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get), 
                        CURLOPT_HEADER => 0, 
                        CURLOPT_RETURNTRANSFER => TRUE, 
                        CURLOPT_TIMEOUT => 4 
                ); 

                $ch = curl_init(); 
                curl_setopt_array($ch, ($options + $defaults)); 
                if( ! $result = curl_exec($ch)) 
                { 
                        trigger_error(curl_error($ch)); 
                } 
                curl_close($ch); 
                return $result; 
        }
}


