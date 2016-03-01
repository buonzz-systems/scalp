<?php namespace Buonzz\Scalp\Commands\Metadata;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Buonzz\Scalp\Analyzer;

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
        
        $file = $input->getArgument('file_path');

        $analyzer = new Analyzer();
        $data = $analyzer->analyze($file);
        $output->writeln($data);
    }

}