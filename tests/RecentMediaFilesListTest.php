<?php 

use Buonzz\Scalp\RecentMediaFilesList;

class RecentMediaFilesListTest extends PHPUnit_Framework_TestCase{
	
  public function testSort(){

      $files = RecentMediaFilesList::get('/home/nfs/vol2/pics/12242017');
      //    	$this->assertTrue(is_object($var));
      var_dump($files);
  }
  
}