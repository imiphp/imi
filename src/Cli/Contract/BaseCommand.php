<?php

declare(strict_types=1);

namespace Imi\Cli\Contract;

use Imi\Cli\ImiCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand
{
    public function __construct(protected ImiCommand $command, protected InputInterface $input, protected OutputInterface $output)
    {
    }
}
