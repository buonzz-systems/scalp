<?php namespace Buonzz\Scalp\Commands\Export;

class KeyCleaner{  
	public static function clean($value){
      $patterns = array();
      
      $patterns[0] = '/Thumbnail.FileType/';
      $patterns[1] = '/Thumbnail.MimeType/';      

      $replacements = array();
      
      $replacements[0] = 'Thumbnail-FileType';
      $replacements[1] = 'Thumbnail-MimeType';

      return preg_replace($patterns, $replacements, $value);     
  }
}