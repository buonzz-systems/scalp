<?php namespace Buonzz\Scalp\Commands\Dump;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Buonzz\Scalp\DirectoryParser;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Buonzz\Scalp\ScalpLogger;
use Symfony\Component\Console\Helper\ProgressBar;

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
                'data/dump.json'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = '';
        
        $path = $input->getOption('folder-path');
        $output_file = $input->getOption('output-file');
        
        $logger = new ConsoleLogger($output);
        $slogger = new ScalpLogger($logger);

        $progress = new ProgressBar($output);        

        if (!file_exists($path)) {
            $text = "Error: ".$path ." <-- cant find the specified folder";        
            throw new \Exception($text);
        }
        else
        {
            $progress->start();

            $ar = DirectoryParser::directoryToArray($path);
            $progress->advance();
            $text = json_encode($ar);
            $progress->advance();
            file_put_contents($output_file,$text);
            $progress->advance();
            $progress->finish();
            $logger->info("Dump written on " . $output_file);
        } 

        $output->writeln("\nDump written on " . $output_file);               
    }
}