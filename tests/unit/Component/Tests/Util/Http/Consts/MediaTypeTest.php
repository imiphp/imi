<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Http\Consts;

use Imi\Test\BaseTest;
use Imi\Util\Http\Consts\MediaType;

class MediaTypeTest extends BaseTest
{
    public function testGetContentType()
    {
        $this->assertEquals(MediaType::IMAGE_PNG, MediaType::getContentType('png'));
    }
}
