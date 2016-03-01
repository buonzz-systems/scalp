<?php namespace Buonzz\Scalp;

class MediaFilesList {

	public static function get($folder){

		$output = array();
		$it = new \RecursiveDirectoryIterator($folder);
		$display = array( 'jpeg', 'jpg', 'png', 'mov', 'mp4', 'mp3', 'mkv', 'flv');
		
		foreach(new \RecursiveIteratorIterator($it) as $file)
		{
			$f = explode('.', $file);
			$f = strtolower(array_pop($f));
	
		    if (in_array($f, $display))
		        $output[] = $file;
		}

		return $output;

	}

}