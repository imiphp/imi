<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\Random;

/**
 * @testdox Imi\Util\Random
 */
class RandomTest extends BaseTest
{
    public function testInt(): void
    {
        $val = Random::int();
        $this->assertTrue($val >= \PHP_INT_MIN && $val <= \PHP_INT_MAX);

        $val = Random::int(1, 3);
        $this->assertTrue($val >= 1 && $val <= 3);
    }

    public function testFloat(): void
    {
        $val = Random::float();
        $this->assertTrue($val >= \PHP_INT_MIN && $val <= \PHP_INT_MAX);

        $val = Random::float(1, 3);
        $this->assertTrue($val >= 1 && $val <= 3);
    }

    public function testNumber(): void
    {
        $val = Random::number();
        $this->assertTrue($val >= \PHP_INT_MIN && $val <= \PHP_INT_MAX);
        [, $afterDot] = explode('.', $val);
        $this->assertLessThanOrEqual(2, \strlen($afterDot));

        $val = Random::number(1, 3, 1);
        $this->assertTrue($val >= 1 && $val <= 3);
        [, $afterDot] = explode('.', $val . '.0');
        $this->assertLessThanOrEqual(1, \strlen($afterDot));
    }

    public function testText(): void
    {
        $val = Random::text('哈', 5, 5);
        $this->assertEquals('哈哈哈哈哈', $val);

        $val = Random::text('哈', 5);
        $this->assertEquals('哈哈哈哈哈', $val);

        $val = Random::text('啊哦额', 10, 20);
        $this->assertEquals(1, preg_match('/^[啊哦额]{10,20}$/u', $val));
    }

    public function testBytes(): void
    {
        $val = Random::text('a', 5, 5);
        $this->assertEquals('aaaaa', $val);

        $val = Random::text('a', 5);
        $this->assertEquals('aaaaa', $val);

        $val = Random::text('abc', 10, 20);
        $this->assertEquals(1, preg_match('/^[abc]{10,20}$/', $val));
    }

    public function testLetter(): void
    {
        $val = Random::letter(10, 20);
        $this->assertEquals(1, preg_match('/^[a-zA-Z]{10,20}$/', $val));

        $this->assertEquals(5, \strlen(Random::letter(5)));
    }

    public function testDigital(): void
    {
        $val = Random::digital(10, 20);
        $this->assertEquals(1, preg_match('/^\d{10,20}$/', $val));

        $this->assertEquals(5, \strlen(Random::digital(5)));
    }

    public function testLetterAndNumber(): void
    {
        $val = Random::letterAndNumber(10, 20);
        $this->assertEquals(1, preg_match('/^[a-zA-Z\d]{10,20}$/', $val));

        $this->assertEquals(5, \strlen(Random::letterAndNumber(5)));
    }
}
