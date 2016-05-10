<?php namespace Buonzz\Scalp;

class Analyzer{

	// see https://github.com/JamesHeinrich/getID3/blob/master/structure.txt
	private $desired_properties = array('filesize','bitrate', 'fileformat', 'filename', 'mime_type', 'playtime_seconds', 'playtime_string', 'filepath', 'tags');


 public function analyze($filepath, $json_output = FALSE){
	 $getID3 = new \getID3;

    $fileInfo = $getID3->analyze($filepath);

    
    $info = array();
    
    // common file system info
    $info['last_modified'] = date("c",filemtime($filepath));
    $info['last_accessed'] = date("c",fileatime($filepath));
    $info['file_permissions'] = substr(sprintf('%o', fileperms($filepath)), -4);
    $info['date_indexed'] = date("c",time());

    // turn the path into tags
    $info['path_tags'] = $this->path_to_tags($filepath, $fileInfo['filename']); 


    // dynamic properties
    foreach($this->desired_properties as $p)
    {
        if(isset($fileInfo[$p]))
            $info[$p] = utf8_encode($fileInfo[$p]);

    }

    // jpg-related metadata
    $info['exif'] = $this->extract_jpg($fileInfo);


    // video-specific meta
    if(isset($fileInfo['video'])){
            $info['width'] = utf8_encode($fileInfo['video']['resolution_x']);
            $info['height'] = utf8_encode($fileInfo['video']['resolution_y']);
    }
    

    if($json_output)
    {

        $data = json_encode($info);
        if(strlen($data) <=0)
            throw new \Exception(json_last_error_msg());
    }
    else
        $data = $info;


    return $data;

   } // analyze


   function path_to_tags($path, $filename){
       $tmp =  explode("/", $path);
       return array_values(array_diff($tmp, array($filename, '')));
   }

   function extract_jpg($fileInfo){

     $exif_data = array();
        if(isset($fileInfo['jpg']) && isset($fileInfo['jpg']['exif']) && isset($fileInfo['jpg']['exif']['EXIF']))
        {   
    

            if(isset($fileInfo['jpg']['exif']['EXIF']['DateTimeDigitized']))
            {

                $tmp = strtotime($fileInfo['jpg']['exif']['EXIF']['DateTimeDigitized']);
                
                if($tmp !== FALSE)
                    $exif_data['DateTimeDigitized'] = date("c",$tmp);
            }

            if(isset($fileInfo['jpg']['exif']['EXIF']['ExposureTime']))
                $exif_data['ExposureTime'] = $fileInfo['jpg']['exif']['EXIF']['ExposureTime'];
            
            if(isset($fileInfo['jpg']['exif']['EXIF']['FNumber']))
                $exif_data['FNumber'] = $fileInfo['jpg']['exif']['EXIF']['FNumber'];
            
            if(isset($fileInfo['jpg']['exif']['EXIF']['ISOSpeedRatings']))
                $exif_data['ISOSpeedRatings'] = $fileInfo['jpg']['exif']['EXIF']['ISOSpeedRatings'];            
            
            if(isset($fileInfo['jpg']['exif']['EXIF']['ShutterSpeedValue']))
                $exif_data['ShutterSpeedValue'] = (string) $fileInfo['jpg']['exif']['EXIF']['ShutterSpeedValue'];
            
            if(isset($fileInfo['jpg']['exif']['EXIF']['ApertureValue']))
                $exif_data['ApertureValue'] = $fileInfo['jpg']['exif']['EXIF']['ApertureValue'];
            
            if(isset($fileInfo['jpg']['exif']['EXIF']['FocalLength']))
                $exif_data['FocalLength'] =  $fileInfo['jpg']['exif']['EXIF']['FocalLength'];
            
        }

        return $exif_data;

   } // extract jpg

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