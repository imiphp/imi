<?php
namespace Imi\Tool\Tools\Process;

use Imi\App;
use Imi\Util\Args;
use Imi\Tool\ArgType;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Tool;
use Imi\Process\ProcessManager;
use Imi\Tool\Annotation\Operation;
use Imi\Process\ProcessPoolManager;

/**
 * @Tool("process")
 */
class Process
{
    /**
     * 开启一个进程，可以任意添加参数
     * 
     * @Operation(name="start", co=false)
     *
     * @Arg(name="name", type=ArgType::STRING, required=true, comments="进程名称，通过@Process注解定义")
     * @Arg(name="redirectStdinStdout", type=ArgType::STRING, default=null, comments="重定向子进程的标准输入和输出。启用此选项后，在子进程内输出内容将不是打印屏幕，而是写入到主进程管道。读取键盘输入将变为从管道中读取数据。默认为阻塞读取。")
     * @Arg(name="pipeType", type=ArgType::STRING, default=null, comments="管道类型，启用$redirectStdinStdout后，此选项将忽略用户参数，强制为1。如果子进程内没有进程间通信，可以设置为 0")
     * 
     * @return void
     */
    public function start($name, $redirectStdinStdout, $pipeType)
    {
        // 加载服务器注解
        \Imi\Bean\Annotation::getInstance()->init(\Imi\Main\Helper::getAppMains());
        App::initWorker();
        $args = Args::get();
        $process = ProcessManager::create($name, $args, $redirectStdinStdout, $pipeType);
        $process->start();
        $result = \swoole_process::wait(true);
        echo 'Process exit! pid:', $result['pid'], ', code:', $result['code'], ', signal:', $result['signal'], PHP_EOL;
    }

    /**
     * 开启一个进程池，可以任意添加参数
     *
     * @Operation(name="pool", co=false)
     * 
     * @Arg(name="name", type=ArgType::STRING, required=true, comments="进程池名称，通过@ProcessPool注解定义")
     * @Arg(name="worker", type=ArgType::INT, default=null, comments="进程数量，不传则根据注解配置设定")
     * @Arg(name="ipcType", type=ArgType::INT, default=null, comments="进程间通信的模式，默认为0表示不使用任何进程间通信特性，不传则根据注解配置设定")
     * @Arg(name="msgQueueKey", type=ArgType::STRING, default=null, comments="消息队列键，不传则根据注解配置设定")
     * 
     * @return void
     */
    public function pool($name, $worker, $ipcType, $msgQueueKey)
    {
        // 加载服务器注解
        \Imi\Bean\Annotation::getInstance()->init(\Imi\Main\Helper::getAppMains());
        App::initWorker();
        $args = Args::get();
        $processPool = ProcessPoolManager::create($name, $worker, $args, $ipcType, $msgQueueKey);
        $processPool->start();
    }
}