<?php

namespace Imi\Util;

/**
 * SESSION 操作类
 * author:lovefc
 * time:2018/06/21 19:07
 */

class Session
{
    private $sessionId;
    private $cookieKey;
    private $storeDir;
    private $file;
    private $isStart;

    /**
     * 初始化设置
     */
    public function __construct()
    {
        $this->cookieKey = 'PHPSESSID';
        $this->storeDir = 'tmp/';
        $this->isStart = false;
    }

    /**
     * 启动写入
     * @param object $request
     * @param object $response
     * @return null
     */
    public function start($request, $response)
    {
        if (empty($request) || empty($response)) {
            return false;
        }
        $this->isStart = true;
        $sessionId = $request->cookie[$this->cookieKey];
        if (empty($sessionId)) {
            $sessionId = uniqid();
            $response->cookie($this->cookieKey, $sessionId);
        }
        $this->sessionId = $sessionId;
        $storeFile = $this->storeDir . $sessionId;
        if (!is_file($storeFile)) {
            touch($storeFile);
        }
        $session = $this->get($storeFile);
        $_SESSION = $session;
    }

    /**
     * 保存session到文件（别名）
     * @return null
     */
    public function end()
    {
        $this->save();
    }

    /**
     * 保存session到文件
     * @return null
     */
    private function save()
    {
        if ($this->isStart) {
            $data = json_encode($_SESSION);
            ftruncate($this->file, 0);

            if ($data) {
                rewind($this->file);
                fwrite($this->file, $data);
            }
            flock($this->file, LOCK_UN);
            fclose($this->file);
        }
    }

    /**
     * 获取解析文件内容
     * @param string $fileName
     * @return string
     */
    private function get($fileName)
    {
        $this->file = fopen($fileName, 'c+b');
        if (flock($this->file, LOCK_EX | LOCK_NB)) {
            $data = [];
            clearstatcache();
            if (filesize($fileName) > 0) {
                $data = fread($this->file, filesize($fileName));
                $data = json_decode($data, true);
            }
            return $data;
        }
    }
}