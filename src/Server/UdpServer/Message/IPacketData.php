<?php

namespace Imi\Server\UdpServer\Message;

interface IPacketData
{
    /**
     * 数据内容.
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
     * 获取客户端信息.
     *
     * @return array
     */
    public function getClientInfo();
}
