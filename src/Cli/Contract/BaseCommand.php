<?php
namespace Imi\Cli\Contract;

use Imi\Cli\ImiSymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand
{
    /**
     * @var ImiSymfonyCommand
     */
    protected ImiSymfonyCommand $command;

    /**
     * @var InputInterface
     */
    protected InputInterface $input;

    /**
     * @var OutputInterface
     */
    protected OutputInterface $output;

    public function __construct(ImiSymfonyCommand $command, InputInterface $input, OutputInterface $output)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
    }

}