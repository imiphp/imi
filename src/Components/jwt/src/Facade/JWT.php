<?php

namespace Imi\JWT\Facade;

use Imi\Facade\Annotation\Facade;
use Imi\Facade\BaseFacade;

/**
 * @Facade(class="JWT")
 *
 * @method static mixed __init()
 * @method static \Imi\JWT\Model\JWTConfig[] getList()
 * @method static string|null getDefault()
 * @method static \Imi\JWT\Model\JWTConfig|null getConfig(string|null $name = NULL)
 * @method static \Lcobucci\JWT\Builder getBuilderInstance(string|null $name = NULL)
 * @method static \Lcobucci\JWT\Token|\Lcobucci\JWT\UnencryptedToken getToken(mixed $data, string|null $name = NULL, callable|null $beforeGetToken = NULL)
 * @method static \Lcobucci\JWT\Parser getParserInstance(string|null $name = NULL)
 * @method static \Lcobucci\JWT\Token|\Lcobucci\JWT\UnencryptedToken parseToken(string $jwt, string|null $name = NULL)
 * @method static int getJwtPackageVersion()
 */
abstract class JWT extends BaseFacade
{
}
