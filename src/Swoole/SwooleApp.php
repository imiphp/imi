<?php
namespace Imi\Swoole;

use Imi\App;
use Imi\Cli\CliApp;
use Imi\Util\Process\ProcessType;
use Imi\Util\Process\ProcessAppContexts;
use Symfony\Component\Console\ConsoleEvents;

class SwooleApp extends CliApp
{
    /**
     * 获取应用类型
     *
     * @return string
     */
    public function getType(): string
    {
        return 'swoole';
    }

    /**
     * 构造方法
     *
     * @param string $namespace
     * @return void
     */
    public function __construct(string $namespace)
    {
        parent::__construct($namespace);
        $this->cliEventDispatcher->addListener(ConsoleEvents::COMMAND, function(){
            $this->checkEnvironment();
            App::set(ProcessAppContexts::PROCESS_NAME, ProcessType::MASTER, true);
            App::set(ProcessAppContexts::MASTER_PID, getmypid(), true);
            App::set(ProcessAppContexts::SCRIPT_NAME, realpath($_SERVER['SCRIPT_FILENAME']));
            // App::initFramework($this->namespace);
            // if(!isset($_SERVER['argv'][1]))
            // {
            //     echo "Has no operation! You can try the command: \033[33;33m", $_SERVER['argv'][0], " server/start\033[0m", PHP_EOL;
            //     return;
            // }
        }, PHP_INT_MAX);
    }

    /**
     * 检查环境
     *
     * @return void
     */
    private function checkEnvironment()
    {
        // Swoole 检查
        if(!extension_loaded('swoole'))
        {
            echo 'No Swoole extension installed or enabled', PHP_EOL;
            exit;
        }
        // 短名称检查
        $useShortname = ini_get_all('swoole')['swoole.use_shortname']['local_value'];
        $useShortname = strtolower(trim(str_replace('0', '', $useShortname)));
        if (in_array($useShortname, ['', 'off', 'false'], true))
        {
            echo 'Please enable swoole short name before using imi!', PHP_EOL, 'You can set swoole.use_shortname = on into your php.ini.', PHP_EOL;
            exit;
        }
    }

}
