<?php
namespace Imi\Process;

use Imi\App;
use Imi\Util\File;
use Imi\Event\Event;
use Imi\Bean\BeanFactory;
use Imi\Process\Parser\ProcessParser;
use Imi\Process\Exception\ProcessAlreadyRunException;
use Imi\Util\Imi;

/**
 * 进程管理类
 */
abstract class ProcessManager
{
	/**
	 * 锁集合
	 *
	 * @var array
	 */
	private static $lockMap = [];

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
		if(null === $processOption)
		{
			return null;
		}
		if($processOption['Process']->unique && static::isRunning($name))
		{
			throw new ProcessAlreadyRunException(sprintf('process %s already run', $name));
		}
		if(null === $redirectStdinStdout)
		{
			$redirectStdinStdout = $processOption['Process']->redirectStdinStdout;
		}
		if(null === $pipeType)
		{
			$pipeType = $processOption['Process']->pipeType;
		}
		$processInstance = BeanFactory::newInstance($processOption['className'], $args);
		$process = new \Swoole\Process(function(\Swoole\Process $swooleProcess) use($processInstance, $name, $processOption){
			// 设置进程名称
			$swooleProcess->name($name);
			if($processOption['Process']->unique)
			{
				if(!static::lockProcess($name))
				{
					throw new \RuntimeException('lock process lock file error');
				}
			}
			// 进程开始事件
			Event::trigger('IMI.PROCESS.BEGIN', [
				'name'		=>	$name,
				'process'	=>	$swooleProcess,
			]);
			// 执行任务
			call_user_func([$processInstance, 'run'], $swooleProcess);
			swoole_event_wait();
			if($processOption['Process']->unique)
			{
				static::unlockProcess($name);
			}
			// 进程结束事件
			Event::trigger('IMI.PROCESS.END', [
				'name'		=>	$name,
				'process'	=>	$swooleProcess,
			]);
		}, $redirectStdinStdout, $pipeType);
		return $process;
	}

	/**
	 * 进程是否已在运行，只有unique为true时有效
	 *
	 * @param string $name
	 * @return boolean
	 */
	public static function isRunning($name)
	{
		$processOption = ProcessParser::getInstance()->getProcess($name);
		if(null === $processOption)
		{
			return false;
		}
		if(!$processOption['Process']->unique)
		{
			return false;
		}
		$fileName = static::getLockFileName($name);
		if(!is_file($fileName))
		{
			return false;
		}
		$fp = fopen($fileName, 'w+');
		if(false === $fp)
		{
			return false;
		}
		if(!flock($fp, LOCK_EX | LOCK_NB))
		{
			fclose($fp);
			return true;
		}
		flock($fp, LOCK_UN);
		fclose($fp);
		unlink($fileName);
		return false;
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
		$cmd = Imi::getImiCmd('process', 'start') . ' -name ' . $name;
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

	/**
	 * 锁定进程，实现unique
	 *
	 * @param string $name
	 * @return boolean
	 */
	private static function lockProcess($name)
	{
		$fileName = static::getLockFileName($name);
		$fp = fopen($fileName, 'w+');
		if(false === $fp)
		{
			return false;
		}
		if(!flock($fp, LOCK_EX | LOCK_NB))
		{
			fclose($fp);
			return false;
		}
		static::$lockMap[$name] = [
			'fileName'	=>	$fileName,
			'fp'		=>	$fp,
		];
		return true;
	}

	/**
	 * 解锁进程，实现unique
	 *
	 * @param string $name
	 * @return boolean
	 */
	private static function unlockProcess($name)
	{
		if(!isset(static::$lockMap[$name]))
		{
			return false;
		}
		if(flock(static::$lockMap[$name]['fp'], LOCK_UN) && fclose(static::$lockMap[$name]['fp']))
		{
			unlink(static::$lockMap[$name]['fileName']);
			unset(static::$lockMap[$name]);
			return true;
		}
		return false;
	}

	/**
	 * 获取文件锁的文件名
	 *
	 * @param string $name
	 * @return string
	 */
	private static function getLockFileName($name)
	{
		$path = File::path(sys_get_temp_dir(), str_replace('\\', '-', App::getNamespace()), 'processLock');
		if(!is_dir($path))
		{
			File::createDir($path);
		}
		return File::path($path, $name . '.lock');
	}
}