<?php namespace Buonzz\Scalp\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Elasticsearch\ClientBuilder;

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
        $folder = $input->getArgument('folder_path');

        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id',
            'body' => ['testField' => 'abc']
        ];

        $response = $client->index($params);
        print_r($response);
    }
}