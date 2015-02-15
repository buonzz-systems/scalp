<?php namespace Buonzz\Scalp\Commands\Export;

class FieldMapper{  
	public static function map($raw){

    	$metadata->filesize = $raw['filesize'];
		$metadata->mime_type = $raw['mime_type'];
		$metadata->encoding = $raw['encoding'];
		$metadata->fileformat = $raw['fileformat'];
		$metadata->playtime_seconds = $raw['playtime_seconds'];
		$metadata->bitrate = $raw['bitrate'];
		$metadata->resolution = array('width' => $raw['video']['resolution_x'],
									  'height' => $raw['video']['resolution_y']);

		$mdata = json_encode($metadata);

		return $mdata;        
    }
}