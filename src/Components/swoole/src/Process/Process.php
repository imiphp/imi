<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

trait TProcess
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
     */
    public function exit($exitCode = 0): void
    {
        if ($this->pid > 0)
        {
            parent::exit($exitCode);
        }
        else
        {
            exit($exitCode);
        }
    }
}

// @phpstan-ignore-next-line
if (\SWOOLE_VERSION_ID >= 50000)
{
    include __DIR__ . '/Process.swoole-5';
}
else
{
    class Process extends \Swoole\Process
    {
        use TProcess;
    }
}
