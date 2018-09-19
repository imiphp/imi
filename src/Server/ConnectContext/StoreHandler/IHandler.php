<?php
namespace Imi\Server\ConnectContext\StoreHandler;

/**
 * 连接上下文存储处理器
 */
interface IHandler
{
    /**
     * 读取数据
     *
     * @param string $key
     * @return array
     */
    public function read(string $key): array;

    /**
     * 保存数据
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function save(string $key, array $data);

    /**
     * 销毁数据
     *
     * @param string $key
     * @return void
     */
    public function destroy(string $key);

    /**
     * 数据是否存在
     *
     * @param string $key
     * @return void
     */
    public function exists(string $key);

}