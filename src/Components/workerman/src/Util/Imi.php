<?php

declare(strict_types=1);

namespace Imi\Workerman\Util;

use Imi\App;
use Imi\Config;
use Imi\Worker;

class Imi
{
    use \Imi\Util\Traits\TStaticClass;

    public const DEFAULT_PROCESS_NAMES = [
        'master'        => 'imi:master:{namespace}',
        'worker'        => 'imi:worker-{workerId}:{namespace}',
        'process'       => 'imi:process-{processName}:{namespace}',
    ];

    /**
     * 设置当前进程名.
     */
    public static function setProcessName(string $type, array $data = []): void
    {
        if ('Darwin' === \PHP_OS)
        {
            // 苹果 MacOS 不允许设置进程名
            return;
        }
        cli_set_process_title(static::getProcessName($type, $data));
    }

    /**
     * 获取 imi 进程名.
     */
    public static function getProcessName(string $type, array $data = []): string
    {
        if (!isset(self::DEFAULT_PROCESS_NAMES[$type]))
        {
            return '';
        }
        $rule = Config::get('@app.process.' . $type, self::DEFAULT_PROCESS_NAMES[$type]);
        $data['namespace'] = App::getNamespace();
        switch ($type)
        {
            case 'master':
                break;
            case 'worker':
                $data['workerId'] = Worker::getWorkerId();
                break;
            case 'process':
                if (!isset($data['processName']))
                {
                    return '';
                }
                break;
        }
        $result = $rule;
        foreach ($data as $k => $v)
        {
            if (!\is_scalar($v))
            {
                continue;
            }
            $result = str_replace('{' . $k . '}', (string) $v, $result);
        }

        return $result;
    }
}
