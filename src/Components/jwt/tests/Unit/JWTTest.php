<?php

declare(strict_types=1);

namespace Imi\JWT\Test\Unit;

use Imi\App;
use Imi\JWT\Facade\JWT;
use Lcobucci\JWT\Builder;
use PHPUnit\Framework\TestCase;

class JWTTest extends TestCase
{
    public function testJWT(): void
    {
        $data = [
            'memberId'  => 19260817,
        ];
        $token = JWT::getToken($data, null, static function (Builder $builder): void {
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
        $this->expectException(\Imi\JWT\Exception\InvalidTokenException::class);
        $token2 = JWT::parseToken($tokenStr, null, true); // 验证
    }

    public function testJWTValidation(): void
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

    public function testJWTValidateFail(): void
    {
        $this->expectException(\Imi\JWT\Exception\InvalidTokenException::class);
        /** @var \Imi\JWT\Test\Test\A $a */
        $a = App::getBean('A');
        [$token, $data] = $a->testFail();
    }
}
