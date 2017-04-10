<?php namespace Buonzz\Scalp\Commands\ElasticSearch;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Stopwatch\Stopwatch;

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
        
        $stopwatch = new Stopwatch();
        $stopwatch->start('indexing');
        $output->writeln("Initializing");

        $counters = array();
        $counters['skipped'] = 0;
        $counters['mismatch'] =0;
        $counters['new'] =0;

        // connect
        $output->writeln("Establishing connection to ElasticSearch at " . getenv('DB_HOSTNAME'));

        try{

            $client = ElasticServer::build_client();
            $is_resync = ElasticServer::index_exists($client);

        }catch(\Exception $ex){
            $output->writeln('<error>Opps!, I encountered an error upon connecting to '. getenv('DB_HOSTNAME'). '</error>');
            exit;
        }   
        

        
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
        $progress->setMessage('loading files...');
        $progress->setFormat("files: %current% [%bar%] %elapsed:6s% %memory:6s% - %message%\n");
        $progress->start();

        foreach($files as $file){

            $progress->setMessage('Processing <comment>'. $file->getPathname() .' </comment>');
            $metadata = $analyzer->analyze($file->getPathname()); 
            $file_id = ElasticServer::get_file_id_by_hash($client,$metadata['file_contents_hash']);

            // if file doesn't exists, insert it
            if($file_id == false)
            {
                $progress->setMessage('new file: <comment>'. $file->getFilename() .' </comment>');

                
                $params = [
                    'index' => ElasticServer::build_db_name(),
                    'type' => getenv('DOC_TYPE'),
                    'body' => $metadata
                ];
                $response = $client->index($params);

                $counters['new']++;

            }
            else // update existing document
            {

                $progress->setMessage('comparing content hash if changed');

                $db_content_hash = ElasticServer::get_content_hash_by_id(
                                                $client, $file_id);

                $local_content_hash = $metadata['file_contents_hash'];

                if($db_content_hash != $local_content_hash)
                {
                    $progress->setMessage('<comment>'. $file->getFilename() . '</comment> hash mismatch, updating...');
                    
                    $params = ['doc' => [getenv('DOC_TYPE') => $metadata]];

                    ElasticServer::update($client, $file_id, $params);
                    $counters['mismatch']++;
                }
                else
                {
                    $counters['skipped']++;
                    $progress->setMessage('<comment>'. $file->getFilename() . "</comment>'s contents did not changed, skipped");                    
                }
            }

            $progress->advance();
        } // foreach

        $progress->finish();
        $progress->clear();
        $event = $stopwatch->stop('indexing');

        $output->writeln("Success! <comment>Processed files:</comment> " . count($files) 
                        . ' <comment>Duration:</comment> ' . $analyzer->ms_to_human($event->getDuration())
                        . ' <comment>New:</comment>' . $counters['new'] 
                        . ' <comment>Skipped:</comment>' . $counters['skipped']
                        . ' <comment>Mismatch:</comment>' . $counters['mismatch']
                        );
    }
}