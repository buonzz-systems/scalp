<?php namespace Buonzz\Scalp\Commands\Export;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Buonzz\Scalp\DirectoryParser;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Buonzz\Scalp\ScalpLogger;
use Symfony\Component\Console\Helper\ProgressBar;


class MongoDBCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('export:mongodb')
            ->setDescription('Accepts a folder then dump its structure into a script that can be used to store into MongoDB')
            ->addOption(
                'folder-path',
                null,
                InputOption::VALUE_REQUIRED,
                'What is the absolute path to the folder?',
                '.'
            )->addOption(
                'output-file',
                null,
                InputOption::VALUE_REQUIRED,
                'What file to dump this?',
                'data/mongodb_dump.js'
            )->addOption(
                'database',
                null,
                InputOption::VALUE_REQUIRED,
                'What is the MongoDB database name?',
                'media'
            )->addOption(
                'collection',
                null,
                InputOption::VALUE_REQUIRED,
                'What is the MongoDB Collection name?',
                'files'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = '';
        
        $path = $input->getOption('folder-path');
        $output_file = $input->getOption('output-file');
        $database = $input->getOption('database');
        $collection = $input->getOption('collection');

        $logger = new ConsoleLogger($output);
        $slogger = new ScalpLogger($logger);

        $progress = new ProgressBar($output);        

        if (!file_exists($path)) {
            $text = "Error: ".$path ." <-- cant find the specified folder";        
            throw new \Exception($text);
        }
        else
        {
            $gen = new MongoDBScriptGenerator($path, $database, $collection);
            $progress->start();            
            $progress->advance();
            $data_output = $gen->generate();
            file_put_contents($output_file,$data_output);
            $progress->finish();           
        } 

        $output->writeln("\nDump written on " . $output_file);               
    }
}