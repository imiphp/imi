<?php

declare(strict_types=1);

namespace Imi\Log\Handler;

abstract class Base
{
    /**
     * 日志记录.
     *
     * @var \Imi\Log\Record[]
     */
    protected $records = [];

    /**
     * 允许记录的日志等级们.
     *
     * @var string[]
     */
    protected $levels = [];

    /**
     * 日志缓存数量
     * 当日志达到指定条数时，执行批量写入操作，减少对性能的影响.
     *
     * @var int
     */
    protected $logCacheNumber = 1;

    /**
     * 日志格式.
     *
     * @var string
     */
    protected $format = '{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message} {errorFile}:{errorLine}';

    /**
     * 调用跟踪格式.
     *
     * @var string
     */
    protected $traceFormat = '#{index}  {call} called at [{file}:{line}]';

    /**
     * 限制 trace 条目数量，默认为 -1 不限制.
     *
     * @var int
     */
    protected $traceLimit = -1;

    /**
     * date()函数支持的格式.
     */
    const DATE_FORMATS = [
        'd',
        'D',
        'j',
        'l',
        'N',
        'S',
        'w',
        'z',
        'W',
        'F',
        'm',
        'M',
        'n',
        't',
        'L',
        'o',
        'Y',
        'y',
        'a',
        'A',
        'B',
        'g',
        'G',
        'h',
        'H',
        'i',
        's',
        'u',
        'e',
        'I',
        'O',
        'P',
        'T',
        'Z',
        'c',
        'r',
        'U',
    ];

    /**
     * date()函数支持的格式缓存文本.
     *
     * @var string
     */
    private $dateFormatsCacheStr;

    public function __construct($option = [])
    {
        foreach ($option as $k => $v)
        {
            $this->$k = $v;
        }
        $this->dateFormatsCacheStr = implode('#', static::DATE_FORMATS);
    }

    /**
     * 写日志.
     *
     * @param \Imi\Log\Record $record
     *
     * @return void
     */
    public function log(\Imi\Log\Record $record)
    {
        $records = &$this->records;
        if (\in_array($record->getLevel(), $this->levels))
        {
            $records[] = $record;
            $this->trySave();
        }
    }

    /**
     * 批量写日志.
     *
     * @param \Imi\Log\Record[] $logs
     *
     * @return void
     */
    public function logBatch(array $logs)
    {
        foreach ($logs as $log)
        {
            $this->log($log);
        }
    }

    /**
     * 尝试保存日志，当满足保存条件时才保存.
     *
     * @return void
     */
    public function trySave()
    {
        if (isset($this->records[$this->logCacheNumber - 1]))
        {
            $this->save();
        }
    }

    /**
     * 保存日志，直接写入.
     *
     * @return void
     */
    public function save()
    {
        $this->__save();
        $this->records = [];
    }

    /**
     * 真正的保存操作实现.
     *
     * @return void
     */
    abstract protected function __save();

    /**
     * 获取日期时间.
     *
     * @param string $time 不传则使用当前时间
     *
     * @return string
     */
    public function getDateTime($time = null)
    {
        if (null === $time)
        {
            $time = time();
        }

        return date($this->dateTimeFormat, $time);
    }

    /**
     * 获取日志字符串.
     *
     * @param \Imi\Log\Record $record
     *
     * @return string
     */
    public function getLogString(\Imi\Log\Record $record)
    {
        $logTime = $record->getLogTime();
        $vars = [
            'message'       => $record->getMessage(),
            'level'         => $record->getLevel(),
            'timestamp'     => $logTime,
            'trace'         => $this->parseTrace($record),
        ];

        $replaces = [];
        foreach ($vars as $key => $value)
        {
            if (is_scalar($value))
            {
                $replaces['{' . $key . '}'] = $value;
            }
        }
        foreach ($record->getContext() as $key => $value)
        {
            if (is_scalar($value))
            {
                $replaces['{' . $key . '}'] = $value;
            }
        }
        $logContent = strtr($this->format, $replaces);

        return $this->replaceDateTime($logContent, $logTime);
    }

    /**
     * 处理代码调用跟踪.
     *
     * @param \Imi\Log\Record $record
     *
     * @return string
     */
    public function parseTrace(\Imi\Log\Record $record)
    {
        $result = [];
        $trace = $record->getTrace();
        $traceLimit = $this->traceLimit;
        $traceFormat = $this->traceFormat;
        foreach ($trace as $index => $vars)
        {
            if ($traceLimit > -1 && $index >= $traceLimit)
            {
                break;
            }
            $vars['call'] = $this->getTraceCall($vars);
            $vars['index'] = $index;
            $replaces = [];
            foreach ($vars as $name => $value)
            {
                if (is_scalar($value))
                {
                    $replaces['{' . $name . '}'] = (string) $value;
                }
            }
            $result[] = strtr($traceFormat, $replaces);
        }

        return implode(\PHP_EOL, $result);
    }

    /**
     * 获取调用跟踪的调用.
     *
     * @return string
     */
    public function getTraceCall($trace)
    {
        $call = '';
        if (isset($trace['class'], $trace['type']))
        {
            // 匿名类必须用NULL分割一下
            list($class) = explode("\0", $trace['class']);
            $call .= $class . $trace['type'];
        }
        if (isset($trace['function']))
        {
            $call .= $trace['function'] . '(' . $this->getTraceArgs($trace) . ')';
        }

        return $call;
    }

    /**
     * 获取调用跟踪的方法参数.
     *
     * @return string
     */
    public function getTraceArgs($trace)
    {
        $result = [];
        foreach ($trace['args'] ?? [] as $value)
        {
            if (is_scalar($value))
            {
                $result[] = (string) $value;
            }
            else
            {
                $result[] = \gettype($value);
            }
        }

        return implode(', ', $result);
    }

    /**
     * 替换日期时间参数.
     *
     * @param string $string
     * @param int    $timestamp
     *
     * @return void
     */
    protected function replaceDateTime($string, $timestamp)
    {
        $list = explode('#', date($this->dateFormatsCacheStr, $timestamp));
        $replaces = [];
        foreach ($list as $i => $item)
        {
            $replaces['{' . static::DATE_FORMATS[$i] . '}'] = $item;
        }

        return strtr($string, $replaces);
    }
}
