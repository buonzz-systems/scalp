<?php namespace Buonzz\Scalp\Commands\ElasticSearch;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

use Buonzz\Scalp\Searcher;

class SearchIndexCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('search')
            ->setDescription('search files on ElasticSearch')
            ->addArgument(
            'keywords',
            InputArgument::REQUIRED,
            'Enter any keywords you want to search'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $keywords = $input->getArgument('keywords');

        $results = Searcher::search($keywords);

        $data = array();
        
        foreach($results as $result){
            
            $data[] = array(
                    $result['filepath'] . '/'. $result['filename'] , 
                    $this->human_filesize($result['filesize']), 
                    date("n/j/Y g:i A, D", strtotime($result['last_modified'])), 
                    isset($result['DateTimeDigitized']) ? date("n/j/Y g:i A, D", strtotime($result['DateTimeDigitized'])) : null
                    );
        }

        $table = new Table($output);
        
        $table
            ->setHeaders(array('File', 'Size', 'Last Modified', 'Date Captured'))
            ->setRows($data);

        $table->render();
    }

    private function human_filesize($bytes, $dec = 2) 
    {
        $size   = array(' B', ' kB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

}