<?php

namespace Imi\Core\App\Contract;

/**
 * 应用类接口.
 */
interface IApp
{
    /**
     * 构造方法.
     *
     * @param string $namespace
     *
     * @return void
     */
    public function __construct(string $namespace);

    /**
     * 加载配置.
     *
     * @return void
     */
    public function loadConfig(): void;

    /**
     * 加载入口.
     *
     * @return void
     */
    public function loadMain(): void;

    /**
     * 加载运行时.
     *
     * @return int
     */
    public function loadRuntime(): int;

    /**
     * 初始化.
     *
     * @return void
     */
    public function init(): void;

    /**
     * 获取应用类型.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * 运行应用.
     *
     * @return void
     */
    public function run(): void;
}
