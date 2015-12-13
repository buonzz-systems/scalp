<?php namespace Buonzz\Scalp\Commands\Import;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
// use Elasticsearch\ClientBuilder;

class ElasticCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('import:elastic')
            ->setDescription('Accepts a folder, then store the metadata info to ElasticSearch');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(!file_exists("./scalp.yaml")){
            $output->writeln("<error>scalp.yaml not found in current directory. Please create one first</error>");
            exit();
        }

        $yaml = new Parser();
        // $client = ClientBuilder::create()->build();

        try {
            $config = $yaml->parse(file_get_contents('./scalp.yaml'));
        } catch (ParseException $e) {
            $output->writeln("Unable to parse the YAML string: %s", $e->getMessage());
        }


        $output->writeln("Hello: " . $config["ES_HOST"]);               
    }
}