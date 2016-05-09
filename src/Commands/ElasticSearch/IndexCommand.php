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
        $client = ClientBuilder::create()->build();


        $output->writeln("Initializing");


        $params = ['index' => getenv('DB_NAME')];
        $response = $client->indices()->delete($params);


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

        $response = $client->indices()->create($mappings);


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
}