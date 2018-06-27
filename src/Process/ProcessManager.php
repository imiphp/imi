<?php
namespace Imi\Process;

use Imi\Process\Parser\ProcessParser;
use Imi\Bean\BeanFactory;
use Imi\Event\Event;

/**
 * 进程管理类
 */
abstract class ProcessManager
{
	/**
	 * 创建进程
	 * 本方法无法在控制器中使用
	 * 返回\Swoole\Process对象实例
	 * 
	 * @param string $name
	 * @param array $args
	 * @param boolean $redirectStdinStdout
	 * @param int $pipeType
	 * @return \Swoole\Process
	 */
	public static function create($name, $args = [], $redirectStdinStdout = null, $pipeType = null): \Swoole\Process
	{
		$processOption = ProcessParser::getInstance()->getProcess($name);
		if(null === $redirectStdinStdout)
		{
			$redirectStdinStdout = $processOption['Process']->redirectStdinStdout;
		}
		if(null === $pipeType)
		{
			$pipeType = $processOption['Process']->pipeType;
		}
		$processInstance = BeanFactory::newInstance($processOption['className'], $args);
		$process = new \Swoole\Process(function(\Swoole\Process $swooleProcess) use($processInstance, $name){
			// 进程开始事件
			Event::trigger('IMI.PROCESS.BEGIN', [
				'name'		=>	$name,
				'process'	=>	$swooleProcess,
			]);
			// 执行任务
			call_user_func([$processInstance, 'run'], $swooleProcess);
			// 进程结束事件
			Event::trigger('IMI.PROCESS.END', [
				'name'		=>	$name,
				'process'	=>	$swooleProcess,
			]);
		}, $redirectStdinStdout, $pipeType);
		// 设置进程名称
		$process->name($name);
		return $process;
	}

	/**
	 * 运行进程，同步阻塞等待进程执行返回
	 * 不返回\Swoole\Process对象实例
	 * 执行失败返回false，执行成功返回数组，包含了进程退出的状态码、信号、输出内容。
	 * array(
	 * 	'code' => 0,
	 * 	'signal' => 0,
	 * 	'output' => '',
	 * );
	 *
	 * @param string $name
	 * @param array $args
	 * @param boolean $redirectStdinStdout
	 * @param int $pipeType
	 * @return array
	 */
	public static function run($name, $args = [], $redirectStdinStdout = null, $pipeType = null)
	{
		$cmd = 'php ' . $_SERVER['argv'][0] . ' process/start -name ' . $name;
		if(null !== $redirectStdinStdout)
		{
			$cmd .= ' -redirectStdinStdout ' . $redirectStdinStdout;
		}
		if(null !== $pipeType)
		{
			$cmd .= ' -pipeType ' . $pipeType;
		}
		return \Swoole\Coroutine::exec($cmd);
	}

	/**
	 * 运行进程，创建一个协程执行进程，无法获取进程执行结果
	 * 执行失败返回false，执行成功返回数组，包含了进程退出的状态码、信号、输出内容。
	 * array(
	 * 	'code' => 0,
	 * 	'signal' => 0,
	 * 	'output' => '',
	 * );
	 *
	 * @param string $name
	 * @param array $args
	 * @param boolean $redirectStdinStdout
	 * @param int $pipeType
	 * @return void
	 */
	public static function coRun($name, $args = [], $redirectStdinStdout = null, $pipeType = null)
	{
		go(function() use($name, $args, $redirectStdinStdout, $pipeType){
			static::run($name, $args, $redirectStdinStdout, $pipeType);
		});
	}
}