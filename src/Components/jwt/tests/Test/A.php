<?php

declare(strict_types=1);

namespace Imi\JWT\Test\Test;

use Imi\Bean\Annotation\Bean;
use Imi\JWT\Annotation\JWTValidation;

#[Bean(name: 'A')]
class A
{
    /**
     * @param \Lcobucci\JWT\Token $token
     * @param \stdClass           $data
     *
     * @return array
     */
    #[JWTValidation(tokenParam: 'token', dataParam: 'data')]
    public function test($token = null, $data = null)
    {
        return [$token, $data];
    }

    /**
     * @param \Lcobucci\JWT\Token $token
     * @param \stdClass           $data
     *
     * @return array
     */
    #[JWTValidation(name: 'b', tokenParam: 'token', dataParam: 'data')]
    public function testFail($token = null, $data = null)
    {
        return [$token, $data];
    }
}
