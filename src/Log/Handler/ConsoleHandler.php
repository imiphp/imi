<?php

declare(strict_types=1);

namespace Imi\Log\Handler;

use Imi\Cli\ImiCommand;
use Imi\Log\MonoLogger;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritDoc}
 */
class ConsoleHandler extends AbstractProcessingHandler
{
    protected OutputInterface $output;

    /**
     * {@inheritDoc}
     */
    public function __construct(?OutputInterface $output = null, $level = MonoLogger::DEBUG, bool $bubble = true)
    {
        $this->output = $output ?? ImiCommand::getOutput();
        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record): void
    {
        $this->output->write((string) $record['formatted']);
    }
}
