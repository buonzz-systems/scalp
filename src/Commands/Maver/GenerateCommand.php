<?php namespace Buonzz\Scalp\Commands\Maver;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Buonzz\Scalp\Analyzer;
use Buonzz\Scalp\MediaFilesList;
use Buonzz\Scalp\ExcludedContents;

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
        
        $analyzer = new Analyzer();

        $source_folder = getenv('INPUT_FOLDER');
        $destination_folder = getenv('OUTPUT_FOLDER');
        $output_file_list = [];

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
            
            try {

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
                $output->writeln('<comment>'. $info->file_contents_hash . "." . $ext .  '</comment> resized');
                
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

             $output_file_list[$info->file_contents_hash] = $info->filepath;

            $output->writeln('<comment>'. $file->getFilename() .  '</comment> pre-processed');
            
            }
            catch(\Exception $e){
                $output->writeln($e->getMessage());   
                continue; 
            }

        } // end for each

         $output->writeln("Writing summary file");
        $file = fopen($destination_folder . "/files.json","w");
        fwrite($file, json_encode($output_file_list));
        fclose($file);

         $output->writeln("Success!");

     }

    }

    private function resize($file, $percent, $target){
        $thumb = new \PHPThumb\GD($file);
        $thumb->resizePercent($percent);
        $thumb->save($target);
    }

}