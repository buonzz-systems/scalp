<?php 

use Buonzz\Scalp\Commands\Export\KeyCleaner;

class KeyCleanerTest extends PHPUnit_Framework_TestCase{
  public function testIsThereAnySyntaxError(){
    	$var = new KeyCleaner;
    	$this->assertTrue(is_object($var));
    	unset($var);
  }

  public function testClean(){
      $var = new KeyCleaner;
      $this->assertTrue(KeyCleaner::clean("Thumbnail.FileType") == 'Thumbnail-FileType');
      $this->assertTrue(KeyCleaner::clean("Thumbnail.MimeType") == 'Thumbnail-MimeType');
      unset($var);
  }
}