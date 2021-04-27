<?php

declare(strict_types=1);

namespace Imi\Server\View\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\View\Annotation\View;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\ResponseHeader;

/**
 * Xml视图处理器.
 *
 * @Bean("XmlView")
 */
class Xml implements IHandler
{
    /**
     * @param mixed $data
     */
    public function handle(View $viewAnnotation, $data, IHttpResponse $response): IHttpResponse
    {
        $response->setHeader(ResponseHeader::CONTENT_TYPE, MediaType::APPLICATION_XML);
        if ($data instanceof \DOMDocument)
        {
            $response->getBody()->write($data->saveXML());
        }
        elseif ($data instanceof \SimpleXMLElement)
        {
            $response->getBody()->write($data->asXML());
        }
        else
        {
            throw new \RuntimeException('Unsupport xml object type: ' . \gettype($data));
        }

        return $response;
    }
}
