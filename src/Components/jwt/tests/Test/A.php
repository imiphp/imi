<?php

declare(strict_types=1);

namespace Imi\JWT\Test\Test;

use Imi\Bean\Annotation\Bean;
use Imi\JWT\Annotation\JWTValidation;

#[Bean(name: 'A')]
class A
{
    #[JWTValidation(tokenParam: 'token', dataParam: 'data')]
    public function test(?\Lcobucci\JWT\Token $token = null, mixed $data = null): array
    {
        return [$token, $data];
    }

    #[JWTValidation(name: 'b', tokenParam: 'token', dataParam: 'data')]
    public function testFail(?\Lcobucci\JWT\Token $token = null, mixed $data = null): array
    {
        return [$token, $data];
    }
}
