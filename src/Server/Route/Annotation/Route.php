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
	 * 请求地址规则
	 * @var string
	 */
	public $url;

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
	 * 参数条件
	 * 必须包含这些参数才可以被调用
	 * 可以是单个字符串：id=100
	 * 也可以是键值数组
	 * @var string|array
	 */
	public $params;

	/**
	 * 请求头条件
	 * 必须包含这些请求头才可以被调用
	 * 可以是单个字符串：Content-Type:text/html;charset=utf-8
	 * 也可以是键值数组
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