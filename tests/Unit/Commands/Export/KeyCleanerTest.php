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

      echo KeyCleaner::clean("Thumbnail.FileType") ."\r\n";
      echo KeyCleaner::clean("Thumbnail.MimeType") ."\r\n";
      echo KeyCleaner::clean("[Content_Types].xml") ."\r\n";
      echo KeyCleaner::clean(".rels");


      $this->assertTrue(KeyCleaner::clean("Thumbnail.FileType") == 'Thumbnail_FileType');
      $this->assertTrue(KeyCleaner::clean("Thumbnail.MimeType") == 'Thumbnail_MimeType');
      $this->assertTrue(KeyCleaner::clean("[Content_Types].xml") == 'Content_Types_xml');
      $this->assertTrue(KeyCleaner::clean(".rels") == '_rels');


      unset($var);
  }
}