<?php

declare(strict_types=1);

namespace AMQPApp\ApiServer\Controller;

use AMQPApp\AMQP\QueueTest\QueueTestMessage;
use AMQPApp\AMQP\Test\TestMessage;
use AMQPApp\AMQP\Test2\TestMessage2;
use Imi\Controller\HttpController;
use Imi\Queue\Facade\Queue;
use Imi\Queue\Model\Message;
use Imi\Redis\Redis;
use Imi\RequestContext;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

/**
 * @Controller("/")
 */
class IndexController extends HttpController
{
    /**
     * @Action
     * @Route("/")
     *
     * @return mixed
     */
    public function index()
    {
        $this->response->getBody()->write('imi');

        return $this->response;
    }

    /**
     * @Action
     *
     * @return mixed
     */
    public function publish(int $memberId = 19260817)
    {
        $message = new TestMessage();
        $message->setMemberId($memberId);
        $r1 = RequestContext::getBean('TestPublisher')->publish($message);

        $message2 = new TestMessage2();
        $message2->setMemberId($memberId);
        $message2->setContent('memberId:' . $memberId);
        $r2 = RequestContext::getBean('TestPublisher2')->publish($message2);

        $queueTestMessage = new QueueTestMessage();
        $queueTestMessage->setMemberId($memberId);
        $message = new Message();
        $message->setMessage($queueTestMessage->toMessage());
        Queue::getQueue('QueueTest1')->push($message);

        return [
            'r1'    => $r1,
            'r2'    => $r2,
        ];
    }

    /**
     * @Action
     *
     * @param int $memberId
     *
     * @return mixed
     */
    public function consume($memberId)
    {
        $r1 = Redis::get($key1 = 'imi-amqp:consume:1:' . $memberId);
        $r2 = Redis::get($key2 = 'imi-amqp:consume:2:' . $memberId);
        $r3 = Redis::get($key3 = 'imi-amqp:consume:QueueTest:' . $memberId);
        if (false !== $r1 && false !== $r2 && false !== $r3)
        {
            Redis::del($key1, $key2, $key3);
        }

        return [
            'r1' => $r1,
            'r2' => $r2,
            'r3' => $r3,
        ];
    }
}
