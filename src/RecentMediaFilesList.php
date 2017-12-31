<?php namespace Buonzz\Scalp;

class RecentMediaFilesList {

	public static function get($folder){
		$orig_files = MediaFilesList::get($folder);
		$items = [];

		foreach($orig_files as $file){
			$item[$file] = filemtime($file);
		}

		$sorted_array = arsort($items);
		return array_keys($sorted_array);
	}// get

}