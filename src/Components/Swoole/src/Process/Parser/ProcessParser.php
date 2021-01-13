<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Swoole\Process\Annotation\Process;

class ProcessParser extends BaseParser
{
    /**
     * 处理方法.
     *
     * @param \Imi\Bean\Annotation\Base $annotation 注解类
     * @param string                    $className  类名
     * @param string                    $target     注解目标类型（类/属性/方法）
     * @param string                    $targetName 注解目标名称
     *
     * @return void
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName)
    {
        if ($annotation instanceof Process)
        {
            $data = &$this->data;
            if (isset($data[$annotation->name]) && $data[$annotation->name]['className'] != $className)
            {
                throw new \RuntimeException(sprintf('Process %s is exists', $annotation->name));
            }
            $data[$annotation->name] = [
                'className' => $className,
                'Process'   => $annotation,
            ];
        }
    }

    /**
     * 获取process信息.
     *
     * @param string $name process名称
     *
     * @return array|null
     */
    public function getProcess(string $name): ?array
    {
        return $this->data[$name] ?? null;
    }
}
