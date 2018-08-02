<?php
namespace Imi\Log;

use Imi\App;
use Imi\Config;
use Imi\Worker;
use Imi\Util\File;
use Imi\Util\Coroutine;
use Imi\Bean\BeanFactory;
use Psr\Log\AbstractLogger;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("Logger")
 */
class Logger extends AbstractLogger
{
	/**
	 * 核心处理器
	 * @var array
	 */
	protected $coreHandlers = [
		[
			'class'		=>	\Imi\Log\Handler\Console::class,
			'options'	=>	[
				'levels'	=>	LogLevel::ALL,
			],
		],
	];

	/**
	 * 扩展处理器
	 * @var array
	 */
	protected $exHandlers = [];

	/**
	 * 处理器对象数组
	 *
	 * @var \Imi\Log\Handler\Base[]
	 */
	protected $handlers = [];

	/**
	 * 日志记录
	 * @var \Imi\Log\Record[]
	 */
    protected $records = [];
    
    /**
     * 当前类在缓存中的文件路径
     *
     * @var string
     */
    private $beanCacheFilePath;

	public function __init()
	{
		foreach(array_merge($this->coreHandlers, $this->exHandlers) as $handlerOption)
		{
			$this->handlers[] = BeanFactory::newInstance($handlerOption['class'], $handlerOption['options']);
        }
        $path = Config::get('@app.beanClassCache', sys_get_temp_dir());
        $this->beanCacheFilePath = File::path($path, 'imiBeanCache', '%s', str_replace('\\', DIRECTORY_SEPARATOR, __CLASS__) . '.php');
	}

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
	public function log($level, $message, array $context = array())
	{
		$context = $this->parseContext($context);
		$trace = $context['trace'];
        $logTime = time();
		$this->records[] = new Record($level, $message, $context, $trace, $logTime);
		if(!Coroutine::isIn())
		{
			$this->endRequest();
		}
	}

	/**
	 * 当请求结束时调用
	 * @return void
	 */
	public function endRequest()
	{
		if(isset($this->records[0]))
		{
			foreach($this->handlers as $handler)
			{
				$handler->logBatch($this->records);
			}
			$this->records = [];
		}
	}

	/**
	 * 强制保存所有日志
	 * @return void
	 */
	public function save()
	{
		foreach($this->handlers as $handler)
		{
			$handler->save();
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
        $beanCacheFilePath = sprintf($this->beanCacheFilePath, Worker::getWorkerID() ?? 'imi');
        foreach($backtrace as $i => $item)
        {
            if(isset($item['file']))
            {
                if($hasNull)
                {
                    if($beanCacheFilePath === $item['file'])
                    {
                        $index = $i + 1;
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

    /**
     * 处理context
     *
     * @param array $context
     * @return array
     */
    private function parseContext($context)
    {
        if(!isset($context['trace']))
        {
            $context['trace'] = $this->getTrace();
        }
        return $context;
    }
}