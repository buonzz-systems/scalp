<?php namespace Buonzz\Scalp\Commands\ElasticSearch;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Buonzz\Scalp\ElasticServer;

class DeleteIndexCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('delete')
            ->setDescription('delete ElasticSearch index');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to delete <comment>"'. getenv('DB_NAME') . '</comment>" database on <comment>' .getenv('DB_HOSTNAME') . ':' . getenv('DB_PORT') . '</comment> ? ( default: no ) ', false);

        if ($helper->ask($input, $output, $question)) {
            
            try{
            ElasticServer::delete_index();
            }catch(\Exception $e){ ;} 

            $output->writeln("Index Deleted!");
        }
        else
            $output->writeln("Operation aborted");
    }

}