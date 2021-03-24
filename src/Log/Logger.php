<?php

namespace Imi\Log;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Event\Event;
use Imi\Util\Traits\TBeanRealClass;
use Psr\Log\AbstractLogger;

/**
 * @Bean("Logger")
 */
class Logger extends AbstractLogger
{
    use TBeanRealClass;

    /**
     * 核心处理器.
     *
     * @var array
     */
    protected $coreHandlers = [
        [
            'class'     => \Imi\Log\Handler\Console::class,
            'options'   => [
                'levels'    => [
                    LogLevel::INFO,
                ],
                'format'    => '{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}',
            ],
        ],
        [
            'class'     => \Imi\Log\Handler\Console::class,
            'options'   => [
                'levels' => [
                    LogLevel::DEBUG,
                    LogLevel::NOTICE,
                    LogLevel::WARNING,
                ],
            ],
        ],
        [
            'class'     => \Imi\Log\Handler\Console::class,
            'options'   => [
                'levels' => [
                    LogLevel::ALERT,
                    LogLevel::CRITICAL,
                    LogLevel::EMERGENCY,
                    LogLevel::ERROR,
                ],
                'format' => '{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message} {errorFile}:{errorLine}' . \PHP_EOL . 'Stack trace:' . \PHP_EOL . '{trace}',
                'length' => 1024,
            ],
        ],
    ];

    /**
     * 扩展处理器.
     *
     * @var array
     */
    protected $exHandlers = [];

    /**
     * 处理器对象数组.
     *
     * @var \Imi\Log\Handler\Base[]
     */
    protected $handlers = [];

    /**
     * 日志记录.
     *
     * @var \Imi\Log\Record[]
     */
    protected $records = [];

    /**
     * @return void
     */
    public function __init()
    {
        $handlers = &$this->handlers;
        foreach (array_merge($this->coreHandlers, $this->exHandlers) as $handlerOption)
        {
            $handlers[] = BeanFactory::newInstance($handlerOption['class'], $handlerOption['options']);
        }
        Event::on(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.PROCESS.END'], function () {
            $this->save();
        }, \Imi\Util\ImiPriority::IMI_MIN + 1);
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
    public function log($level, $message, array $context = [])
    {
        $context = $this->parseContext($context);
        $trace = $context['trace'];
        $logTime = time();
        $record = new Record($level, $message, $context, $trace, $logTime);
        foreach ($this->handlers as $handler)
        {
            $handler->log($record);
        }
    }

    /**
     * 强制保存所有日志.
     *
     * @return void
     */
    public function save()
    {
        foreach ($this->handlers as $handler)
        {
            $handler->save();
        }
    }

    /**
     * 获取代码调用跟踪.
     *
     * @param array $backtrace
     *
     * @return array
     */
    protected function getTrace(array &$backtrace)
    {
        $index = null;
        $realClassName = static::__getRealClassName();
        foreach ($backtrace as $i => $item)
        {
            $key = $i + 1;
            if (isset($item['file']) && $realClassName === $item['class'] && isset($backtrace[$key]['file']) && 'AbstractLogger.php' !== basename($backtrace[$key]['file']))
            {
                $index = $i + 2;
                break;
            }
        }
        if (null === $index)
        {
            return [];
        }

        return array_splice($backtrace, $index);
    }

    /**
     * 获取错误文件位置.
     *
     * @param array $backtrace
     *
     * @return array
     */
    public function getErrorFile(array $backtrace)
    {
        $index = null;
        $realClassName = static::__getRealClassName();
        foreach ($backtrace as $i => $item)
        {
            $key = $i + 1;
            if (isset($item['file']) && $realClassName === $item['class'] && isset($backtrace[$key]['file']) && 'AbstractLogger.php' !== basename($backtrace[$key]['file']))
            {
                $index = $key;
                break;
            }
        }
        $backTraceItem = $backtrace[$index] ?? null;

        return [$backTraceItem['file'] ?? '', $backTraceItem['line'] ?? 0];
    }

    /**
     * 处理context.
     *
     * @param array $context
     *
     * @return array
     */
    private function parseContext($context)
    {
        $limit = App::getBean('ErrorLog')->getBacktraceLimit();
        if (!isset($context['trace']))
        {
            $backtrace = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit);
            $context['trace'] = $this->getTrace($backtrace);
        }
        if (!isset($context['errorFile']))
        {
            $backtrace = $backtrace ?? debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit);
            list($file, $line) = $this->getErrorFile($backtrace);
            $context['errorFile'] = $file;
            $context['errorLine'] = $line;
        }

        return $context;
    }

    /**
     * 增加扩展处理器.
     *
     * @param array $exHandler
     *
     * @return void
     */
    public function addExHandler($exHandler)
    {
        if (\in_array($exHandler, $this->exHandlers))
        {
            return; // 防止重复添加
        }
        $this->exHandlers[] = $exHandler;
        $this->handlers[] = BeanFactory::newInstance($exHandler['class'], $exHandler['options']);
    }
}
