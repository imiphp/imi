<?php

declare(strict_types=1);

namespace Imi\Rpc\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Rpc\Route\Annotation\Contract\IRpcAction;

/**
 * RPC 动作注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
#[Parser(className: \Imi\Rpc\Route\Annotation\Parser\RpcControllerParser::class)]
class RpcAction extends Base implements IRpcAction
{
}
