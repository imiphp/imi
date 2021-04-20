<?php

declare(strict_types=1);

namespace Imi\Log\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleHandler extends AbstractProcessingHandler
{
    protected OutputInterface $output;

    public function __construct(?OutputInterface $output = null)
    {
        $this->output = $output ?? new ConsoleOutput();
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record): void
    {
        $this->output->write((string) $record['formatted']);
    }
}
