<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests;

use Imi\Test\BaseTest;

class QuickStartTest extends BaseTest
{
    public function testQuickStart(): void
    {
        $content = $this->php(\dirname(__DIR__) . '/test.php');
        $this->assertTrue(str_contains($content, 'Test swoole quick start'), 'content: ' . $content);
    }
}
