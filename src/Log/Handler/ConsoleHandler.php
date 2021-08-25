<?php

declare(strict_types=1);

namespace Imi\Log\Handler;

use Imi\Cli\ImiCommand;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritDoc}
 */
class ConsoleHandler extends AbstractProcessingHandler
{
    protected OutputInterface $output;

    public function __construct(?OutputInterface $output = null)
    {
        $this->output = $output ?? ImiCommand::getOutput();
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record): void
    {
        $this->output->write((string) $record['formatted']);
    }
}
