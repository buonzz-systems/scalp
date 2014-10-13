<?php namespace Buonzz\Scalp\Commands\Export;

class KeyCleaner{  
	public static function clean($value){
            return preg_replace_callback('/"(\S*\.[a-zA-Z]*)":/i', function($matches){
                  return str_replace('.','_',$matches[0]);                  
      }, $value);     
  }
}