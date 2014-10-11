<?php namespace Buonzz\Scalp;

use Psr\Log\LoggerInterface;

class ScalpLogger
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function info($message)
    {
        $this->logger->info($message);
    }
}
