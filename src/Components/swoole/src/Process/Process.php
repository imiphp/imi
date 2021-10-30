<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

class Process extends \Swoole\Process
{
    /**
     * 发送消息.
     *
     * @return mixed
     */
    public function sendMessage(string $action, array $data = [])
    {
        $data['a'] = $action;
        $message = json_encode($data, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);

        return $this->write($message);
    }

    /**
     * @param int|null $exitCode
     *
     * @return mixed
     */
    public function exit($exitCode = null)
    {
        if ($this->pid > 0)
        {
            return parent::exit($exitCode);
        }
        else
        {
            exit($exitCode);
        }
    }
}
