<?php
require_once( 'city_list.class.php' );

class Utility {

        static function full2half( $str ) {
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
        }

        public function valid_city( $city_name ) {
                return in_array( $city_name , City_list::$list );
        }
}
