<?php

declare(strict_types=1);

namespace Imi\JWT\Facade;

use Imi\Facade\BaseFacade;

/**
 * @method static void                          __init()
 * @method static \Imi\JWT\Model\JWTConfig[]    getList()
 * @method static string|null                   getDefault()
 * @method static \Imi\JWT\Model\JWTConfig|null getConfig(?string $name = NULL)
 * @method static \Lcobucci\JWT\Builder         getBuilderInstance(?string $name = NULL)
 * @method static \Lcobucci\JWT\Parser          getParserInstance(?string $name = NULL)
 * @method static \Lcobucci\JWT\Token           getToken($data, ?string $name = NULL, ?callable $beforeGetToken = NULL)
 * @method static \Lcobucci\JWT\Token           parseToken(string $jwt, ?string $name = NULL, bool $validate = false)
 * @method static void                          validate(?string $name, \Lcobucci\JWT\Token $token)
 * @method static int                           getJwtPackageVersion()
 */
#[
    \Imi\Facade\Annotation\Facade(class: 'JWT')
]
class JWT extends BaseFacade
{
}
