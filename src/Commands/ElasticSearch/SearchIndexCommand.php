<?php namespace Buonzz\Scalp\Commands\ElasticSearch;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

use Buonzz\Scalp\Searcher;
use Buonzz\Scalp\Analyzer;

class SearchIndexCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('es:search')
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
                    Analyzer::human_filesize($result['filesize']), 
                    date("n/j/Y g:i A, D", strtotime($result['last_modified'])), 
                    isset($result['DateTimeDigitized']) ? date("n/j/Y g:i A, D", strtotime($result['DateTimeDigitized'])) : null,
                    '/?id=' . $result['id']
                    );
        }

        $table = new Table($output);
        
        $table
            ->setHeaders(array('File', 'Size', 'Last Modified', 'Date Captured', 'Preview'))
            ->setRows($data);

        $table->render();
    }

}