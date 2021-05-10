<?php

namespace Imi\JWT\Test\Unit;

use Imi\App;
use Imi\JWT\Facade\JWT;
use Lcobucci\JWT\Builder;
use PHPUnit\Framework\TestCase;

class JWTTest extends TestCase
{
    /**
     * @return void
     */
    public function testJWT()
    {
        $data = [
            'memberId'  => 19260817,
        ];
        $token = JWT::getToken($data, null, function (Builder $builder) {
            $builder->expiresAt(strtotime('1926-08-17'));
        });
        $tokenStr = (string) $token;
        $token2 = JWT::parseToken($tokenStr);
        $config = JWT::getConfig();
        $this->assertEquals(json_encode($data), json_encode($token2->getClaim($config->getDataName())));
    }

    /**
     * @return void
     */
    public function testJWTValidation()
    {
        $excepted = [
            'memberId'  => 19260817,
        ];
        /** @var \Imi\JWT\Test\Test\A $a */
        $a = App::getBean('A');
        [$token, $data] = $a->test();
        $this->assertInstanceOf(\Lcobucci\JWT\Token::class, $token);
        $this->assertEquals(json_encode($excepted), json_encode($data));
    }

    /**
     * @return void
     */
    public function testJWTValidateFail()
    {
        $this->expectException(\Imi\JWT\Exception\InvalidTokenException::class);
        /** @var \Imi\JWT\Test\Test\A $a */
        $a = App::getBean('A');
        [$token, $data] = $a->testFail();
    }
}
