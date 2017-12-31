<?php 

class RecentMediaFilesListTest extends PHPUnit_Framework_TestCase{
	
  public function testSort(){
    	$var = new Buonzz\Scalp\MediaFilesList;
    	$this->assertTrue(is_object($var));
    	unset($var);
  }
  
}