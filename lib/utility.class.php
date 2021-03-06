<?php
/**
 * 封装了一些常用的功能
 */

require_once( 'city_list.class.php' );

class Utility {
        /**
         * 全角到半角的转换
         */
        static public function full2half( $str ) {
        //{{{
                $arr = array(
                        //数字  
                        '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4', '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',   
                        //大写字母  
                        'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E', 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J', 
                        'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O', 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T', 
                        'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y', 'Ｚ' => 'Z',  
                        //小写字母  
                        'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd', 'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j', 
                        'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n', 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't', 
                        'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x', 'ｙ' => 'y', 'ｚ' => 'z',  
                        //括号  
                        '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[', '】' => ']', '〖' => '[', '〗' => ']', '《' => ' < ','》' => ' > ','｛' => ' {', '｝' => '} ',  
                        //其它
                        '％' => '%', '＋' => ' + ', '—' => '-', '－' => '-', '～' => '-','．'=>'.','：' => ':', '。' => '.', '，' => ',', '、' => '\\', '；' => ':', '？' => '?', '！' => '!',
                        '…' => '-', '‖' => '|','“' => "\"", '”' => "\"", '‘' => '`','’' => '`', '｜' => '|', '〃' => "\"",'　' => ' ' 
                );
                return strtr( $str , $arr );
        }//}}}

        /**
         * 判断用户输入的城市信息是否有效
         */
        static public function valid_city( $city_name ) {
        //{{{
                $city_name = rtrim( $city_name , '市' );
                if( in_array( $city_name , City_list::$list ) ) {
                        return $city_name;
                } else { 
                        $city_name = rtrim( '州' );
                        if( in_array( $city_name , City_list::$list ) ) {
                                return $city_name;
                        }
                }
                return false;
        }//}}}

        //根据用户输入的市名获取 areaId (和web系统对应)
        static public function get_area_id( $city_name ) {
                if( isset( City_list::$list_with_id[$city_name] ) ) {
                        $id = City_list::$list_with_id[$city_name];
                        return $id;
                }
                return false;
        }

        /**
         * 对用户的输入进行处理
         */
        static public function format_user_input( $input ) {
                return trim(
                        strtolower(
                                Utility::full2half(
                                        $input
                                )
                        ) 
                );
        }
}
