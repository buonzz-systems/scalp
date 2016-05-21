<?php namespace Buonzz\Scalp\Commands\Thumb;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Buonzz\Scalp\MediaFilesList;
use Buonzz\Scalp\Analyzer;

class CreateThumbnailCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('thumbnail:create')
            ->setDescription('create thumbnails for files inside source folder');
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

        if(!file_exists($destination_folder . '/thumbs'))
                mkdir($destination_folder . '/thumbs');

        $output->writeln("reading files from " . $source_folder);
        $output->writeln("writing data to "  . $destination_folder . '/thumbs');
        
        $files = MediaFilesList::get($source_folder);
        
        foreach($files as $k=>$file)
        {

            try{
               $ext = strtolower($file->getExtension()); 

               if(in_array($ext, array('jpg', 'jpeg', 'png')))
               {

                    $prefix = "thumb-" . str_replace('/', '_sc_',  Analyzer::remove_base_path($file->getPath()) . '/');
                    $filename = $destination_folder . "/thumbs/" . $prefix. $file->getFilename();

                    
                    if(!file_exists($filename))
                    {
                        $thumb = new \PHPThumb\GD($file->getRealPath());
                        $thumb->resizePercent(getenv('THUMB_PERCENT_RESIZE'));

                        $thumb->save($filename);
                        $output->writeln('File processed: <comment>'. $filename .  '</comment>');
                    }
                    else
                        $output->writeln('thumbnail already present, skipped : <comment>'. $filename .  '</comment>');   

                }
                else
                    $output->writeln('File skipped: <comment>'. $file->getFilename() .  '</comment>');
            }catch(\Exception $e){
                $output->writeln('<error>'. $e->getMessage() .'</error>');
            }   
        }

         $output->writeln("Success!");
    }

}