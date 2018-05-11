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
				var_dump('params:', $params);
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

	private function checkUrl(RouteParam $param, string $url, &$params)
	{
		$rule = $this->parseUrlRule($url, $fields);
		$params = [];
		if(preg_match_all($rule, $param->request->server['path_info'], $matches) > 0)
		{
			foreach($fields as $i => $fieldName)
			{
				$params[$fieldName] = $matches[$i + 1][0];
			}
			return true;
		}
		return false;
	}

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

	private function checkMethod(RouteParam $param, $method)
	{
		return true;
	}
	
	private function checkDomain(RouteParam $param, $domain)
	{

		return true;
	}
	
	private function checkParams(RouteParam $param, $params)
	{

		return true;
	}
	
	private function checkHeader(RouteParam $param, $header)
	{

		return true;
	}
	
	private function checkRequestMime(RouteParam $param, $requestMime)
	{

		return true;
	}
}