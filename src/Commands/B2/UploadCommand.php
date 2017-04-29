<?php namespace Buonzz\Scalp\Commands\B2;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
            'Enter the Bucket name where you want to upload the files')
            ->addArgument(
            'folder',
            InputArgument::REQUIRED,
            'Enter the folder where to read the files to upload');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $folder = $input->getArgument('folder');
        $account_id = getenv('B2_ACCOUNT_ID');
        $application_key = getenv('B2_APPLICATION_KEY');
        $bucket_name = $input->getArgument('bucket');

        if(!file_exists($folder))
        {        $output->writeln('<error>the "'. $folder .'" folder doesn\'t exists!</error>');
            exit;
        }

        $output->writeln("reading files to upload: "  . $folder);
        $output->writeln('Connecting to BackBlaze using account_id: ' . $account_id);

        $b2_client = new Client($account_id, $application_key);

        // get all files on destination folder
        $files_to_upload = scandir($folder);

        foreach($files_to_upload as $file_to_upload){

            if (!in_array($file_to_upload,ExcludedContents::get())){
                    try{
                    
                        if(!$b2_client->fileExists(['BucketName' => $bucket_name,'FileName' => $file_to_upload,]))
                        {           
                            $output->writeln( "[ ". date("Y-m-d H:i:s") . " ]" 
                                . ' Uploading : <info>' . $file_to_upload . '</info>');

                             $file = $b2_client->upload([
                                'BucketName' => $bucket_name,
                                'FileName' => $file_to_upload,
                                'Body' => fopen($folder . "/" . $file_to_upload, 'r')
                            ]);

                        }else
                            $output->writeln( "[ ". date("Y-m-d H:i:s") . " ]" 
                                . ' Skipped, already exists : <info>' . $file_to_upload . '</info>');
                    }
                    catch(\Exception $e){
                            $msg = $e->getMessage();

                            if($msg == 'Received error from B2: Authorization token has expired')
                            {
                                $output->writeln("Error: Authorization token has expired. restart the app");
                                exit; 
                            }

                            $output->writeln("Error: " . $msg); 
                            sleep(5);
                            continue;           
                    }
            } // if
        } // foreach
        
        $output->writeln("Success!");
    } // execute

}