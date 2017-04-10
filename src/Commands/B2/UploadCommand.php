<?php namespace Buonzz\Scalp\Commands\B2;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Buonzz\Scalp\Analyzer;
use Buonzz\Scalp\MediaFilesList;

use ChrisWhite\B2\Client;
use ChrisWhite\B2\Bucket;

class UploadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('b2:upload')
            ->setDescription('Uploads files to BackBlaze');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $analyzer = new Analyzer();

        $source_folder = getenv('INPUT_FOLDER');
        $destination_folder = getenv('OUTPUT_FOLDER');
        $account_id = getenv('B2_ACCOUNT_ID');
        $application_key = getenv('B2_APPLICATION_KEY');

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
        $files = MediaFilesList::get($source_folder);
        
        foreach($files as $k=>$file)
        {
            $data = $analyzer->analyze($file->getRealPath(),true);
            $prefix = str_replace('/', '.', $file->getPath()) .".";

            $filename = $destination_folder . "/" . $prefix. $file->getFilename() . ".json";
            file_put_contents($filename, $data);
            $output->writeln('<comment>'. $file->getFilename() .  ' processed </comment>');
        }

         $output->writeln("Success!");
    }

}