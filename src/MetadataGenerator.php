<?php namespace Buonzz\Scalp;

use Buonzz\Scalp\Analyzer;
use Buonzz\Scalp\MediaFilesList;
use Buonzz\Scalp\ExcludedContents;

class MetadataGenerator{

	private $input_folder;
	private $output_folder;

	public function __construct($input_folder, $output_folder, $output){
		$this->input_folder = $input_folder;
		$this->output_folder = $output_folder;
		$this->output = $output;
	}

	public function generate(){

		$analyzer = new Analyzer();
		$files = MediaFilesList::get($this->input_folder);
		$output_file_list = [];

		if(file_exists($this->output_folder . '/'. "files.json"))
        {
            $output_file_list = json_decode(
                file_get_contents($this->output_folder . '/'. "files.json"), true);
        }

        foreach($files as $k=>$file)
        {
            
            try {
            	$data = $analyzer->analyze($file->getRealPath(),true);
            	$info = json_decode($data);
            	$filename = $info->file_contents_hash . ".json";
	            $this->output->writeln('<comment>'. $filename .  '</comment> metadata extracted');
	            file_put_contents($this->output_folder . '/'. $filename, $data);
        	}
            catch(\Exception $e){
                $this->output->writeln($e->getMessage());   
                continue; 
            }

        }

	} // generate
}