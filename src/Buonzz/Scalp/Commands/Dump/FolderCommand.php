<?php namespace Buonzz\Scalp\Commands\Dump;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Buonzz\Scalp\DirectoryParser;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Buonzz\Scalp\ScalpLogger;

class FolderCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dump:folder')
            ->setDescription('Accepts a folder then dump its structure as JSON')
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
                'dump.json'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = '';
        
        $path = $input->getOption('folder-path');
        $output_file = $input->getOption('output-file');
        
        $logger = new ConsoleLogger($output);
        $slogger = new ScalpLogger($logger);

        if (!file_exists($path)) {
            $text = "Error: ".$path ." <-- cant find the specified folder";        
            throw new \Exception($text);
        }
        else
        {           
            $ar = DirectoryParser::directoryToArray($path);
            $text = json_encode($ar);
            file_put_contents($output_file,$text);
            $logger->info("Dump written on " . $output_file);
        } 

        $output->writeln("Dump written on " . $output_file);               
    }
}