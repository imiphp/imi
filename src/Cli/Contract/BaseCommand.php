<?php

declare(strict_types=1);

namespace Imi\Cli\Contract;

use Imi\Cli\ImiCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand
{
    protected ImiCommand $command;

    protected InputInterface $input;

    protected OutputInterface $output;

    public function __construct(ImiCommand $command, InputInterface $input, OutputInterface $output)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
    }
}
