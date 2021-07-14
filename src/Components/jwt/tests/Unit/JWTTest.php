<?php

declare(strict_types=1);

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
            if (3 === JWT::getJwtPackageVersion())
            {
                $builder->expiresAt(strtotime('1926-08-17'));
            }
            else
            {
                $builder->expiresAt(new \DateTimeImmutable('1926-08-17'));
            }
        });
        $tokenStr = $token->toString();
        $token2 = JWT::parseToken($tokenStr);
        $config = JWT::getConfig();
        if (3 === JWT::getJwtPackageVersion())
        {
            $this->assertEquals(json_encode($data), json_encode($token2->getClaim($config->getDataName())));
        }
        else
        {
            $this->assertEquals(json_encode($data), json_encode($token2->claims()->get($config->getDataName())));
        }
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
