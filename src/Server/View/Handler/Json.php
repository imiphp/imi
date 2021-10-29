<?php

declare(strict_types=1);

namespace Imi\Server\View\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\View\Annotation\BaseViewOption;
use Imi\Server\View\Annotation\JsonView;
use Imi\Server\View\Annotation\View;
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
     */
    protected int $options = \JSON_THROW_ON_ERROR;

    /**
     * 设置最大深度。 必须大于0。
     */
    protected int $depth = 512;

    /**
     * {@inheritDoc}
     */
    public function handle(View $viewAnnotation, ?BaseViewOption $viewOption, $data, IHttpResponse $response): IHttpResponse
    {
        $response->setHeader(ResponseHeader::CONTENT_TYPE, MediaType::APPLICATION_JSON)
                 ->getBody()
                 ->write(json_encode($data, $this->options, $this->depth));

        return $response;
    }
}
