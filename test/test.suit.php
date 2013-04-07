<?php
require_once( dirname(__FILE__) . '/simpletest/autorun.php' );

class AllTests extends TestSuite {
        function AllTests() {
                $this->TestSuite('All tests');
                $this->addFile('test.reg.php');
                $this->addFile('test.look_around.php');
                $this->addFile('test.search_by_age.php');
        }
}
