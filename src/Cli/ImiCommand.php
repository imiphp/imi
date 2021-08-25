<?php

declare(strict_types=1);

namespace Imi\Cli;

use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\CommandAction;
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

    protected array $argumentsDefinition = [];

    protected array $optionsDefinition = [];

    protected static ImiArgvInput $inputInstance;

    protected static ImiConsoleOutput $outputInstance;

    public static function getInput(): ImiArgvInput
    {
        return static::$inputInstance ??= new ImiArgvInput();
    }

    public static function getOutput(): ImiConsoleOutput
    {
        return static::$outputInstance ??= new ImiConsoleOutput();
    }

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
        /** @var CommandAction $commandAction */
        $commandAction = AnnotationManager::getMethodAnnotations($this->className, $this->methodName, CommandAction::class)[0] ?? null;
        if (null !== $commandAction)
        {
            $this->setDescription($commandAction->description ?? '');
        }
        /** @var Argument $argumentAnnotation */
        foreach (AnnotationManager::getMethodAnnotations($this->className, $this->methodName, Argument::class) as $argumentAnnotation)
        {
            $mode = $argumentAnnotation->required ? InputArgument::REQUIRED : InputArgument::OPTIONAL;
            if (ArgType::ARRAY === $argumentAnnotation->type || ArgType::ARRAY_EX === $argumentAnnotation->type)
            {
                $mode |= InputArgument::IS_ARRAY;
            }
            $this->addArgument($argumentAnnotation->name, $mode, $argumentAnnotation->comments, $argumentAnnotation->default);
        }
        /** @var Option $optionAnnotation */
        foreach (AnnotationManager::getMethodAnnotations($this->className, $this->methodName, Option::class) as $optionAnnotation)
        {
            if (ArgType::BOOLEAN_NEGATABLE === $optionAnnotation->type)
            {
                $mode = InputOption::VALUE_NEGATABLE;
            }
            else
            {
                $mode = $optionAnnotation->required ? InputOption::VALUE_REQUIRED : InputOption::VALUE_OPTIONAL;
                if (ArgType::ARRAY === $optionAnnotation->type || ArgType::ARRAY_EX === $optionAnnotation->type)
                {
                    $mode |= InputOption::VALUE_IS_ARRAY;
                }
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

        static::$inputInstance = $input;
        static::$outputInstance = $output;

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
     * @throws \LogicException When this abstract method is not implemented
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
        $args = $this->getCallToolArgs();
        $input = $this->input;
        if ($input instanceof ImiArgvInput)
        {
            $input->parseByCommand($this);
        }
        $instance = new $this->className($this, $input, $this->output);
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
        $this->argumentsDefinition = $arguments = CliManager::getArguments($this->commandName, $this->actionName);
        $this->optionsDefinition = $options = CliManager::getOptions($this->commandName, $this->actionName);
        $args = [];
        foreach ($methodRef->getParameters() as $param)
        {
            $paramArgumentName = null;
            foreach ($arguments as $argument)
            {
                if ($param->name === $argument['to'])
                {
                    $paramArgumentName = $argument['argumentName'];
                    break;
                }
            }
            if (null === $paramArgumentName && isset($arguments[$param->name]))
            {
                $paramArgumentName = $param->name;
            }
            if (null !== $paramArgumentName)
            {
                $argument = $arguments[$paramArgumentName];
                $value = $this->parseArgValue($this->input->getArgument($argument['argumentName']), $argument);
            }
            else
            {
                $paramOptionName = null;
                foreach ($options as $option)
                {
                    if ($param->name === $option['to'])
                    {
                        $paramOptionName = $option['optionName'];
                        break;
                    }
                }
                if (null === $paramOptionName && isset($options[$param->name]))
                {
                    $paramOptionName = $param->name;
                }
                if (null !== $paramOptionName)
                {
                    $option = $options[$paramOptionName];
                    $value = $this->parseArgValue($this->input->getOption($option['optionName']), $option);
                    if (ArgType::isBooleanType($option['type']) && null === $value)
                    {
                        if ($this->input->hasParameterOption('--' . $option['optionName']) || (null !== $option['shortcut'] && $this->input->hasParameterOption('-' . $option['shortcut'])))
                        {
                            $value = true;
                        }
                    }
                }
                else
                {
                    $value = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                }
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
            case ArgType::ARRAY_EX:
                if (\is_array($value) && !isset($value[1]) && isset($value[0]))
                {
                    $value = explode(',', $value[0]);
                }
                break;
        }

        return $value;
    }

    public function getArgumentsDefinition(): array
    {
        return $this->argumentsDefinition;
    }

    public function getOptionsDefinition(): array
    {
        return $this->optionsDefinition;
    }
}
