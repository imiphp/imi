<?php
/*
 * @Author: CHurricane
 * @Date: 2022-04-14 19:21:42
 * @LastEditors: CHurricane
 * @LastEditTime: 2022-04-15 01:00:25
 * @Description: CronManager 传递 IsRunning 消息类
 */
declare(strict_types=1);

namespace Imi\Cron\Message;

class IsRunning implements IMessage
{
    /**
     * 任务
     */
    public string $task = '';
}
