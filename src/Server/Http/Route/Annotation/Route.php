<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 路由注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\Http\Parser\ControllerParser")
 *
 * @property string|null     $url                  请求地址规则
 * @property bool|null       $ignoreCase           忽略请求地址大小写；null-取HttpRoute中默认值；true-忽略大小写；false-严格判断
 * @property bool|null       $autoEndSlash         智能尾部斜杠，无论是否存在都匹配
 * @property string|string[] $method               请求方法；必须是这些请求方法之一才可以被调用；可以是单个方法的字符串，也可以是字符串数组
 * @property string|string[] $domain               允许的域名；可以是单个域名的字符串，也可以是字符串数组，支持正则
 * @property string|array    $paramsGet            GET参数条件；可以是单个字符串，也可以是数组；取值：id=100 必须包含id，并且值为100、id!=100 或 id<>100 必须包含id，并且值不为100、id 必须包含id参数、!id 必须不包含id参数、"id" => "\d+" 支持正则
 * @property string|array    $paramsPost           POST参数条件；可以是单个字符串，也可以是数组；取值：id=100 必须包含id，并且值为100、id!=100 或 id<>100 必须包含id，并且值不为100、id 必须包含id参数、!id 必须不包含id参数、"id" => "\d+" 支持正则
 * @property string|array    $paramsBody           JSON、XML参数条件；可以是单个字符串，也可以是数组；取值：id=100 必须包含id，并且值为100、id!=100 或 id<>100 必须包含id，并且值不为100、id 必须包含id参数、!id 必须不包含id参数、"id" => "\d+" 支持正则
 * @property bool            $paramsBodyMultiLevel JSON、XML参数条件支持以 . 作为分隔符，支持多级参数获取
 * @property string|array    $header               请求头条件；可以是单个字符串，也可以是数组；取值：id=100 必须包含id，并且值为100、id!=100 或 id<>100 必须包含id，并且值不为100、id 必须包含id参数、!id 必须不包含id参数、"id" => "\d+" 支持正则
 * @property string|string[] $requestMime          请求的mime类型判断；判断请求头中的Content-Type中是否包含这些mime类型之一；支持字符串和字符串数组
 * @property string|string[] $responseMime         返回的mime类型；只有当请求头Accept中包含，才可以返回；支持字符串和字符串数组
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Route extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'url';

    public function __toString()
    {
        return http_build_query($this->toArray());
    }

    /**
     * @param string|string[] $method
     * @param string|string[] $domain
     * @param string|array    $paramsGet
     * @param string|array    $paramsPost
     * @param string|array    $paramsBody
     * @param string|array    $header
     * @param string|string[] $requestMime
     * @param string|string[] $responseMime
     */
    public function __construct(?array $__data = null, ?string $url = null, ?bool $ignoreCase = null, ?bool $autoEndSlash = null, $method = null, $domain = null, $paramsGet = null, $paramsPost = null, $paramsBody = null, bool $paramsBodyMultiLevel = true, $header = null, $requestMime = null, $responseMime = null)
    {
        parent::__construct(...\func_get_args());
    }
}
