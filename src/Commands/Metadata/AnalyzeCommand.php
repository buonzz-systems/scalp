<?php namespace Buonzz\Scalp\Commands\Metadata;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Buonzz\Scalp\Analyzer;
use Buonzz\Scalp\MediaFilesList;

class AnalyzeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('metadata:analyze')
            ->setDescription('Read metadata of files to a JSON format');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $analyzer = new Analyzer();

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

        $files = MediaFilesList::get($source_folder);
        
        foreach($files as $k=>$file)
        {
            $data = $analyzer->analyze($file->getRealPath(),true);
            $prefix = str_replace('/', '.', $file->getPath());

            $filename = $destination_folder . "/" . $prefix. $file->getFilename() . ".json";
            file_put_contents($filename, $data);
        }
    }

}