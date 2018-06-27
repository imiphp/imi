<?php
namespace Imi\Tool\Tools\Process;

use Imi\Tool\ArgType;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Tool;
use Imi\Process\ProcessManager;
use Imi\Tool\Annotation\Operation;
use Imi\Util\Args;

/**
 * @Tool("process")
 */
class Process
{
	/**
	 * 开启一个进程，可以任意添加参数
	 * 
	 * @Operation("start")
	 *
	 * @Arg(name="name", type=ArgType::STRING, required=true, comments="进程名称")
	 * @Arg(name="redirectStdinStdout", type=ArgType::STRING, default=null, comments="重定向子进程的标准输入和输出。启用此选项后，在子进程内输出内容将不是打印屏幕，而是写入到主进程管道。读取键盘输入将变为从管道中读取数据。默认为阻塞读取。")
	 * @Arg(name="pipeType", type=ArgType::STRING, default=null, comments="管道类型，启用$redirectStdinStdout后，此选项将忽略用户参数，强制为1。如果子进程内没有进程间通信，可以设置为 0")
	 * 
	 * @return void
	 */
	public function start($name, $redirectStdinStdout, $pipeType)
	{
		$args = Args::get();
		$process = ProcessManager::create($name, $args, $redirectStdinStdout, $pipeType);
		$process->start();
		$result = \swoole_process::wait(true);
		echo 'process exit! pid:', $result['pid'], ', code:', $result['code'], ', signal:', $result['signal'], PHP_EOL;
	}
}