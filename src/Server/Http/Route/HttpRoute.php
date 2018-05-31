<?php
namespace Imi\Server\Http\Route;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Route\BaseRoute;
use Imi\Server\Http\Message\Request;
use Imi\Server\Route\Annotation\Route as RouteAnnotation;
use Imi\Server\Route\RouteCallable;

/**
 * @Bean("HttpRoute")
 */
class HttpRoute extends BaseRoute
{
	/**
	 * 路由解析处理
	 * @param Request $request
	 * @return array
	 */
	public function parse(Request $request)
	{
		foreach($this->rules as $url => $items)
		{
			if($this->checkUrl($request, $url, $params))
			{
				foreach($items as $item)
				{
					if(
						$this->checkMethod($request, $item['annotation']->method) &&
						$this->checkDomain($request, $item['annotation']->domain, $domainParams) &&
						$this->checkParamsGet($request, $item['annotation']->paramsGet) &&
						$this->checkParamsPost($request, $item['annotation']->paramsPost) &&
						$this->checkHeader($request, $item['annotation']->header) &&
						$this->checkRequestMime($request, $item['annotation']->requestMime)
					)
					{
						$params = array_merge($params, $domainParams);
						return [
							'params'	=>	$params,
							'callable'	=>	$this->parseCallable($params, $item['callable']),
						];
					}
				}
			}
		}
		return null;
	}

	/**
	 * 检查验证url是否匹配
	 * @param Request $request
	 * @param string $url
	 * @param array $params url路由中的自定义参数
	 * @return boolean
	 */
	private function checkUrl(Request $request, string $url, &$params)
	{
		$rule = $this->parseRule($url, $fields);
		$params = [];
		if(preg_match_all($rule, $request->getServerParam('path_info'), $matches) > 0)
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
	 * 处理规则为正则
	 * @param string $rule
	 * @param array $fields 规则中包含的自定义参数
	 * @return string
	 */
	private function parseRule($rule, &$fields)
	{
		$fields = [];
		$rule = str_replace(['/', '\{', '\}'], ['\/', '{', '}'], preg_quote($rule));
		return '/^' . preg_replace_callback(
			'/{([^}]+)}/i',
			function($matches)use(&$fields){
				$fields[] = $matches[1];
				return '(.+)';
			},
			$rule
		) . '\/?$/';
	}

	/**
	 * 检查验证请求方法是否匹配
	 * @param Request $request
	 * @param mixed $method
	 * @return boolean
	 */
	private function checkMethod(Request $request, $method)
	{
		if(Text::isEmpty($method))
		{
			return true;
		}
		else if(is_array($method))
		{
			return in_array($request->getMethod(), $method);
		}
		else
		{
			return $method === $request->getMethod();
		}
	}
	
	/**
	 * 检查验证域名是否匹配
	 * @param Request $request
	 * @param mixed $domain
	 * @return boolean
	 */
	private function checkDomain(Request $request, $domain, &$params)
	{
		$params = [];
		if(Text::isEmpty($domain))
		{
			return true;
		}
		if(!is_array($domain))
		{
			$domain = [$domain];
		}
		foreach($domain as $rule)
		{
			$rule = $this->parseRule($rule, $fields);
			if(preg_match_all($rule, $request->getUri()->getDomain(), $matches) > 0)
			{
				$params = [];
				foreach($fields as $i => $fieldName)
				{
					$params[$fieldName] = $matches[$i + 1][0];
				}
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 检查验证GET参数是否匹配
	 * @param Request $request
	 * @param mixed $params
	 * @return boolean
	 */
	private function checkParamsGet(Request $request, $params)
	{
		if(empty($params))
		{
			return true;
		}
		return Imi::checkCompareRules($params, function($name) use($request){
			return $request->get($name);
		});
		return true;
	}
	
	/**
	 * 检查验证POST参数是否匹配
	 * @param Request $request
	 * @param mixed $params
	 * @return boolean
	 */
	private function checkParamsPost(Request $request, $params)
	{
		if(empty($params))
		{
			return true;
		}
		return Imi::checkCompareRules($params, function($name) use($request){
			return $request->post($name);
		});
	}

	/**
	 * 检查验证请求头是否匹配
	 * @param Request $request
	 * @param mixed $header
	 * @return boolean
	 */
	private function checkHeader(Request $request, $header)
	{
		if(empty($header))
		{
			return true;
		}
		return Imi::checkCompareRules($header, function($name) use($request){
			return $request->getHeaderLine($name);
		});
	}
	
	/**
	 * 检查验证请求媒体类型是否匹配
	 * @param Request $request
	 * @param mixed $requestMime
	 * @return boolean
	 */
	private function checkRequestMime(Request $request, $requestMime)
	{
		if(empty($requestMime))
		{
			return true;
		}
		return Imi::checkCompareValues($requestMime, $request->getHeaderLine('Content-Type'));
	}

	/**
	 * 处理回调
	 * @param array $params
	 * @param mixed $callable
	 * @return callable
	 */
	private function parseCallable($params, $callable)
	{
		if($callable instanceof RouteCallable)
		{
			return $callable->getCallable($params);
		}
		else
		{
			return $callable;
		}
	}
}