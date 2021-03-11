<?php

namespace Imi\Server\View\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Response;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\ResponseHeader;

/**
 * Json视图处理器.
 *
 * @Bean("JsonView")
 */
class Json implements IHandler
{
    /**
     * 由以下常量组成的二进制掩码：
     * JSON_HEX_QUOT
     * JSON_HEX_TAG
     * JSON_HEX_AMP
     * JSON_HEX_APOS
     * JSON_NUMERIC_CHECK
     * JSON_PRETTY_PRINT
     * JSON_UNESCAPED_SLASHES
     * JSON_FORCE_OBJECT
     * JSON_PRESERVE_ZERO_FRACTION
     * JSON_UNESCAPED_UNICODE
     * JSON_PARTIAL_OUTPUT_ON_ERROR。
     *
     * @var int
     */
    protected $options = 0;

    /**
     * 设置最大深度。 必须大于0。
     *
     * @var int
     */
    protected $depth = 512;

    /**
     * @param array                             $data
     * @param array                             $options
     * @param \Imi\Server\Http\Message\Response $response
     *
     * @return \Imi\Server\Http\Message\Response
     */
    public function handle($data, array $options, Response $response): Response
    {
        return $response->withHeader(ResponseHeader::CONTENT_TYPE, MediaType::APPLICATION_JSON)
                        ->write(json_encode($data, $this->options, $this->depth));
    }
}
