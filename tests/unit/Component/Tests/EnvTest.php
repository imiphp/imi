<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Env;
use function Imi\env;
use Imi\Test\BaseTest;
use InvalidArgumentException;

class EnvTest extends BaseTest
{
    public function testFunctionEnv(): void
    {
        $this->assertEquals(123, env('A'));
        $this->assertEquals('imi', env('E'));
        $this->assertEquals('123', env('A', 'default'));
        $this->assertEquals(123, env('A', 0));
        $this->assertEquals(123.0, env('A', 3.14));
        $this->assertEquals('123', env('A', false));
        $this->assertFalse(env('B', false));
        $this->assertTrue(env('BOOL_TRUE', false));
        $this->assertFalse(env('BOOL_FALSE', false));
        $this->assertEquals(['1', '2', '3'], env('C', []));
        $this->assertEquals(['4', '5', '6'], env('D', []));
        $this->assertNull(env('NULL_VALUE'));
        $this->assertNull(env('NULL_VALUE', 666));
    }

    public function testClassEnv(): void
    {
        $this->assertEquals('123', Env::str('A'));
        $this->assertEquals(123, Env::int('A'));
        $this->assertNull(Env::int('NULL_VALUE'));
        $this->assertEquals(123.0, Env::float('A'));
        $this->assertNull(Env::float('NULL_VALUE'));
        $this->assertFalse(Env::bool('B'));
        $this->assertTrue(Env::bool('BOOL_TRUE'));
        $this->assertFalse(Env::bool('BOOL_FALSE'));
        $this->assertNull(Env::bool('NULL_VALUE'));
        $this->assertEquals(['4', '5', '6'], Env::json('D'));
        $this->assertNull(Env::json('NULL_VALUE'));
        $this->assertEquals(['1', '2', '3'], Env::list('C'));
        $this->assertNull(Env::list('NULL_VALUE'));
    }

    public function testInvalidArgument1(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Env::bool('A');
    }

    public function testInvalidArgument2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Env::int('E');
    }

    public function testInvalidArgument3(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Env::float('E');
    }

    public function testInvalidArgument4(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Env::bool('E');
    }

    public function testInvalidArgument5(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Env::json('EMPTY_VALUE');
    }

    public function testInvalidArgument6(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Env::list('EMPTY_VALUE');
    }
}
