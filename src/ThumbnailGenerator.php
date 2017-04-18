<?php namespace Buonzz\Scalp;

class ThumbnailGenerator extends BaseGenerator{

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
                    $this->output->writeln( "[ ". date("Y-m-d H:i:s") . " ]" . '<info> skipped "'. $file->getPath() . "/" . $file->getFilename() .'</info>');
                    continue;
                }

                $ext = strtolower($file->getExtension());


                if($info->width > 500 || $info->height > 500)
                {
                    $this->output->writeln( "[ ". date("Y-m-d H:i:s") . " ]" . '<comment>'. $info->file_contents_hash . "." . $ext .  '</comment> resized');
                    
                    //thumbnail
                    $this->resize($file->getRealPath(), 10, $this->output_folder . '/'. $info->file_contents_hash . "-small." . $ext);

                    //medium
                    $this->resize($file->getRealPath(), 30, $this->output_folder . '/'. $info->file_contents_hash . "-med." . $ext);

                    //large
                    $this->resize($file->getRealPath(), 50, $this->output_folder . '/'. $info->file_contents_hash . "-large." . $ext);
                }
                else
                {
                    $this->output->writeln( "[ ". date("Y-m-d H:i:s") . " ]" . '<comment>'. $info->file_contents_hash . "." . $ext .  '</comment> retained original size');

                    copy(
                            $file->getRealPath(), 
                            $this->output_folder . '/'. $info->file_contents_hash . "-small." . $ext
                        );  

                    copy(
                            $file->getRealPath(), 
                            $this->output_folder . '/'. $info->file_contents_hash . "-medium." . $ext
                        ); 

                    copy(
                            $file->getRealPath(), 
                            $this->output_folder . '/'. $info->file_contents_hash . "-large." . $ext
                        );
                }

                $this->output_file_list[$info->file_contents_hash] = $info->filepath;

        	}
            catch(\Exception $e){
                $this->output->writeln( "[ ". date("Y-m-d H:i:s") . " ]" .  "skipped: ". $e->getMessage()) ;   
                continue; 
            }

        }

        return $this->output_file_list;

	} // generate

    private function resize($file, $percent, $target){
        $thumb = new \PHPThumb\GD($file);
        $thumb->resizePercent($percent);
        $thumb->save($target);
    }

}