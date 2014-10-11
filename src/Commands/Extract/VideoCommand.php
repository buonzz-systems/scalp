<?php namespace Buonzz\Scalp\Commands\Extract;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Buonzz\Scalp\DirectoryParser;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Buonzz\Scalp\ScalpLogger;
use Symfony\Component\Console\Helper\ProgressBar;

class VideoCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('extract:video')
            ->setDescription('Accepts a path to video file then give back the metadata')
            ->addOption(
                'file-path',
                null,
                InputOption::VALUE_REQUIRED,
                'What is the absolute path to the video file?',
                '.'
            )->addOption(
                'output-file',
                null,
                InputOption::VALUE_REQUIRED,
                'What file to dump this?',
                'video-dump.json'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = '';
        
        $path = $input->getOption('file-path');
        $output_file = $input->getOption('output-file');
        
        $logger = new ConsoleLogger($output);
        $slogger = new ScalpLogger($logger);

        $progress = new ProgressBar($output);        

        if (!file_exists($path)) {
            $text = "Error: ".$path ." <-- cant find the specified folder";        
            throw new \Exception($text);
        }
        elseif(strlen($path)<=2){
            $text = "Error: seems you had passed an invalid path to the video";        
            throw new \Exception($text);   
        }
        else
        {
            $progress->start();

            $id3 = new \getID3;
            $fileinfo = $id3->analyze($path);

            file_put_contents($output_file, \ForceUTF8\Encoding::fixUTF8(json_encode($fileinfo)));
            $progress->advance();
            $progress->finish();
            $logger->info("metadata written on " . $output_file);
        } 

        $output->writeln("\nMetadata written on " . $output_file);               
    }
}