<?php namespace Buonzz\Scalp\Commands\Metadata;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AnalyzeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('metadata:analyze')
            ->setDescription('Read metadata of a file to a JSON format')
            ->addArgument(
                'file_path',
                InputArgument::REQUIRED,
                'Which file?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {


        $desired_properties = array('filesize','bitrate', 'fileformat', 'filename', 'mime_type', 'playtime_seconds', 'playtime_string', 'filepath');
        
        $file = $input->getArgument('file_path');
        $getID3 = new \getID3;

        $fileInfo = $getID3->analyze($file);

        $info = array();
        
        foreach($desired_properties as $p)
            $info[$p] = utf8_encode($fileInfo[$p]);
        

        $data = json_encode($info);
        if(strlen($data) <=0)
            throw new \Exception(json_last_error_msg());

        $output->writeln($data);
    }

}