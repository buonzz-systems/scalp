<?php namespace Buonzz\Scalp\Commands\Thumb;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Buonzz\Scalp\MediaFilesList;
use Buonzz\Scalp\ElasticServer;

class SaveThumbnailsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('thumbnail:save')
            ->setDescription('embed the thumbnails base64-encoded data to ElasticSearch documents');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $destination_folder = getenv('OUTPUT_FOLDER');

        if(!file_exists($destination_folder . '/thumbs'))
        {        $output->writeln('<error>the "'.$destination_folder . '/thumbs' .'" folder doesn\'t exists!</error>');
            exit;
        }


        $output->writeln("reading files from " . $destination_folder . '/thumbs');
        $output->writeln("writing data to http://"  . getenv('DB_HOSTNAME') . ':' . getenv('DB_PORT') . '/' . getenv('DB_NAME'));
        
        $files = MediaFilesList::get($destination_folder);

        $client = ElasticServer::build_client();
        
        foreach($files as $k=>$file)
        {

           $ext = strtolower($file->getExtension()); 

           if(in_array($ext, array('jpg', 'jpeg', 'png', 'gif','bmp')))
           {

                $filename = $destination_folder . "/thumbs/" . $file->getFilename();
                $data = base64_encode(file_get_contents($filename));

                $orig_info = $this->get_original_file_info($file->getFilename());

                 $file_id = ElasticServer::get_file_id(
                        $client, 
                        $orig_info['path'], 
                        $orig_info['filename']);


                ElasticServer::update($client, $file_id,[
                                            'doc' => [
                                                'thumbnail' => $data
                                            ]
                                        ]);

                $output->writeln('File processed: <comment>'. $file->getFilename() .  '</comment>');
            }
            else
                $output->writeln('File skipped: <comment>'. $file->getFilename() .  '</comment>');   
        }

         $output->writeln("Success!");
    }

    private function get_original_file_info($filename){
        $output = array();

        $components = explode(".-thumb-", $filename);

        $output['path'] = str_replace('.', '/', $components[0]);
        $output['filename'] = $components[1];

        return $output;
    }

}