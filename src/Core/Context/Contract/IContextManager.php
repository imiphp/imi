<?php
namespace Imi\Core\Context\Contract;

use ArrayObject;

interface IContextManager
{
    /**
     * 创建上下文
     *
     * @param string $flag
     * @param array $data
     * @return \ArrayObject
     */
    public function create(string $flag, array $data = []): ArrayObject;

    /**
     * 销毁上下文
     *
     * @param string $flag
     * @return boolean
     */
    public function destroy(string $flag): bool;

    /**
     * 获取上下文
     *
     * @param string $flag
     * @param boolean $autoCreate
     * @return \ArrayObject
     */
    public function get(string $flag, bool $autoCreate = false): ArrayObject;

    /**
     * 上下文是否存在
     *
     * @param string $flag
     * @return boolean
     */
    public function exists(string $flag): bool;

    /**
     * 获取当前上下文标识
     *
     * @return string
     */
    public function getCurrentFlag(): string;

}
