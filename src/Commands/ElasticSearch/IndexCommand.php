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
            ->setDescription('Read and index a certain directory to ElasticSearch')
            ->addArgument(
                'folder_path',
                InputArgument::OPTIONAL,
                'Where is the files located?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = ClientBuilder::create()->build();
        $output->writeln("Initializing");
        $folder_path = $input->getArgument('folder_path');

        $files = MediaFilesList::get($folder_path);
        
        $analyzer = new Analyzer();


        foreach($files as $file){
            $output->writeln('Processing <comment>'. $file->getPathname() .' </comment>'); 
            $metadata = $analyzer->analyze($file->getPathname());
        
            $params = [
                'index' => 'scalp_media_files',
                'type' => 'file',
                'id' => md5($file->getPathname()),
                'routing' => 'scalp',
                'timestamp' => strtotime("-1d"),
                'body' => $metadata
            ];

            $response = $client->index($params);

            if($response['_shards']['failed'] !=0)
                $output->writeln('<error>ERROR: '. $file->getPathname() . '</error>');
        }

        $output->writeln("Success!");

    }
}