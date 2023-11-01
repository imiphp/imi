<?php

declare(strict_types=1);

namespace AMQPApp\ApiServer\Controller;

use AMQPApp\AMQP\QueueTest\QueueTestMessage;
use AMQPApp\AMQP\Test\TestMessage;
use Imi\Controller\HttpController;
use Imi\Queue\Facade\Queue;
use Imi\Queue\Model\Message;
use Imi\Redis\Redis;
use Imi\RequestContext;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

#[Controller(prefix: '/')]
class IndexController extends HttpController
{
    /**
     * @return mixed
     */
    #[Action]
    #[Route(url: '/')]
    public function index()
    {
        $this->response->getBody()->write('imi');

        return $this->response;
    }

    /**
     * @return mixed
     */
    #[Action]
    public function publish(int $memberId = 19260817)
    {
        $message = new TestMessage();
        $message->setMemberId($memberId);
        $message->setContent('memberId:' . $memberId);
        // @phpstan-ignore-next-line
        $r1 = RequestContext::getBean('TestPublisher')->publish($message);

        $queueTestMessage = new QueueTestMessage();
        $queueTestMessage->setMemberId($memberId);
        $message = new Message();
        $message->setMessage($queueTestMessage->toMessage());
        Queue::getQueue('QueueTest1')->push($message);

        return [
            'r1'    => $r1,
        ];
    }

    /**
     * @param int $memberId
     *
     * @return mixed
     */
    #[Action]
    public function consume($memberId)
    {
        $r1 = Redis::get($key1 = 'imi-amqp:consume:1:' . $memberId);
        $r2 = Redis::get($key2 = 'imi-amqp:consume:QueueTest:' . $memberId);
        if (false !== $r1 && false !== $r2)
        {
            Redis::del($key1, $key2);
        }

        return [
            'r1' => $r1,
            'r2' => $r2,
        ];
    }
}
