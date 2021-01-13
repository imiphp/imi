<?php

declare(strict_types=1);

namespace Imi\Cli;

use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Scanner;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Core\App\Contract\BaseApp;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Imi;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CliApp extends BaseApp
{
    /**
     * @var Application
     */
    protected Application $cli;

    /**
     * @var EventDispatcher
     */
    protected EventDispatcher $cliEventDispatcher;

    /**
     * @var ArgvInput
     */
    protected ArgvInput $input;

    /**
     * @var bool
     */
    private bool $inited = false;

    /**
     * 构造方法.
     *
     * @param string $namespace
     *
     * @return void
     */
    public function __construct(string $namespace)
    {
        parent::__construct($namespace);
        App::set(ProcessAppContexts::SCRIPT_NAME, realpath($_SERVER['SCRIPT_FILENAME']));
        $this->input = new ArgvInput();
        $this->cliEventDispatcher = $dispatcher = new EventDispatcher();
        $this->cli = $cli = new Application('imi', App::getImiVersion());
        $cli->setDispatcher($dispatcher);
        $cli->setCatchExceptions(false);

        $definition = $cli->getDefinition();
        $definition->addOption(
            new InputOption(
                'app-namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                'Your project app namespace'
            )
        );
        $definition->addOption(
            new InputOption(
                'imi-runtime',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set imi runtime file',
                null,
            )
        );
        $definition->addOption(
            new InputOption(
                'no-app-cache',
                null,
                InputOption::VALUE_OPTIONAL,
                'Disable app runtime cache',
                false,
            )
        );
    }

    /**
     * 加载运行时.
     *
     * @return int
     */
    public function loadRuntime(): int
    {
        $input = new ArgvInput();
        $isServerStart = ('server/start' === ($_SERVER['argv'][1] ?? null));
        if ($isServerStart)
        {
            $result = false;
        }
        else
        {
            // 尝试加载项目运行时
            $appRuntimeFile = $input->getParameterOption('--app-runtime');
            if (false !== $appRuntimeFile && Imi::loadRuntimeInfo($appRuntimeFile))
            {
                $this->isAppRuntime = true;

                return LoadRuntimeResult::ALL;
            }
        }
        // 尝试加载 imi 框架运行时
        if ($file = $input->getParameterOption('--imi-runtime'))
        {
            // 尝试加载指定 runtime
            $result = Imi::loadRuntimeInfo($file);
        }
        else
        {
            // 尝试加载默认 runtime
            $result = Imi::loadRuntimeInfo(Imi::getRuntimePath('imi-runtime.cache'));
        }
        if (!$result)
        {
            // 不使用缓存时去扫描
            Scanner::scanImi();
            if ($isServerStart)
            {
                Imi::buildRuntime(Imi::getRuntimePath('imi-runtime-bak.cache'));
                $this->isAppRuntime = true;
            }

            return LoadRuntimeResult::IMI_LOADED;
        }

        return $result ? LoadRuntimeResult::ALL : 0;
    }

    /**
     * 初始化.
     *
     * @return void
     */
    public function init(): void
    {
        if ($this->inited)
        {
            return;
        }
        $this->inited = true;
        $this->addCommands();
    }

    private function addCommands(): void
    {
        foreach (AnnotationManager::getAnnotationPoints(Command::class, 'class') as $point)
        {
            /** @var Command $commandAnnotation */
            $commandAnnotation = $point->getAnnotation();
            $className = $point->getClass();
            foreach (AnnotationManager::getMethodsAnnotations($className, CommandAction::class) as $methodName => $commandActionAnnotations)
            {
                $command = new ImiCommand($commandAnnotation, $commandActionAnnotations[0], $className, $methodName);
                if (!$this->cli->has($command->getName()))
                {
                    $this->cli->add($command);
                }
            }
        }
    }

    /**
     * 获取应用类型.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'cli';
    }

    /**
     * 运行应用.
     *
     * @return void
     */
    public function run(): void
    {
        $this->cli->run(new ImiArgvInput());
    }

    /**
     * Get the value of cli.
     *
     * @return Application
     */
    public function getCli(): Application
    {
        return $this->cli;
    }
}
