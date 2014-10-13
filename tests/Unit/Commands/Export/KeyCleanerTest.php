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

      $test_json = array('Thumbnail.FileType'=> '1',
                         'Thumbnail.MimeType'=> '1',
                         '[Content_Types].xml'=> '1',
                         '.rels' => 1);
      
      $this->assertTrue(KeyCleaner::clean(
        json_encode($test_json)) == 
      '{"Thumbnail_FileType":"1","Thumbnail_MimeType":"1","[Content_Types]_xml":"1","_rels":1}');
      unset($var);
  }
}