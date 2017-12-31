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

                 // if this is been processed already, skip it.
                if(array_key_exists($info->file_contents_hash, $this->output_file_list))
                {
                    $this->output->writeln( "[ ". date("Y-m-d H:i:s") . " ]" . '<info> skipped "'
                            . $file->getPath() . "/" . $file->getFilename() .'</info>');
                    continue;
                }


            	$filename = $info->file_contents_hash . ".json";
	            $this->output->writeln( "[ ". date("Y-m-d H:i:s") . " ]" . '<comment>'. 
                        $file->getPath() . "/" . $file->getFilename() .  '</comment> metadata extracted');
	            file_put_contents($this->output_folder . '/'. $filename, $data);
        	}
            catch(\Exception $e){
                $this->output->writeln($e->getMessage());   
                continue; 
            }

        }

        return $this->output_file_list;

	} // generate
}