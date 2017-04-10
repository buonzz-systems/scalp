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
            
            $info = json_decode($data);

            $filename = $info->file_contents_hash . ".json";
            $output->writeln('<comment>'. $filename .  ' metadata extracted </comment>');
            file_put_contents($destination_folder . '/'. $filename, $data);
            
            $ext = strtolower($file->getExtension());
            
            if($info->width > 500 || $info->height > 500)
            {

                $thumb = new \PHPThumb\GD($file->getRealPath());
                $thumb->resizePercent(getenv('THUMB_PERCENT_RESIZE'));
                $output->writeln('<comment>'. $filename .  ' resizing before upload </comment>');
                $thumb->save($destination_folder . '/'. $info->file_contents_hash . "." . $ext);
            }
            else
            {
                $output->writeln('<comment>'. $filename .  ' retained original size </comment>');
                copy(
                        $file->getRealPath(), 
                        $destination_folder . '/'. $info->file_contents_hash . "." . $ext
                    );  
            }

            $output->writeln('<comment>'. $file->getFilename() .  ' processed </comment>');
        }

         $output->writeln("Success!");
    }

}