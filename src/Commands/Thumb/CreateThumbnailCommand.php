<?php namespace Buonzz\Scalp\Commands\Thumb;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Buonzz\Scalp\ThumbnailGenerator;

class CreateThumbnailCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('thumbnail:create')
            ->setDescription('create thumbnails for files inside source folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $source_folder = getenv('INPUT_FOLDER');
        $destination_folder = getenv('OUTPUT_FOLDER');

        if(!file_exists($source_folder))
        {        $output->writeln('<error>the "'. $source_folder .'" folder doesn\'t exists!</error>');
            exit;
        }

        if(!file_exists($destination_folder))
        {
            mkdir($destination_folder);
        }

        $generator = new ThumbnailGenerator($source_folder, $destination_folder, $output);
        $generator->generate();


         $output->writeln("Success!");
    }

}