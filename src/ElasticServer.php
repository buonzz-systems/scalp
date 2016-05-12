<?php namespace Buonzz\Scalp;

use Elasticsearch\ClientBuilder;
use Monolog\Logger;

class ElasticServer{

    public static function build_client(){
        $hosts = array();

        if (getenv('DB_USERNAME') != 'null' || getenv('DB_PASSWORD') != 'null') {
           $hosts[] = 'http://' . getenv('DB_USERNAME') . ":" . getenv('DB_PASSWORD') .'@' .getenv('DB_HOSTNAME') . ':' . getenv('DB_PORT');
        }
        else
        {
           $hosts[] = 'http://' .  getenv('DB_HOSTNAME') . ':' . getenv('DB_PORT');
        }

        $log_filename = '/scalp-'. date('Y.m.d.H.i'). '.log';

        $logger = ClientBuilder::defaultLogger(getenv('LOG_FOLDER') . $log_filename, Logger::INFO);

        $client = ClientBuilder::create()   // Instantiate a new ClientBuilder
                    ->setLogger($logger)    // set the logger
                    ->setHosts($hosts)      // Set the hosts
                    ->build();              // Build the client object
        return $client;
    }

    public static function build_db_name(){
        return getenv('DB_NAME');
    }

    public static function get_mappings(){

         $mappings = array(
            'index' => ElasticServer::build_db_name(),
            'body' => array(
                'settings' => array(
                    'number_of_shards' => getenv('DB_SHARDS') ? getenv('DB_SHARDS'): 1 ,
                    'number_of_replicas' => getenv('DB_REPLICAS') ? getenv('DB_REPLICAS') : 1
                ),
                'mappings' => array(
                    getenv('DOC_TYPE') => array(
                        'properties' => array(
                            'exif'=> array('type'=> 'nested', 
                            'properties' => array('ShutterSpeedValue' => array('type' => 'string'))
                            ),
                            'file_contents_hash'=> array('type' => "string", 'index' => 'not_analyzed'),
                            'filepath'=> array('type' => "string", 'index' => 'not_analyzed'),
                            'filename'=> array('type' => "string", 'index' => 'not_analyzed')
                        )
                    )
                )
            )
        );

        return $mappings;
    }

    public static function delete_db($client){
        try{
            $params = ['index' => ElasticServer::build_db_name()];
            $response = $client->indices()->delete($params);
        }
        catch(\Elasticsearch\Common\Exceptions\Missing404Exception $e){
            ;
        }
    }

    public static function get_file_id($client, $filepath, $filename){
        $params = [
            'index' => ElasticServer::build_db_name(),
            'type' => getenv('DOC_TYPE'),
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [ 'match' => [ 'filepath' => $filepath ] ],
                            [ 'match' => [ 'filename' => $filename ] ],
                        ]
                    ]
                ]
            ]
        ];

        $results = $client->search($params);
       
        if(isset($results['hits']['hits'][0]['_id']))
            return $results['hits']['hits'][0]['_id'];
        else
            return false;
    } // get_file_id


    public static function get_file_id_by_hash($client, $hash){
        $params = [
            'index' => ElasticServer::build_db_name(),
            'type' => getenv('DOC_TYPE'),
            'body' => [
                'query' =>  [ 'match' => [ 'file_contents_hash' => $hash ] ]
                    ]
                ];

        $results = $client->search($params);
       
        if(isset($results['hits']['hits'][0]['_id']))
            return $results['hits']['hits'][0]['_id'];
        else
        return false;
    } // get_file id by content hash


     public static function get_content_hash_by_id($client, $id){
        $params = [
            'index' => ElasticServer::build_db_name(),
            'type' => getenv('DOC_TYPE'),
            'id' => $id
        ];

        $results = $client->get($params);

        if(isset($results['_source']['file_contents_hash']))
            return $results['_source']['file_contents_hash'];
        else
            return false;
    } // get content hash




    public static function update($client, $id, $body){
        $params = [
            'index' => ElasticServer::build_db_name(),
            'type' => getenv('DOC_TYPE'),
            'id' => $id,
            'body' => $body
        ];

        $response = $client->update($params);
    } // update

    public static function index_exists($client){
        $params = ['index' => ElasticServer::build_db_name()];
         return $client->indices()->exists($params);
    }

}