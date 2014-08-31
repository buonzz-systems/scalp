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
     $output = $dfr->compare('c6c202d7703d4ba2727e96b6156297793fc7388d');
     var_dump($output);
  }   
}