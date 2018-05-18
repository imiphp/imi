<?php
namespace Imi\Server\View\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Util\Http\Consts\MediaType;
use Imi\Server\View\Annotation\View;
use Imi\Server\Http\Message\Response;
use Imi\Util\Http\Consts\RequestHeader;

/**
 * Xml视图处理器
 * @Bean("XmlView")
 */
class Xml implements IHandler
{
	public function handle(View $viewAnnotation, Response $response): Response
	{
		$response = $response->withAddedHeader(RequestHeader::CONTENT_TYPE, MediaType::APPLICATION_XML);
		if($viewAnnotation->data instanceof \DOMDocument)
		{
			return $response->write($viewAnnotation->data->saveXML());
		}
		else if($viewAnnotation->data instanceof \SimpleXMLElement)
		{
			return $response->write($viewAnnotation->data->asXML());
		}
		else
		{
			throw new \RuntimeException('Unsupport xml object type: ' . gettype($viewAnnotation->data));
		}
	}
}