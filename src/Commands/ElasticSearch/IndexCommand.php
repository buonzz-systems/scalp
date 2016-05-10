<?php namespace Buonzz\Scalp\Commands\ElasticSearch;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Helper\ProgressBar;

use Elasticsearch\ClientBuilder;
use Monolog\Logger;

use Buonzz\Scalp\MediaFilesList;
use Buonzz\Scalp\Analyzer;

class IndexCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('es:index')
            ->setDescription('Read and index a certain directory to ElasticSearch');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $output->writeln("Initializing");

        $progress = new ProgressBar($output);
        $progress->setFormat("files: %current% [%bar%] %elapsed:6s% %memory:6s% - %message%\n");

        // connect
        $output->writeln("Establishing connection to ES at " . getenv('DB_HOSTNAME'));
        $client = $this->build_client();


        // delete old index
        $output->writeln("removing the current db to avoid possible duplicates : " . $this->build_db_name());
        $this->delete_db($client);

        // re-creating the db and mappings
        $output->writeln("re-creating the db and mappings");
        $response = $client->indices()->create($this->get_mappings());


        $files = MediaFilesList::get(getenv('INPUT_FOLDER'));
        
        $analyzer = new Analyzer();

        $progress->start();

        foreach($files as $file){
            $progress->setMessage('Processing <comment>'. $file->getPathname() .' </comment>');
            $metadata = $analyzer->analyze($file->getPathname());
        
            $params = [
                'index' => $this->build_db_name(),
                'type' => getenv('DOC_TYPE'),
                'body' => $metadata
            ];

            $response = $client->index($params);
            $progress->advance();
        }

        $progress->finish();
        $progress->clear();

        $output->writeln("Success! - processed files: " . count($files));
    }

    private function  get_mappings(){

         $mappings = array(
            'index' => $this->build_db_name(),
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

    private function delete_db($client){
        try{
            $params = ['index' => $this->build_db_name()];
            $response = $client->indices()->delete($params);
        }
        catch(\Elasticsearch\Common\Exceptions\Missing404Exception $e){
            ;
        }
    }

    private function build_client(){


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

    private function build_db_name(){
        return getenv('DB_NAME') . '-'. date('Y.m.d');
    }
}