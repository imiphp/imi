<?php

declare(strict_types=1);

namespace Imi\Server\DataParser;

class JsonObjectParser implements IParser
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
     * 编码为存储格式.
     *
     * @param mixed $data
     */
    public function encode($data): string
    {
        return json_encode($data, $this->options, $this->depth);
    }

    /**
     * 解码为php变量.
     *
     * @return mixed
     */
    public function decode(string $data)
    {
        return json_decode($data, false, $this->depth, $this->options);
    }
}
