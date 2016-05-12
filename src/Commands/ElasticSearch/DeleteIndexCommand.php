<?php namespace Buonzz\Scalp\Commands\ElasticSearch;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Buonzz\Scalp\ElasticServer;

class DeleteIndexCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('es:delete')
            ->setDescription('delete ElasticSearch index');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ElasticServer::delete_index();        
        $output->writeln("Index Deleted!");
    }

}