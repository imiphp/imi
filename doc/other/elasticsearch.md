# ElasticSearch

首先需要引入`yurunsoft/guzzle-swoole`：

`composer require yurunsoft/guzzle-swoole`

实例化时候这么写即可：

```php
$client = \Elasticsearch\ClientBuilder::create()->setHosts(['192.168.0.233:9200'])
                                                ->setHandler(new \Yurun\Util\Swoole\Guzzle\Ring\SwooleHandler()) // 关键是这句
                                                ->build();
```
