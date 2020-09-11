<?php
namespace Imi\Core\App\Contract;

/**
 * 应用类接口
 */
interface IApp
{
    /**
     * 构造方法
     *
     * @param string $namespace
     * @return void
     */
    public function __construct(string $namespace);

    /**
     * 获取应用类型
     *
     * @return string
     */
    public function getType(): string;

    /**
     * 运行应用
     *
     * @return void
     */
    public function run(): void;

}
