<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Format;

use Imi\Test\BaseTest;

abstract class BaseFormatTest extends BaseTest
{
    public const DATA = [
        'name'     => 'imi',
        'birthday' => '2018-06-21',
    ];

    protected string $class;

    public function test(): void
    {
        $formatter = new $this->class();

        $str = $formatter->encode(self::DATA);
        $this->assertIsString($str);
        $this->assertNotEmpty($str);

        $data = $formatter->decode($str);
        $this->assertIsArray($data);
        $this->assertEquals(self::DATA, $data);
    }
}
