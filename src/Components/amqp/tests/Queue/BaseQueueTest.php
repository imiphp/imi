<?php

declare(strict_types=1);

namespace Imi\AMQP\Test\Queue;

use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Model\Message;
use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

abstract class BaseQueueTest extends TestCase
{
    abstract protected function getDriver(): IQueueDriver;

    public function testClear(): void
    {
        $this->getDriver()->clear();
        $this->assertTrue(true);
    }

    public function testPush(): void
    {
        $driver = $this->getDriver();

        $message = new Message();
        $message->setMessage('testPush');
        $messageId = $driver->push($message);
        $this->assertNotEmpty($messageId);
    }

    public function testPop(): void
    {
        $driver = $this->getDriver();
        $message = $driver->pop();
        $this->assertInstanceOf(\Imi\Queue\Contract\IMessage::class, $message);
        $this->assertNotEmpty($message->getMessageId());
        $this->assertEquals('testPush', $message->getMessage());
        $driver->success($message);
    }

    public function testPopTimeout(): void
    {
        $message = $totalTime = null;
        $channel = new Channel(1);
        go(function () use (&$message, &$totalTime, $channel) {
            go(function () use ($channel) {
                Coroutine::sleep(1);
                $message = new Message();
                $message->setMessage('testPopTimeout');
                $messageId = $this->getDriver()->push($message);
                $this->assertNotEmpty($messageId);
                $channel->push(1);
            });
            $time = microtime(true);
            $message = $this->getDriver()->pop(3);
            $totalTime = microtime(true) - $time;
            $channel->push(1);
        });
        for ($i = 0; $i < 2; ++$i)
        {
            $channel->pop(3);
        }
        $this->assertEquals(1, (int) $totalTime);
        $this->assertInstanceOf(\Imi\Queue\Contract\IMessage::class, $message);
        $this->assertNotEmpty($message->getMessageId());
        $this->assertEquals('testPopTimeout', $message->getMessage());
    }

    public function testPushDelay(): void
    {
        $driver = $this->getDriver();
        $driver->clear();
        $message = new Message();
        $message->setMessage('testPushDelay');
        $messageId = $driver->push($message, 3);
        $this->assertNotEmpty($messageId);

        $time = microtime(true);
        for ($i = 0; $i < 3; ++$i)
        {
            sleep(1);
            $message = $driver->pop();
            if (null !== $message)
            {
                break;
            }
        }
        $this->assertEquals(3, (int) (microtime(true) - $time));
        $this->assertInstanceOf(\Imi\Queue\Contract\IMessage::class, $message);
        $this->assertNotEmpty($message->getMessageId());
        $this->assertEquals('testPushDelay', $message->getMessage());
    }

    public function testDelete(): void
    {
        $driver = $this->getDriver();

        $message = new Message();
        $message->setMessage('testDelete');
        $messageId = $driver->push($message);
        $this->assertNotEmpty($messageId);

        $message->setMessageId($messageId);

        $this->assertTrue($driver->delete($message));
    }

    public function testClearAndStatus(): void
    {
        $driver = $this->getDriver();
        $driver->clear();
        $status = $driver->status();
        $this->assertEquals(0, $status->getReady());
        $this->assertEquals(0, $status->getWorking());
        $this->assertEquals(0, $status->getDelay());
        $this->assertEquals(0, $status->getTimeout());
        $this->assertEquals(0, $status->getFail());

        $message = new Message();
        $message->setMessage('testClearAndStatus-a');
        $messageId = $driver->push($message);
        $this->assertNotEmpty($messageId);

        $message->setMessage('testClearAndStatus-b');
        $messageId = $driver->push($message, 3600);
        $this->assertNotEmpty($messageId);

        $status = $driver->status();
        $this->assertEquals(1, $status->getReady());
        $this->assertEquals(1, $status->getDelay());
    }

    public function testRestoreFailMessages(): void
    {
        $driver = $this->getDriver();
        $driver->clear();

        $message = new Message();
        $message->setMessage('testRestoreFailMessages');
        $messageId = $driver->push($message);
        $this->assertNotEmpty($messageId);

        $message = $driver->pop();
        $this->assertNotEmpty($message->getMessageId());
        $this->assertEquals('testRestoreFailMessages', $message->getMessage());

        $driver->fail($message);

        $this->assertEquals(1, $driver->restoreFailMessages());

        // requeue
        $message = $driver->pop();
        $this->assertNotEmpty($message->getMessageId());
        $this->assertEquals('testRestoreFailMessages', $message->getMessage());

        $driver->fail($message, true);

        $this->assertEquals(1, $driver->status()->getReady());
        $this->assertEquals(0, $driver->restoreFailMessages());
    }

    public function testRestoreTimeoutMessages(): void
    {
        $driver = $this->getDriver();
        $driver->clear();

        $message = new Message();
        $message->setMessage('testRestoreTimeoutMessages');
        $message->setWorkingTimeout(1);
        $messageId = $driver->push($message);
        $this->assertNotEmpty($messageId);

        $message = $driver->pop();
        $this->assertNotEmpty($message->getMessageId());
        $this->assertEquals('testRestoreTimeoutMessages', $message->getMessage());

        sleep(1);

        $message = $driver->pop();
        $this->assertNull($message);

        $this->assertEquals(1, $driver->restoreTimeoutMessages());
    }
}
