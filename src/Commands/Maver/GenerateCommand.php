<?php namespace Buonzz\Scalp\Commands\Maver;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Buonzz\Scalp\ThumbnailGenerator;
use Buonzz\Scalp\MetadataGenerator;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('maver:generate')
            ->setDescription('Generate Files for Maver');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $source_folder = getenv('INPUT_FOLDER');
        $destination_folder = getenv('OUTPUT_FOLDER');

        if(!file_exists($source_folder))
        {        
            $output->writeln('<error>the "'. $source_folder .'" folder doesn\'t exists!</error>');
            exit;
        }

        if(!file_exists($destination_folder))
        {
            mkdir($destination_folder);
        }

        $output->writeln("reading files from " . $source_folder);
        $output->writeln("writing data to "  . $destination_folder);

        $output_file_list = [];
        $metadata_generator = new MetadataGenerator($source_folder, $destination_folder, $output);
        $output_file_list = array_merge($output_file_list, $metadata_generator->generate());

        $thumb_generator = new ThumbnailGenerator($source_folder, $destination_folder, $output);
        $output_file_list = array_merge($output_file_list, $thumb_generator->generate());


        $output->writeln("Writing summary file");
        $file = fopen($destination_folder . "/files.json","w");
        fwrite($file, json_encode($output_file_list));
        fclose($file);

         $output->writeln("Success!");

     }

}