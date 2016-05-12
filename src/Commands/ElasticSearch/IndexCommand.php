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
use Buonzz\Scalp\ElasticServer;

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
        $client = ElasticServer::build_client();

        $is_resync = ElasticServer::index_exists($client);

        
        if($is_resync)
        {
            $output->writeln("database <comment>'". ElasticServer::build_db_name() . "'</comment> already exists, performing resync..");
        }
        else
        {
            // creating the db and mappings
            $output->writeln("fresh indexing process detected, creating the db and data mappings");
            $response = $client->indices()->create(ElasticServer::get_mappings());
        }

        $output->writeln("--------");

        $files = MediaFilesList::get(getenv('INPUT_FOLDER'));
        
        $analyzer = new Analyzer();

        $progress = new ProgressBar($output);
        $progress->setFormat("files: %current% [%bar%] %elapsed:6s% %memory:6s% - %message%\n");
        $progress->start();

        foreach($files as $file){
            $progress->setMessage('Processing <comment>'. $file->getPathname() .' </comment>');
            $metadata = $analyzer->analyze($file->getPathname());
        
            $params = [
                'index' => ElasticServer::build_db_name(),
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
}