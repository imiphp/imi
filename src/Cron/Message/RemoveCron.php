<?php
namespace Imi\Cron\Message;

class RemoveCron implements IMessage
{
    /**
     * 任务唯一ID
     *
     * @var string
     */
    public $id;

}