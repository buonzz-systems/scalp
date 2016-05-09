<?php namespace Buonzz\Scalp;

class Analyzer{

	// see https://github.com/JamesHeinrich/getID3/blob/master/structure.txt
	private $desired_properties = array('filesize','bitrate', 'fileformat', 'filename', 'mime_type', 'playtime_seconds', 'playtime_string', 'filepath', 'tags');


 public function analyze($filepath, $json_output = FALSE){
	 $getID3 = new \getID3;

    $fileInfo = $getID3->analyze($filepath);

    $info = array();
    $info['last_modified'] = filemtime($filepath);
    
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
            $exif_data = array();
            $exif_data['DateTimeDigitized'] = $fileInfo['jpg']['exif']['EXIF']['DateTimeDigitized'];
            $exif_data['ExposureTime'] = $fileInfo['jpg']['exif']['EXIF']['ExposureTime'];
            $exif_data['FNumber'] = $fileInfo['jpg']['exif']['EXIF']['FNumber'];
            $exif_data['ISOSpeedRatings'] = $fileInfo['jpg']['exif']['EXIF']['ISOSpeedRatings'];            
            $exif_data['ShutterSpeedValue'] = (string) $fileInfo['jpg']['exif']['EXIF']['ShutterSpeedValue'];
            $exif_data['ApertureValue'] = $fileInfo['jpg']['exif']['EXIF']['ApertureValue'];
            $exif_data['FocalLength'] =  $fileInfo['jpg']['exif']['EXIF']['FocalLength'];
            
            $info['exif'] = $exif_data;
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