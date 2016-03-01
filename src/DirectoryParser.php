<?php namespace Buonzz\Scalp;

use Buonzz\Scalp\ExcludedContents;

class DirectoryParser{
    
    public static function directoryToArray($dir) {
         $result = array(); 
           $cdir = scandir($dir); 
           foreach ($cdir as $key => $value) 
           { 
              if (!in_array($value,ExcludedContents::get())) 
              { 
                 if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
                 { 
                    $result[$value] = self::directoryToArray($dir . DIRECTORY_SEPARATOR . $value); 
                 } 
                 else 
                 { 
                    $result[] = $value; 
                 } 
              } 
           } 
           
           return $result; 
    }
}