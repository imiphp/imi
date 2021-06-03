<?php

declare(strict_types=1);

namespace Imi\AMQP\Test\RabbitMQ;

use Yurun\Util\HttpRequest;

class RabbitMQTest extends BaseTest
{
    /**
     * @var string
     */
    private $host = 'http://127.0.0.1:8080/';

    public function testPublish(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'publish?memberId=20180621');
        $this->assertEquals([
            'r1'    => true,
            'r2'    => true,
        ], $response->json(true));
    }

    public function testConsume(): void
    {
        $http = new HttpRequest();
        $excepted = [
            'r1'    => '{"memberId":20180621}',
            'r2'    => '{"memberId":20180621,"content":"memberId:20180621"}',
            'r3'    => '{"memberId":20180621}',
        ];
        for ($i = 0; $i < 10; ++$i)
        {
            $response = $http->get($this->host . 'consume?memberId=20180621');
            $data = $response->json(true);
            if ($excepted === $data)
            {
                break;
            }
            sleep(1);
        }
        $this->assertEquals($excepted, $data ?? null);
    }
}
