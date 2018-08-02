<?php
namespace Imi\Log;

use Imi\Config;
use Imi\Worker;
use Imi\Log\Log;
use Imi\Util\File;
use Imi\Bean\Annotation\Bean;

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
        $path = Config::get('@app.beanClassCache', sys_get_temp_dir());
        $this->beanCacheFilePath = File::path($path, 'imiBeanCache', 'imi', str_replace('\\', DIRECTORY_SEPARATOR, __CLASS__) . '.php');
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
				Log::error($errstr, [
					'trace'	=>	$this->getTrace(),
				]);
				break;
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				Log::warning($errstr, [
					'trace'	=>	$this->getTrace(),
				]);
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				Log::notice($errstr, [
					'trace'	=>	$this->getTrace(),
				]);
				break;
			case E_STRICT:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				Log::info($errstr, [
					'trace'	=>	$this->getTrace(),
				]);
				break;
		}
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
				'trace'	=>	$this->getTrace(),
			]);
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