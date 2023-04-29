<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Server\Http;

use Imi\Server\Http\Message\Emitter\SseMessageEvent;
use Imi\Test\BaseTest;

class SseMessageEventTest extends BaseTest
{
    public function test(): void
    {
        $this->assertEquals("data: testData\n\n", (string) new SseMessageEvent('testData'));

        $this->assertEquals("data: testData\ndata: second line\n\n", (string) new SseMessageEvent("testData\nsecond line"));

        $this->assertEquals("event: eventName\ndata: testData\n\n", (string) new SseMessageEvent('testData', 'eventName'));

        $this->assertEquals("event: eventName\nid: 123\ndata: testData\n\n", (string) new SseMessageEvent('testData', 'eventName', '123'));

        $this->assertEquals("event: eventName\nid: 123\nretry: 15000\ndata: testData\n\n", (string) new SseMessageEvent('testData', 'eventName', '123', 15000));

        $this->assertEquals(": This is a test\nevent: eventName\nid: 123\nretry: 15000\ndata: testData\n\n", (string) new SseMessageEvent('testData', 'eventName', '123', 15000, 'This is a test'));
    }
}
