<?php

declare(strict_types=1);

namespace Imi\Cli;

use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\Option;
use Imi\Event\Event;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImiCommand extends Command
{
    /**
     * 命令名称.
     */
    protected ?string $commandName = null;

    /**
     * 命令动作名称.
     */
    protected string $actionName = '';

    /**
     * 类名.
     */
    protected string $className = '';

    /**
     * 方法名.
     */
    protected string $methodName = '';

    /**
     * 是否启用动态参数支持.
     */
    protected bool $dynamicOptions = false;

    protected InputInterface $input;

    protected OutputInterface $output;

    /**
     * Get 类名.
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Get 方法名.
     */
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function __construct(?string $commandName, string $actionName, string $className, string $methodName, bool $dynamicOptions = false)
    {
        $this->commandName = $commandName;
        $this->actionName = $actionName;
        $this->className = $className;
        $this->methodName = $methodName;
        $this->dynamicOptions = $dynamicOptions;

        if (null === $commandName)
        {
            $finalCommandName = $actionName ?? $methodName;
        }
        else
        {
            $finalCommandName = $commandName . '/' . ($actionName ?? $methodName);
        }
        parent::__construct($finalCommandName);
    }

    protected function configure(): void
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
            $input->setDynamicOptions($this->dynamicOptions);
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
     */
    protected function executeCommand(): int
    {
        Event::trigger('IMI.COMMAND.BEFORE');
        $instance = new $this->className($this, $this->input, $this->output);
        $args = $this->getCallToolArgs();
        $instance->{$this->methodName}(...$args);
        Event::trigger('IMI.COMMAND.AFTER');

        return Command::SUCCESS;
    }

    /**
     * 获取执行参数.
     */
    private function getCallToolArgs(): array
    {
        $methodRef = new \ReflectionMethod($this->className, $this->methodName);
        $arguments = CliManager::getArguments($this->commandName, $this->actionName);
        $options = CliManager::getOptions($this->commandName, $this->actionName);
        $args = [];
        foreach ($methodRef->getParameters() as $param)
        {
            if (isset($arguments[$param->name]))
            {
                $value = $this->parseArgValue($this->input->getArgument($param->name), $arguments[$param->name]);
            }
            elseif (isset($options[$param->name]))
            {
                $value = $this->parseArgValue($this->input->getOption($param->name), $options[$param->name]);
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
     * @param mixed $value
     * @param array $option
     *
     * @return mixed
     */
    private function parseArgValue($value, $option)
    {
        switch ($option['type'])
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
                if (\is_string($value))
                {
                    $value = (bool) json_decode($value);
                }
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
