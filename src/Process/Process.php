<?php
namespace Imi\Process;

class Process extends \Swoole\Process
{
    /**
     * 发送消息
     *
     * @param string $action
     * @param array $data
     * @return mixed
     */
    public function sendMessage(string $action, array $data = [])
    {
        $data['a'] = $action;
        $message = json_encode($data);
        return $this->write($message);
    }

}
