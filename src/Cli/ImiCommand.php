<?php

declare(strict_types=1);

namespace Imi\Cli;

use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\Command as CommandAnnotation;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\Parser\ToolParser;
use Imi\Event\Event;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImiCommand extends Command
{
    /**
     * 类名.
     *
     * @var string
     */
    protected string $className;

    /**
     * 方法名.
     *
     * @var string
     */
    protected string $methodName;

    /**
     * @var \Imi\Cli\Annotation\Command
     */
    protected CommandAnnotation $commandAnnotation;

    /**
     * @var \Imi\Cli\Annotation\CommandAction
     */
    protected CommandAction $commandActionAnnotation;

    /**
     * @var InputInterface
     */
    protected InputInterface $input;

    /**
     * @var OutputInterface
     */
    protected OutputInterface $output;

    /**
     * Get 类名.
     *
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Get 方法名.
     *
     * @return string
     */
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function __construct(CommandAnnotation $commandAnnotation, CommandAction $commandActionAnnotation, string $className, string $methodName)
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->commandAnnotation = $commandAnnotation;
        $this->commandActionAnnotation = $commandActionAnnotation;
        if (null === $commandAnnotation->name)
        {
            $commandName = $commandActionAnnotation->name ?? $methodName;
        }
        else
        {
            $commandName = $commandAnnotation->name . '/' . ($commandActionAnnotation->name ?? $methodName);
        }
        parent::__construct($commandName);
    }

    protected function configure()
    {
        foreach (AnnotationManager::getMethodAnnotations($this->className, $this->methodName, Argument::class) as $argumentAnnotation)
        {
            /** @var Argument $argumentAnnotation */
            $type = $argumentAnnotation->required ? InputArgument::REQUIRED : InputArgument::OPTIONAL;
            if (ArgType::ARRAY === $argumentAnnotation->type)
            {
                $type |= InputArgument::IS_ARRAY;
            }
            $this->addArgument($argumentAnnotation->name, $type, $argumentAnnotation->comments, $argumentAnnotation->default);
        }
        foreach (AnnotationManager::getMethodAnnotations($this->className, $this->methodName, Option::class) as $optionAnnotation)
        {
            /** @var Option $optionAnnotation */
            $mode = $optionAnnotation->required ? InputOption::VALUE_REQUIRED : InputArgument::OPTIONAL;
            if (ArgType::ARRAY === $optionAnnotation->type)
            {
                $mode |= InputOption::VALUE_IS_ARRAY;
            }
            $this->addOption($optionAnnotation->name, $optionAnnotation->shortcut, $mode, $optionAnnotation->comments, $optionAnnotation->default);
        }
    }

    /**
     * Runs the command.
     *
     * The code to execute is either defined directly with the
     * setCode() method or by overriding the execute() method
     * in a sub-class.
     *
     * @return int The command exit code
     *
     * @throws \Exception When binding input fails. Bypass this by calling {@link ignoreValidationErrors()}.
     *
     * @see setCode()
     * @see execute()
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        if ($input instanceof ImiArgvInput)
        {
            $input->setDynamicOptions($this->commandActionAnnotation->dynamicOptions);
        }

        return parent::run($input, $output);
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return int 0 if everything went fine, or an exit code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        App::set(CliAppContexts::COMMAND_NAME, $this->getName(), true);

        return $this->executeCommand();
    }

    /**
     * 执行命令行.
     *
     * @return int
     */
    protected function executeCommand(): int
    {
        try
        {
            Event::trigger('IMI.COMMAND.BEFORE');
            $instance = new $this->className($this, $this->input, $this->output);
            $args = $this->getCallToolArgs();
            $instance->{$this->methodName}(...$args);
            Event::trigger('IMI.COMMAND.AFTER');
        }
        catch (\Throwable $th)
        {
            /** @var \Imi\Log\ErrorLog $errorLog */
            $errorLog = App::getBean('ErrorLog');
            $errorLog->onException($th);
            throw $th;
        }

        return Command::SUCCESS;
    }

    /**
     * 获取执行参数.
     *
     * @return array
     */
    private function getCallToolArgs(): array
    {
        $methodRef = new \ReflectionMethod($this->className, $this->methodName);
        $argumentAnnotations = ToolParser::getInstance()->getData()['class'][$this->className]['Methods'][$this->methodName]['Arguments'] ?? [];
        $args = [];
        $optionAnnotations = ToolParser::getInstance()->getData()['class'][$this->className]['Methods'][$this->methodName]['Options'] ?? [];
        $args = [];
        foreach ($methodRef->getParameters() as $param)
        {
            if (isset($argumentAnnotations[$param->name]))
            {
                $annotation = $argumentAnnotations[$param->name];
                $value = $this->parseArgValue($this->input->getArgument($param->name), $annotation);
            }
            elseif (isset($optionAnnotations[$param->name]))
            {
                $annotation = $optionAnnotations[$param->name];
                $value = $this->parseArgValue($this->input->getOption($param->name), $annotation);
            }
            else
            {
                $value = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
            }
            $args[] = $value;
        }

        return $args;
    }

    /**
     * 处理参数值
     *
     * @param mixed                                                   $value
     * @param \Imi\Cli\Annotation\Argument|\Imi\Cli\Annotation\Option $annotation
     *
     * @return mixed
     */
    private function parseArgValue($value, $annotation)
    {
        switch ($annotation->type)
        {
            case ArgType::STRING:
                break;
            case ArgType::INT:
                $value = (int) $value;
                break;
            case ArgType::FLOAT:
            case ArgType::DOUBLE:
                $value = (float) $value;
                break;
            case ArgType::BOOL:
            case ArgType::BOOLEAN:
                $value = (bool) json_decode($value);
                break;
            case ArgType::ARRAY:
                if (!\is_array($value))
                {
                    $value = explode(',', $value);
                }
                break;
        }

        return $value;
    }
}
