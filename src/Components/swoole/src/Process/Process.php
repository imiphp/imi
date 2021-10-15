<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

class Process extends \Swoole\Process
{
    protected string $name;

    protected ?string $alias;

    /**
     * 发送消息.
     *
     * @return mixed
     */
    public function sendMessage(string $action, array $data = [])
    {
        $data['a'] = $action;
        $message = json_encode($data);

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

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): void
    {
        $this->alias = $alias;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPid(): int
    {
        $info = ProcessManager::readProcessInfo(ProcessManager::buildUniqueId($this->name, $this->alias));

        if (null === $info)
        {
            return 0;
        }

        return $info['pid'];
    }
}
