<?php

namespace Imi\Server\TcpServer\Message;

interface IReceiveData
{
    /**
     * 获取客户端的socket id.
     *
     * @return int
     */
    public function getFd(): int;

    /**
     * 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断.
     *
     * @return string
     */
    public function getData();

    /**
     * 获取格式化后的数据，一般是数组或对象
     *
     * @return mixed
     */
    public function getFormatData();

    /**
     * 获取Reactor线程ID.
     *
     * @return int
     */
    public function getReactorID(): int;
}
