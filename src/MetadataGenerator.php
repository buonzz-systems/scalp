<?php namespace Buonzz\Scalp;

class MetadataGenerator extends BaseGenerator{

	public function __construct($input_folder, $output_folder, $output){
        parent::__construct($input_folder, $output_folder, $output);
	}

	public function generate(){

        parent::generate();

        foreach($this->files as $k=>$file)
        {
            
            try {
            	$data = $this->analyzer->analyze($file->getRealPath(),true);
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