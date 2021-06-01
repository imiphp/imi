<?php

declare(strict_types=1);

namespace Imi\Kafka\Test\Kafka;

use Yurun\Util\HttpRequest;

class KafkaMQTest extends BaseTest
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
            'success' => true,
        ], $response->json(true));
    }

    public function testConsum(): void
    {
        $http = new HttpRequest();
        $excepted = [
            'r1'    => '{"memberId":20180621}',
            'r2'    => '{"memberId":20180621}',
        ];
        for ($i = 0; $i < 10; ++$i)
        {
            sleep(1);
            $response = $http->get($this->host . 'consume?memberId=20180621');
            $data = $response->json(true);
            if ($excepted === $data)
            {
                break;
            }
        }
        $this->assertEquals($excepted, $data ?? null);
    }
}
