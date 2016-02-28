<?php namespace Buonzz\Scalp\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IndexCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('scalp:index')
            ->setDescription('Read and index a certain directory to ElasticSearch')
            ->addArgument(
                'folder_path',
                InputArgument::OPTIONAL,
                'Where is the files located?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $output->writeln("Initialiazing");
        $folder = $input->getArgument('folder_path');
    }
}