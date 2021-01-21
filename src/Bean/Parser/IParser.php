<?php

declare(strict_types=1);

namespace Imi\Bean\Parser;

interface IParser
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
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName);

    /**
     * 获取数据.
     *
     * @return array
     */
    public function getData(): array;

    /**
     * 设置数据.
     *
     * @param array $data
     *
     * @return void
     */
    public function setData(array $data);
}
