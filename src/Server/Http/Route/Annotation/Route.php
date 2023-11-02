<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 路由注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Server\Http\Parser\ControllerParser::class)]
class Route extends Base implements \Stringable
{
    public function __toString(): string
    {
        return http_build_query($this->toArray());
    }

    public function __construct(
        /**
         * 请求地址规则.
         */
        public ?string $url = null,
        /**
         * 忽略请求地址大小写；null-取HttpRoute中默认值；true-忽略大小写；false-严格判断.
         */
        public ?bool $ignoreCase = null,
        /**
         * 智能尾部斜杠，无论是否存在都匹配.
         */
        public ?bool $autoEndSlash = null,
        /**
         * 请求方法；必须是这些请求方法之一才可以被调用；可以是单个方法的字符串，也可以是字符串数组.
         *
         * @var string|string[]|null
         */
        public $method = null,
        /**
         * 允许的域名；可以是单个域名的字符串，也可以是字符串数组，支持正则.
         *
         * @var string|string[]|null
         */
        public $domain = null,
        /**
         * GET参数条件；可以是单个字符串，也可以是数组；取值：id=100 必须包含id，并且值为100、id!=100 或 id<>100 必须包含id，并且值不为100、id 必须包含id参数、!id 必须不包含id参数、"id" => "\d+" 支持正则.
         *
         * @var string|array|null
         */
        public $paramsGet = null,
        /**
         * POST参数条件；可以是单个字符串，也可以是数组；取值：id=100 必须包含id，并且值为100、id!=100 或 id<>100 必须包含id，并且值不为100、id 必须包含id参数、!id 必须不包含id参数、"id" => "\d+" 支持正则.
         *
         * @var string|array|null
         */
        public $paramsPost = null,
        /**
         * JSON、XML参数条件；可以是单个字符串，也可以是数组；取值：id=100 必须包含id，并且值为100、id!=100 或 id<>100 必须包含id，并且值不为100、id 必须包含id参数、!id 必须不包含id参数、"id" => "\d+" 支持正则.
         *
         * @var string|array|null
         */
        public $paramsBody = null,
        /**
         * JSON、XML参数条件支持以 . 作为分隔符，支持多级参数获取.
         */
        public bool $paramsBodyMultiLevel = true,
        /**
         * 请求头条件；可以是单个字符串，也可以是数组；取值：id=100 必须包含id，并且值为100、id!=100 或 id<>100 必须包含id，并且值不为100、id 必须包含id参数、!id 必须不包含id参数、"id" => "\d+" 支持正则.
         *
         * @var string|array|null
         */
        public $header = null,
        /**
         * 请求的mime类型判断；判断请求头中的Content-Type中是否包含这些mime类型之一；支持字符串和字符串数组.
         *
         * @var string|string[]|null
         */
        public $requestMime = null,
        /**
         * 返回的mime类型；只有当请求头Accept中包含，才可以返回；支持字符串和字符串数组.
         *
         * @var string|string[]|null
         */
        public $responseMime = null
    ) {
    }
}
