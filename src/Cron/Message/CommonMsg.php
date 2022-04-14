<?php
/*
 * @Author: CHurricane
 * @Date: 2022-04-14 18:51:42
 * @LastEditors: CHurricane
 * @LastEditTime: 2022-04-15 01:01:33
 * @Description: CronManager 传递 通用客户端消息类
 */
declare(strict_types=1);

namespace Imi\Cron\Message;

class CommonMsg implements IMessage
{
    public mixed $response = [];

    public function __construct(mixed $response)
    {
        $this->response = $response;
    }
}
