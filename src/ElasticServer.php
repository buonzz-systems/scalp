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
        return getenv('DB_NAME') . '-'. date('Y.m.d');
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
                            )
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
}