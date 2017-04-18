<?php namespace Buonzz\Scalp;

use Buonzz\Scalp\Analyzer;
use Buonzz\Scalp\MediaFilesList;
use Buonzz\Scalp\ExcludedContents;

class BaseGenerator{

	protected $input_folder;
	protected $output_folder;
	protected $output_file_list;
	protected $files;
	protected $analyzer;

	public function __construct($input_folder, $output_folder, $output){
		$this->input_folder = $input_folder;
		$this->output_folder = $output_folder;
		$this->output = $output;
	}

	public function generate(){

		$this->analyzer = new Analyzer();
		$this->files = MediaFilesList::get($this->input_folder);
		$this->output_file_list = [];

		if(file_exists($this->output_folder . '/'. "files.json"))
        {
            $this->output_file_list = json_decode(
                file_get_contents($this->output_folder . '/'. "files.json"), true);
        }

	}

}