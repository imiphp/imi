<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message\Contract;

use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Http\Contract\IResponse;

/**
 * Http 响应接口.
 */
interface IHttpResponse extends IResponse
{
    /**
     * 设置服务器端重定向
     * 默认状态码为302.
     */
    public function redirect(string $url, int $status = StatusCode::FOUND): self;

    /**
     * 发送所有响应数据.
     */
    public function send(): self;

    /**
     * 发送文件，一般用于文件下载.
     *
     * @param string $filename 要发送的文件名称，文件不存在或没有访问权限sendfile会失败
     * @param int    $offset   上传文件的偏移量，可以指定从文件的中间部分开始传输数据。此特性可用于支持断点续传。
     * @param int    $length   发送数据的尺寸，默认为整个文件的尺寸
     */
    public function sendFile(string $filename, int $offset = 0, int $length = 0): self;

    /**
     * 是否已结束请求
     */
    public function isEnded(): bool;
}
