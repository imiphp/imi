<?php

declare(strict_types=1);

namespace Imi\Pool;

use Imi\Util\Uri;

trait TUriResourceConfig
{
    protected function initUriResourceConfig()
    {
        foreach ($this->resourceConfig as &$config)
        {
            if (\is_array($config))
            {
                continue;
            }
            $list = explode(';', $config);
            $config = [];
            foreach ($list as $uri)
            {
                $uriObj = new Uri($uri);
                parse_str($uriObj->getQuery(), $config);
                if (!isset($config['host']))
                {
                    $config['host'] = $uriObj->getHost();
                }
                if (!isset($config['port']))
                {
                    $config['port'] = $uriObj->getPort();
                }
            }
        }
    }
}
