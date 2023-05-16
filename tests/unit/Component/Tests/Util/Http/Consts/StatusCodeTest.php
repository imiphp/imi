<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Http\Consts;

use Imi\Test\BaseTest;
use Imi\Util\Http\Consts\StatusCode;

class StatusCodeTest extends BaseTest
{
    public function testGetReasonPhrase()
    {
        $this->assertEquals('OK', StatusCode::getReasonPhrase(StatusCode::OK));
    }
}
