<?php namespace Buonzz\Scalp\Commands\B2;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Buonzz\Scalp\Analyzer;
use Buonzz\Scalp\MediaFilesList;
use Buonzz\Scalp\ExcludedContents;

use ChrisWhite\B2\Client;
use ChrisWhite\B2\Bucket;

class UploadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('b2:upload')
            ->setDescription('Uploads files to BackBlaze')
            ->addArgument(
            'bucket',
            InputArgument::REQUIRED,
            'Enter the Bucket name where you want to upload the files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $analyzer = new Analyzer();

        $source_folder = getenv('INPUT_FOLDER');
        $destination_folder = getenv('OUTPUT_FOLDER');
        $account_id = getenv('B2_ACCOUNT_ID');
        $application_key = getenv('B2_APPLICATION_KEY');
        $bucket_name = $input->getArgument('bucket');

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

            if(file_exists($destination_folder . '/'. $filename))
                continue;

            $output->writeln('<comment>'. $filename .  '</comment> metadata extracted');
            file_put_contents($destination_folder . '/'. $filename, $data);
            
            $ext = strtolower($file->getExtension());
            
            if($info->width > 500 || $info->height > 500)
            {
                $output->writeln('<comment>'. $info->file_contents_hash . "." . $ext .  '</comment> resized before upload ');
                
                //thumbnail
                $this->resize($file->getRealPath(), 10, $destination_folder . '/'. $info->file_contents_hash . "-small." . $ext);

                //medium
                $this->resize($file->getRealPath(), 30, $destination_folder . '/'. $info->file_contents_hash . "-med." . $ext);

                //large
                $this->resize($file->getRealPath(), 50, $destination_folder . '/'. $info->file_contents_hash . "-large." . $ext);
            }
            else
            {
                $output->writeln('<comment>'. $info->file_contents_hash . "." . $ext .  '</comment> retained original size');

                copy(
                        $file->getRealPath(), 
                        $destination_folder . '/'. $info->file_contents_hash . "-small." . $ext
                    );  

                copy(
                        $file->getRealPath(), 
                        $destination_folder . '/'. $info->file_contents_hash . "-medium." . $ext
                    ); 

                copy(
                        $file->getRealPath(), 
                        $destination_folder . '/'. $info->file_contents_hash . "-large." . $ext
                    );
            }

            $output->writeln('<comment>'. $file->getFilename() .  '</comment> pre-processed');
        }

        $output->writeln('Connecting to BackBlaze using account_id: ' . $account_id);

        $b2_client = new Client($account_id, $application_key);


        // get all files on destination folder
        $files_to_upload = scandir($destination_folder);

        foreach($files_to_upload as $file_to_upload){

            if (!in_array($file_to_upload,ExcludedContents::get())){
                $output->writeln('Uploading : ' . $file_to_upload);            
                 $file = $b2_client->upload([
                    'BucketName' => $bucket_name,
                    'FileName' => $file_to_upload,
                    'Body' => fopen($destination_folder . "/" . $file_to_upload, 'r')
                ]);
            }
        }

         $output->writeln("Success!");
    }

    private function resize($file, $percent, $target){
        $thumb = new \PHPThumb\GD($file);
        $thumb->resizePercent($percent);
        $thumb->save($target);
    }

}