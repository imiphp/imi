<?php

declare(strict_types=1);

namespace Imi\Pool;

use Imi\Util\Uri;

trait TUriResourceConfig
{
    protected function initUriResourceConfig(): void
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
                $config['host'] ??= $uriObj->getHost();
                $config['port'] ??= $uriObj->getPort();
            }
        }
    }
}
