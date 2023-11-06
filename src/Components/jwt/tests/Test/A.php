<?php

declare(strict_types=1);

namespace Imi\JWT\Test\Test;

use Imi\Bean\Annotation\Bean;
use Imi\JWT\Annotation\JWTValidation;

#[Bean(name: 'A')]
class A
{
    /**
     * @return array
     */
    #[JWTValidation(tokenParam: 'token', dataParam: 'data')]
    public function test(?\Lcobucci\JWT\Token $token = null, ?\stdClass $data = null)
    {
        return [$token, $data];
    }

    /**
     * @return array
     */
    #[JWTValidation(name: 'b', tokenParam: 'token', dataParam: 'data')]
    public function testFail(?\Lcobucci\JWT\Token $token = null, ?\stdClass $data = null)
    {
        return [$token, $data];
    }
}
