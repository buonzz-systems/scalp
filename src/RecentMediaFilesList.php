<?php namespace Buonzz\Scalp;

class RecentMediaFilesList {

	public static function get($folder){
		$orig_files = MediaFilesList::get($folder);
		$items1 = [];
		$items2 = [];

		foreach($orig_files as $file1)
		{
			$items1[md5($file1)] = $file1;
		}

		foreach($orig_files as $file2){
			$items2[md5($file2)] = filemtime($file2);
		}

		$sorted_array = arsort($items2);
		$sorted_keys =  array_keys($sorted_array);

		$output = [];

		foreach($sorted_keys as $key){
			$output[] = $items1[$key];
		}

		return $output;
	}// get

}