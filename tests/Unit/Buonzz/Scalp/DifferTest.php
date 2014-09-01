<?php 

class DifferTest extends PHPUnit_Framework_TestCase{
  public function testIsThereAnySyntaxError(){
    	$var = new Buonzz\Scalp\Differ;
    	$this->assertTrue(is_object($var));
    	unset($var);
  }

  public function testSetRepo(){
      $dfr = new Buonzz\Scalp\Differ;
      $dfr->setRepo('/var/share/darwin/scalp');
      unset($dfr);
  }

  public function testCompare(){
     $dfr = new Buonzz\Scalp\Differ;
     $dfr->setRepo('/var/share/darwin/scalp');
     $output = $dfr->compare('a41b4b8fe760ab175bbebfbb4d799d6374a67fdb');     
     $this->assertTrue(count($output["upload"])>=0);   
  }   
}