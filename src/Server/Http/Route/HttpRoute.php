<?php
namespace Imi\Server\Http\Route;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Route\BaseRoute;
use Imi\Server\Route\BaseRouteParam;
use Imi\Server\Route\Annotation\Route as RouteAnnotation;

/**
 * @Bean("HttpRoute")
 */
class HttpRoute extends BaseRoute
{
	/**
	 * 路由解析处理
	 * @param BaseRouteParam $param
	 * @return array
	 */
	public function parse(BaseRouteParam $param)
	{
		// 为了IDE能提示，PHP>=7.2才可以覆盖方法时修改参数类型
		if(!$param instanceof RouteParam)
		{
			return;
		}
		foreach($this->rules as $url => $items)
		{
			if($this->checkUrl($param, $url, $params))
			{
				foreach($items as $item)
				{
					if(
						$this->checkMethod($param, $item['annotation']->method) &&
						$this->checkDomain($param, $item['annotation']->domain) &&
						$this->checkParams($param, $item['annotation']->params) &&
						$this->checkHeader($param, $item['annotation']->header) &&
						$this->checkRequestMime($param, $item['annotation']->requestMime)
					)
					{
						return [
							'params'	=>	$params,
							'callable'	=>	$item['callable']
						];
					}
				}
			}
		}
		return null;
	}

	/**
	 * 检查验证url是否匹配
	 * @param RouteParam $param
	 * @param string $url
	 * @param array $params url路由中的自定义参数
	 * @return boolean
	 */
	private function checkUrl(RouteParam $param, string $url, &$params)
	{
		$rule = $this->parseUrlRule($url, $fields);
		$params = [];
		if(preg_match_all($rule, $param->request->getServerParam('path_info'), $matches) > 0)
		{
			foreach($fields as $i => $fieldName)
			{
				$params[$fieldName] = $matches[$i + 1][0];
			}
			return true;
		}
		return false;
	}

	/**
	 * 处理url路由为正则
	 * @param string $url
	 * @param array $fields 路由中包含的自定义参数
	 * @return string
	 */
	private function parseUrlRule($url, &$fields)
	{
		$fields = [];
		$url = str_replace(['/', '\{', '\}'], ['\/', '{', '}'], preg_quote($url));
		return '/^' . preg_replace_callback(
			'/{([^}]+)}/i',
			function($matches)use(&$fields){
				$fields[] = $matches[1];
				return '(.+)';
			},
			$url
		) . '\/?$/';
	}

	/**
	 * 检查验证请求方法是否匹配
	 * @param RouteParam $param
	 * @param mixed $method
	 * @return boolean
	 */
	private function checkMethod(RouteParam $param, $method)
	{
		
		return true;
	}
	
	/**
	 * 检查验证域名是否匹配
	 * @param RouteParam $param
	 * @param mixed $domain
	 * @return boolean
	 */
	private function checkDomain(RouteParam $param, $domain)
	{

		return true;
	}
	
	/**
	 * 检查验证参数是否匹配
	 * @param RouteParam $param
	 * @param mixed $params
	 * @return boolean
	 */
	private function checkParams(RouteParam $param, $params)
	{

		return true;
	}
	
	/**
	 * 检查验证请求头是否匹配
	 * @param RouteParam $param
	 * @param mixed $header
	 * @return boolean
	 */
	private function checkHeader(RouteParam $param, $header)
	{

		return true;
	}
	
	/**
	 * 检查验证请求媒体类型是否匹配
	 * @param RouteParam $param
	 * @param mixed $requestMime
	 * @return boolean
	 */
	private function checkRequestMime(RouteParam $param, $requestMime)
	{

		return true;
	}
}