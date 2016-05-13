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
    $info['human_filesize'] = Analyzer::human_filesize($fileInfo['filesize']);

    // turn the path into tags
    $info['path_tags'] = $this->path_to_tags($filepath, $fileInfo['filename']); 

    // dynamic properties
    foreach($this->desired_properties as $p)
    {
        if(isset($fileInfo[$p]) && !is_array($fileInfo[$p]))
            $info[$p] = utf8_encode($fileInfo[$p]);

    }

    // jpg-related metadata
    $info['exif'] = $this->extract_jpg($fileInfo);

     // date tags
    $info['date_tags'] = $this->compute_date_tags($info);    

    // store the content hash, used in resync
    $info['file_contents_hash'] = hash_file('sha256',$filepath);

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
                $exif_data['ExposureTime'] = floatval($fileInfo['jpg']['exif']['EXIF']['ExposureTime']);
            
            if(isset($fileInfo['jpg']['exif']['EXIF']['FNumber']))
                $exif_data['FNumber'] = floatval($fileInfo['jpg']['exif']['EXIF']['FNumber']);
            
            if(isset($fileInfo['jpg']['exif']['EXIF']['ISOSpeedRatings']))
                $exif_data['ISOSpeedRatings'] = floatval($fileInfo['jpg']['exif']['EXIF']['ISOSpeedRatings']);            
            
            if(isset($fileInfo['jpg']['exif']['EXIF']['ShutterSpeedValue']))
                $exif_data['ShutterSpeedValue'] = floatval($fileInfo['jpg']['exif']['EXIF']['ShutterSpeedValue']);
            
            if(isset($fileInfo['jpg']['exif']['EXIF']['ApertureValue']))
                $exif_data['ApertureValue'] = floatval($fileInfo['jpg']['exif']['EXIF']['ApertureValue']);
            
            if(isset($fileInfo['jpg']['exif']['EXIF']['FocalLength']))
                $exif_data['FocalLength'] =  floatval($fileInfo['jpg']['exif']['EXIF']['FocalLength']);
            
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

    function ms_to_human($ms)
    {
        $sign = $ms < 0 ? "-" : "";
        $ms = abs($ms);
        $sec = floor($ms / 1000);
        $ms = $ms % 1000;
        $min = floor($sec / 60);
        $sec = $sec % 60;
        $hr = floor($min / 60);
        $min = $min % 60;
        $day = floor($hr / 60);
        $hr = $hr % 60;
        return "$sign$hr h $min m $sec s";
    }

    function compute_date_tags($info){
        $base_date = '';
        $output = array();

        if(isset($info['exif']) && isset($info['exif']['DateTimeDigitized']))
            $base_date = strtotime($info['exif']['DateTimeDigitized']);
        else
            $base_date = strtotime($info['last_modified']);

        $output[] =  date("D", $base_date);
        $output[] =  date("jS", $base_date);
        $output[] =  date("l", $base_date);
        $output[] =  date("F", $base_date);
        $output[] =  date("M", $base_date);
        $output[] =  date("Y", $base_date);
        $output[] =  date("ga", $base_date);
        $output[] =  date("e", $base_date);
        $output[] =  date("j", $base_date);

        return $output;
    }

    public static function human_filesize($bytes, $dec = 2) 
    {
        $size   = array(' B', ' kB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}