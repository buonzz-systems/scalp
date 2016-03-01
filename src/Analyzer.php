<?php namespace Buonzz\Scalp;

class Analyzer{

	// see https://github.com/JamesHeinrich/getID3/blob/master/structure.txt
	private $desired_properties = array('filesize','bitrate', 'fileformat', 'filename', 'mime_type', 'playtime_seconds', 'playtime_string', 'filepath', 'tags');


 public function analyze($filepath){
	 $getID3 = new \getID3;

    $fileInfo = $getID3->analyze($filepath);

    $info = array();
    
    foreach($this->desired_properties as $p)
    {
        if(isset($fileInfo[$p]))
            $info[$p] = utf8_encode($fileInfo[$p]);

        if(isset($fileInfo['video'])){
            $info['width'] = utf8_encode($fileInfo['video']['resolution_x']);
            $info['height'] = utf8_encode($fileInfo['video']['resolution_y']);
        }

        if(isset($fileInfo['jpg']))
            $info['exif'] = $this->utf8_converter($fileInfo['jpg']['exif']);
    }
    

    $data = json_encode($info);
    if(strlen($data) <=0)
        throw new \Exception(json_last_error_msg());

    return $data;

   }

   function utf8_converter($array)
    {
        array_walk_recursive($array, function(&$item, $key){
            if(!mb_detect_encoding($item, 'utf-8', true)){
                    $item = utf8_encode($item);
            }
        });
     
        return $array;
    }
}