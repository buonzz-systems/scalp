<?php namespace Buonzz\Scalp;

class Analyzer{

	// see https://github.com/JamesHeinrich/getID3/blob/master/structure.txt
	private $desired_properties = array('filesize','bitrate', 'fileformat', 'filename', 'mime_type', 'playtime_seconds', 'playtime_string', 'filepath', 'tags');


 public function analyze($filepath, $json_output = FALSE){
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

        if(isset($fileInfo['jpg']) && isset($fileInfo['jpg']['exif']))
        {   
             $info['exif'] = $this->utf8_converter($fileInfo['jpg']['exif']);
             //var_dump($fileInfo['jpg']['exif']['EXIF']);
             //die();
        }

    }

    $info['date_indexed'] = time();
    

    if($json_output)
    {

        $data = json_encode($info);
        if(strlen($data) <=0)
            throw new \Exception(json_last_error_msg());
    }
    else
        $data = $info;


    return $data;

   }

   function utf8_converter($array)
    {
        array_walk_recursive($array, function(&$item, $key){
            if(!mb_detect_encoding($item, 'utf-8', true)){
                    $item = utf8_encode($item);
            }
        });

        $array = $this->fixArrayKey($array);
     
        return $array;
    }

    function fixArrayKey(&$arr)
    {
        $arr = array_combine(
            array_map(
                function ($str) {
                    return str_replace(".", "_", $str);
                },
                array_keys($arr)
            ),
            array_values($arr)
        );

        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $this->fixArrayKey($arr[$key]);
            }
        }
    }
}