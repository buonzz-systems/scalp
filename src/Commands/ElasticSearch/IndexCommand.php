<?php namespace Buonzz\Scalp\Commands\ElasticSearch;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Elasticsearch\ClientBuilder;
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

        // connect
        $output->writeln("Establishing connection to ES at " . getenv('DB_HOSTNAME'));
        $client = $this->build_client();


        // delete old index
        $output->writeln("deleting the old db: " . getenv('DB_NAME'));
        $this->delete_db($client);

        // re-creating the db and mappings
        $output->writeln("re-creating the db and mappings");
        $response = $client->indices()->create($this->get_mappings());


        $files = MediaFilesList::get(getenv('INPUT_FOLDER'));
        
        $analyzer = new Analyzer();

        foreach($files as $file){
            $output->writeln('Processing <comment>'. $file->getPathname() .' </comment>'); 
            $metadata = $analyzer->analyze($file->getPathname());
        
            $params = [
                'index' => getenv('DB_NAME'),
                'type' => getenv('DOC_TYPE'),
                'timestamp' => strtotime("-1d"),
                'body' => $metadata
            ];

            $response = $client->index($params);

        }

        $output->writeln("Success!");

    }

    private function  get_mappings(){
         $mappings = [
            'index' => getenv('DB_NAME'),
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1
                ],
                'mappings' => [
                    getenv('DOC_TYPE') => [
                        'properties' => [
                            'exif.ShutterSpeedValue' => [
                                'type' => 'string'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $mappings;
    }

    private function delete_db($client){
        try{
            $params = ['index' => getenv('DB_NAME')];
            $response = $client->indices()->delete($params);
        }
        catch(\Elasticsearch\Common\Exceptions\Missing404Exception $e){
            ;
        }
    }

    private function build_client(){
        $hosts = [
            getenv('DB_HOSTNAME') . ':' . getenv('DB_PORT')
        ];

        $client = ClientBuilder::create()   // Instantiate a new ClientBuilder
                    ->setHosts($hosts)      // Set the hosts
                    ->build();              // Build the client object
        return $client;
    }
}