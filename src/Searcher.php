<?php namespace Buonzz\Scalp;

use GuzzleHttp\Client;
use Buonzz\Scalp\ElasticServer;

class Searcher{

    public static function search($query){
        
        $results = array();

        $client = new Client([
            'base_uri' =>  ElasticServer::build_db_uri(),
            'timeout'  => 2.0,
        ]);


        $response = $client->request('GET', Searcher::build_uri(), [
            'query' => ['q' => $query]
        ]);

        if($response->getStatusCode() == 200)
        {
            $results = (string) $response->getBody();
            $results = json_decode($results);
            $results = $results->hits->hits;

            $new_result = array();

            foreach($results as $result_item){
                
                $item = array();
                
                $item['last_modified'] = $result_item->_source->last_modified;
                $item['last_accessed'] = $result_item->_source->last_accessed;
                $item['file_permissions'] = $result_item->_source->file_permissions;
                $item['date_indexed'] = $result_item->_source->date_indexed;
                $item['path_tags'] = implode(", ",$result_item->_source->path_tags);
                $item['filesize'] = $result_item->_source->filesize;
                $item['fileformat'] = $result_item->_source->filesize;
                $item['filename'] = $result_item->_source->filename;
                $item['filepath'] = $result_item->_source->filepath;
                
                if(is_object($result_item->_source->exif))
                {
                    $item['DateTimeDigitized'] = $result_item->_source->exif->DateTimeDigitized;
                    $item['ExposureTime'] = $result_item->_source->exif->ExposureTime;
                    $item['FNumber'] = $result_item->_source->exif->FNumber;
                    $item['ISOSpeedRatings'] = $result_item->_source->exif->ISOSpeedRatings;
                    $item['ShutterSpeedValue'] = $result_item->_source->exif->ShutterSpeedValue;
                    $item['ApertureValue'] = $result_item->_source->exif->ApertureValue;
                    $item['FocalLength'] = $result_item->_source->exif->FocalLength;
                }

                $item['date_tags'] = implode(", ", $result_item->_source->date_tags);
                $item['file_contents_hash'] = $result_item->_source->file_contents_hash;
                $item['width'] = $result_item->_source->width;
                $item['height'] = $result_item->_source->height;

                $new_result[] = $item;
            }

            $results = $new_result;
        }

        return $results;

    } //search
    
    public static function build_uri(){
     return '/'. getenv('DB_NAME') .'/'. getenv('DOC_TYPE').'/_search';  
    }
}