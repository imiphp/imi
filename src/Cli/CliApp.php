<?php
namespace Imi\Cli;

use Imi\App;
use Imi\Event\Event;
use Imi\Cli\ImiSymfonyCommand;
use Imi\Core\Contract\BaseApp;
use Imi\Cli\Parser\ToolParser;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\Operation;
use Imi\Cli\Annotation\CommandAction;
use Imi\Bean\Annotation\AnnotationManager;
use Symfony\Component\Console\Application;
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
     * 构造方法
     *
     * @param string $namespace
     * @return void
     */
    public function __construct(string $namespace)
    {
        parent::__construct($namespace);
        $this->cliEventDispatcher = $dispatcher = new EventDispatcher;
        $this->cli = $cli = new Application('imi', App::getImiVersion());
        $cli->setDispatcher($dispatcher);
        Event::one('IMI.INITED', function() use($cli){
            foreach(AnnotationManager::getAnnotationPoints(Command::class, 'class') as $point)
            {
                /** @var Command $commandAnnotation */
                $commandAnnotation = $point->getAnnotation();
                $className = $point->getClass();
                foreach(AnnotationManager::getMethodsAnnotations($className, CommandAction::class) as $methodName => $commandActionAnnotations)
                {
                    $cli->add(new ImiSymfonyCommand($commandAnnotation, $commandActionAnnotations[0], $className, $methodName));
                }
            }
            Tool::init();
        });
    }

    /**
     * 获取应用类型
     *
     * @return string
     */
    public function getType(): string
    {
        return 'cli';
    }

    /**
     * 运行应用
     *
     * @return void
     */
    public function run(): void
    {
        $this->cli->run();
    }

    /**
     * Get the value of cli
     *
     * @return Application
     */ 
    public function getCli(): Application
    {
        return $this->cli;
    }

}
