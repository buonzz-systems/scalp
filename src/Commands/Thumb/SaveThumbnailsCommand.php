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
        $output->writeln("writing data to http://"  . getenv('DB_HOSTNAME') . ':' . getenv('DB_PORT') . '/' . $this->build_db_name());
        
        $files = MediaFilesList::get($destination_folder);

        $client = ElasticServer::build_client();

        if(!$this->index_exists($client))
            $response = $client->indices()->create($this->get_mappings());
        
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

                $metadata = array('fileid' => $file_id,
                                  'path' => $orig_info['path'],
                                  'filename' => $orig_info['filename'],
                                  'timestamp' => date("c",time()),
                                  'data' => $data
                                  );


                $params = [
                    'index' => $this->build_db_name(),
                    'type' => 'thumbnails',
                    'body' => $metadata
                ];

                $response = $client->index($params);


                $output->writeln('File processed: <comment>'. $file->getFilename() .  '</comment>');
            }
            else
                $output->writeln('File skipped: <comment>'. $file->getFilename() .  '</comment>');   
        }

         $output->writeln("Success!");
    }

    private function get_original_file_info($filename){
        $output = array();

        $tmp = substr($filename,6);
        $tmp2 = explode("_sc_", $tmp);

        $output['filename'] = $tmp2[count($tmp2)-1];
        
        array_pop($tmp2);
        $output['path'] = implode('/', $tmp2);
        
        return $output;
    }

    private function build_db_name(){
        return 'thumbnails-' . getenv('DB_NAME');
    }

    private function get_mappings(){

         $mappings = array(
            'index' => $this->build_db_name(),
            'body' => array(
                'settings' => array(
                    'number_of_shards' => getenv('DB_SHARDS') ? getenv('DB_SHARDS'): 2 ,
                    'number_of_replicas' => getenv('DB_REPLICAS') ? getenv('DB_REPLICAS') : 1
                ),
                'mappings' => array(
                    'thumbnails' => array(
                          'properties' => array(
                                'fileid' => array('type'=>'string', 'index' => 'not_analyzed'),
                                'path' => array('type'=>'string', 'index' => 'not_analyzed'),
                                'filename' => array('type'=>'string', 'index' => 'not_analyzed'),
                                'timestamp' => array('type'=>'date', 'index' => 'not_analyzed'),
                                'data' => array('type'=>'string', 'index' => 'no')
                            )
                        ))
            )
        );

        return $mappings;
    }

    private function index_exists($client){
        $params = ['index' => $this->build_db_name()];
         return $client->indices()->exists($params);
    }

}