<?php namespace Buonzz\Scalp\Commands\Export;

use Buonzz\Scalp\ExcludedContents;
use Symfony\Component\Console\Helper\ProgressBar;

class MongoDBScriptGenerator{
	private $path;
	private $output;
	private $db;
	private $collection;
  private $output_file;

	public function __construct($path, $db, $collection, $output_file){
		$this->path = $path;		
		$this->db = $db;
		$this->collection = $collection;
    $this->output_file = $output_file;
	}

	public function generate($progressbar){
    $progressbar->setMessage('Export process started');

		$output = "conn = new Mongo(); \r\n";    
		$output .= "db = conn.getDB('".$this->db."'); \r\n";
		file_put_contents($this->output_file,$output);
    $progressbar->setMessage('analyzing files');
    $this->array_to_script($this->path, $progressbar);
	}

	private function array_to_script($path, $progressbar){
 
     $result = ''; 
     $cdir = scandir($path); 
       foreach ($cdir as $key => $value) 
       { 
          if (!in_array($value,ExcludedContents::get())) 
          { 

             if (is_dir($path . DIRECTORY_SEPARATOR . $value)) 
             { 
                $this->array_to_script($path . DIRECTORY_SEPARATOR . $value, $progressbar); 
             } 
             else 
             { 
                $this->get_meta($value, $path . DIRECTORY_SEPARATOR);
                $progressbar->setMessage($path . DIRECTORY_SEPARATOR. $value); 
             }
             $progressbar->advance();
          } 
       }       
             
       return $result; 
    }

    private function get_meta($item, $path){
    	$output = '';
    	$id3 = new \getID3;  


    	if(file_exists($path.$item) && is_file($path.$item))
    	{
    		$fileinfo = $id3->analyze($path.$item);
        
    		$metadata = KeyCleaner::clean(
                          \ForceUTF8\Encoding::fixUTF8(
                           json_encode($fileinfo)
                          ));

	    	$output = 'db.'.$this->collection.'.insert({';
  			$output .= '"file_name":"'. $item.'",';
  			$output .= '"metadata":'. $metadata .',';
  			$output .= '"path":"'. $path.'"';
  			$output .=  "});\r\n";
        file_put_contents($this->output_file,$output, FILE_APPEND);
  		}
  		return $output;
    }
}