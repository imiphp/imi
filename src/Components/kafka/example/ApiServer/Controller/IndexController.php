<?php

declare(strict_types=1);

namespace KafkaApp\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Kafka\Pool\KafkaPool;
use Imi\Queue\Facade\Queue;
use Imi\Queue\Model\Message;
use Imi\Redis\Redis;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use KafkaApp\Kafka\QueueTest\QueueTestMessage;

#[Controller(prefix: '/')]
class IndexController extends HttpController
{
    #[Action]
    #[Route(url: '/')]
    public function index(): mixed
    {
        $this->response->getBody()->write('imi');

        return $this->response;
    }

    #[Action]
    public function publish(int $memberId = 19260817): array
    {
        $producer = KafkaPool::getInstance();
        $producer->send('queue-imi-1', json_encode([
            'memberId' => $memberId,
        ], \JSON_THROW_ON_ERROR));

        $queueTestMessage = new QueueTestMessage();
        $queueTestMessage->setMemberId($memberId);
        $message = new Message();
        $message->setMessage($queueTestMessage->toMessage());
        Queue::getQueue('QueueTest1')->push($message);

        return [
            'success' => true,
        ];
    }

    #[Action]
    public function consume(int $memberId): array
    {
        $r1 = Redis::get($key1 = 'imi-kafka:consume:1:' . $memberId);
        $r2 = Redis::get($key2 = 'imi-kafka:consume:QueueTest:' . $memberId);
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
