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
}