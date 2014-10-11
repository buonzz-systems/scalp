<?php namespace Buonzz\Scalp\Commands\Export;

use Buonzz\Scalp\ExcludedContents;

class MongoDBScriptGenerator{
	private $path;
	private $output;
	private $db;
	private $collection;

	public function __construct($path, $db, $collection){
		$this->path = $path;		
		$this->db = $db;
		$this->collection = $collection;
	}

	public function generate(){		
		$this->output = "conn = new Mongo(); \r\n";
		$this->output .= "db = conn.getDB('".$this->db."'); \r\n";
		$this->output .= $this->array_to_script($this->path);
		return $this->output;
	}

	private function array_to_script($path){		
	   $result = ''; 
       $cdir = scandir($path); 
       foreach ($cdir as $key => $value) 
       { 
          if (!in_array($value,ExcludedContents::get())) 
          { 

             if (is_dir($path . DIRECTORY_SEPARATOR . $value)) 
             { 
                $result .= $this->array_to_script($path . DIRECTORY_SEPARATOR . $value); 
             } 
             else 
             { 
                $result .= $this->get_meta($value, $path . DIRECTORY_SEPARATOR); 
             } 
          } 
       } 
           
       return $result; 
    }

    private function get_meta($item, $path){
    	$output = '';
    	if(file_exists($path.$item) && is_file($path.$item))
    	{
	    	$output = 'db.'.$this->collection.'.insert({';
			$output .= '"file_name":"'. $item.'",';
			$output .= '"file_size":"'. filesize($path.$item) .'",';
			$output .= '"path":"'. $path.'"';
			$output .=  "});\r\n";
		}
		return $output;
    }
}