<?php

declare(strict_types=1);

namespace Imi\Core\App\Contract;

/**
 * 应用类接口.
 */
interface IApp
{
    /**
     * 构造方法.
     */
    public function __construct(string $namespace);

    /**
     * 加载配置.
     */
    public function loadConfig(): void;

    /**
     * 加载入口.
     */
    public function loadMain(): void;

    /**
     * 加载运行时.
     */
    public function loadRuntime(): int;

    /**
     * 初始化.
     */
    public function init(): void;

    /**
     * 获取应用类型.
     */
    public function getType(): string;

    /**
     * 运行应用.
     */
    public function run(): void;
}
