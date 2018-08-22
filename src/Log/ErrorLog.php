<?php
namespace Imi\Log;

use Imi\App;
use Imi\Config;
use Imi\Worker;
use Imi\Log\Log;
use Imi\Util\File;
use Imi\RequestContext;
use Imi\Pool\PoolManager;
use Imi\Bean\Annotation\Bean;
use Imi\Util\Imi;

/**
 * @Bean("ErrorLog")
 */
class ErrorLog
{
    /**
     * 当前类在缓存中的文件路径
     *
     * @var string
     */
	private $beanCacheFilePath;

	/**
	 * 注册错误监听
	 *
	 * @return void
	 */
	public function register()
	{
		error_reporting(0);
		$this->beanCacheFilePath = Imi::getImiClassCachePath(str_replace('\\', DIRECTORY_SEPARATOR, __CLASS__) . '.php');
		register_shutdown_function([$this, 'onShutdown']);
		set_error_handler([$this, 'onError']);
	}

	/**
	 * 错误
	 *
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 * @return void
	 */
	public function onError($errno, $errstr, $errfile, $errline)
	{
		switch($errno)
		{
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
				$method = 'error';
				break;
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				$method = 'warning';
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$method = 'notice';
				break;
			case E_STRICT:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$method = 'info';
				break;
		}
		Log::$method($errstr, [
			'trace'		=>	$this->getTrace(),
			'errorFile'	=>	$errfile,
			'errorLine'	=>	$errline,
		]);
	}

	/**
	 * 致命错误
	 *
	 * @return void
	 */
	public function onShutdown()
	{
		$e = error_get_last();
		if (in_array($e['type'], [
			E_ERROR,
			E_PARSE,
			E_CORE_ERROR,
			E_COMPILE_ERROR,
			E_USER_ERROR,
			E_RECOVERABLE_ERROR
		]))
		{
			Log::error($e['message'], [
				'trace'	=>	[],
			]);
		}
		$logger = App::getBean('Logger');
		$logger->save();
		exit;
	}

	/**
	 * 致命错误
	 *
	 * @return void
	 */
	public function onException(\Throwable $ex)
	{
		// 日志处理
		Log::error($ex->getMessage(), [
			'trace'		=>	$ex->getTrace(),
			'errorFile'	=>	$ex->getFile(),
			'errorLine'	=>	$ex->getLine(),
		]);
		// 请求上下文处理
		if(RequestContext::exsits())
		{
			// 释放请求的进程池资源
			PoolManager::destroyCurrentContext();
			RequestContext::destroy();
		}
	}

	/**
	 * 获取代码调用跟踪
	 * @return array
	 */
	protected function getTrace()
	{
		$backtrace = debug_backtrace();
        $index = null;
		$hasNull = false;
        foreach($backtrace as $i => $item)
        {
            if(isset($item['file']))
            {
                if($hasNull)
                {
                    if($this->beanCacheFilePath === $item['file'])
                    {
                        $index = $i + 2;
                        break;
                    }
                }
            }
            else
            {
                $hasNull = true;
            }
		}
        if(null === $index)
        {
            return [];
		}
		return array_splice($backtrace, $index);
	}
}