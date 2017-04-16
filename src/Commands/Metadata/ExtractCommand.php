<?php namespace Buonzz\Scalp\Commands\Metadata;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Buonzz\Scalp\MetadataGenerator;

class ExtractCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('metadata:extract')
            ->setDescription('Read metadata of files to a JSON format');
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

        $output->writeln("reading files from " . $source_folder);
        $output->writeln("writing data to "  . $destination_folder);

        $generator = new MetadataGenerator($source_folder, $destination_folder, $output);
        $generator->generate();
        
         $output->writeln("Success!");
    }

}