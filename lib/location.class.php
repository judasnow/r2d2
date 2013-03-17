<?php
class Location {
        private $_city_list = array( '自贡' );
        private $_city_matrix = array();

        public function valid_city( $city_name ) {
                return in_array( $city_name , $this->_city_list );
        }
}
