<?php namespace Buonzz\Scalp\Commands\Export;

class KeyCleaner{  
	public static function clean($value){
      $patterns = array();
      
      $patterns[0] = '/Thumbnail.FileType/';
      $patterns[1] = '/Thumbnail.MimeType/';
      $patterns[2] = '/\[Content_Types\]\.xml/';
      $patterns[3] = '/\.rels/';      
           

      $replacements = array();
      
      $replacements[0] = 'Thumbnail-FileType';
      $replacements[1] = 'Thumbnail-MimeType';
      $replacements[2] = 'Content_Types_xml';
      $replacements[3] = 'rels';

      return preg_replace($patterns, $replacements, $value);     
  }
}