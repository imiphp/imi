<?php

declare(strict_types=1);

namespace Imi\Swoole\Util;

use Imi\App;
use Imi\Config;
use Imi\Worker;

class Imi
{
    private function __construct()
    {
    }

    /**
     * 设置当前进程名.
     *
     * @param string $type
     * @param array  $data
     *
     * @return void
     */
    public static function setProcessName(string $type, array $data = [])
    {
        if ('Darwin' === \PHP_OS)
        {
            // 苹果 MacOS 不允许设置进程名
            return;
        }
        cli_set_process_title(static::getProcessName($type, $data));
    }

    /**
     * 获取 imi 进程名
     * 返回false则失败.
     *
     * @param string $type
     * @param array  $data
     *
     * @return string
     */
    public static function getProcessName(string $type, array $data = []): string
    {
        static $defaults = [
            'master'        => 'imi:master:{namespace}',
            'manager'       => 'imi:manager:{namespace}',
            'worker'        => 'imi:worker-{workerId}:{namespace}',
            'taskWorker'    => 'imi:taskWorker-{workerId}:{namespace}',
            'process'       => 'imi:process-{processName}:{namespace}',
            'processPool'   => 'imi:process-pool-{processPoolName}-{workerId}:{namespace}',
        ];
        if (!isset($defaults[$type]))
        {
            return false;
        }
        $rule = Config::get('@app.process.' . $type, $defaults[$type]);
        $data['namespace'] = App::getNamespace();
        switch ($type)
        {
            case 'master':
                break;
            case 'manager':
                break;
            case 'worker':
                $data['workerId'] = Worker::getWorkerId();
                break;
            case 'taskWorker':
                $data['workerId'] = Worker::getWorkerId();
                break;
            case 'process':
                if (!isset($data['processName']))
                {
                    return false;
                }
                break;
            case 'processPool':
                if (!isset($data['processPoolName'], $data['workerId']))
                {
                    return false;
                }
                break;
        }
        $result = $rule;
        foreach ($data as $k => $v)
        {
            if (!is_scalar($v))
            {
                continue;
            }
            $result = str_replace('{' . $k . '}', (string) $v, $result);
        }

        return $result;
    }
}
