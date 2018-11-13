<?php
namespace Imi\Server\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 路由注解
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\Route\Parser\ControllerParser")
 */
class Route extends Base
{
    /**
     * 只传一个参数时的参数名
     * @var string
     */
    protected $defaultFieldName = 'url';

    /**
     * 请求地址规则
     * @var string
     */
    public $url;

    /**
     * 忽略请求地址大小写
     * null-取HttpRoute中默认值
     * true-忽略大小写
     * false-严格判断
     *
     * @var boolean|null
     */
    public $ignoreCase;

    /**
     * 请求方法
     * 必须是这些请求方法之一才可以被调用
     * 可以是单个方法的字符串，也可以是字符串数组
     * @var string|string[]
     */
    public $method;

    /**
     * 允许的域名
     * 可以是单个域名的字符串，也可以是字符串数组，支持正则
     * @var string|string[]
     */
    public $domain;

    /**
     * GET参数条件
     * 可以是单个字符串，也可以是数组
     * 取值：
     * id=100 必须包含id，并且值为100
     * id!=100 或 id<>100 必须包含id，并且值不为100
     * id 必须包含id参数
     * !id 必须不包含id参数
     * "id" => "\d+" 支持正则
     * @var string|array
     */
    public $paramsGet;

    /**
     * POST参数条件
     * 可以是单个字符串，也可以是数组
     * 取值：
     * id=100 必须包含id，并且值为100
     * id!=100 或 id<>100 必须包含id，并且值不为100
     * id 必须包含id参数
     * !id 必须不包含id参数
     * "id" => "\d+" 支持正则
     * @var string|array
     */
    public $paramsPost;

    /**
     * 请求头条件
     * 可以是单个字符串，也可以是数组
     * 取值：
     * id=100 必须包含id，并且值为100
     * id!=100 或 id<>100 必须包含id，并且值不为100
     * id 必须包含id参数
     * !id 必须不包含id参数
     * "id" => "\d+" 支持正则
     * @var string|array
     */
    public $header;

    /**
     * 请求的mime类型判断
     * 判断请求头中的Content-Type中是否包含这些mime类型之一
     * 支持字符串和字符串数组
     * @var string|string[]
     */
    public $requestMime;

    /**
     * 返回的mime类型
     * 只有当请求头Accept中包含，才可以返回
     * 支持字符串和字符串数组
     * @var string|string[]
     */
    public $responseMime;
}