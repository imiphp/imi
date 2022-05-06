# ElasticSearch

[toc]

ElasticSearch是一个基于Lucene的搜索服务器。它提供了一个分布式多用户能力的全文搜索引擎，基于RESTful web接口。Elasticsearch是用Java语言开发的，并作为Apache许可条款下的开放源码发布，是一种流行的企业级搜索引擎。

## 使用

首先需要引入 [Guzzle-Swoole](guzzle.html)：

`composer require yurunsoft/guzzle-swoole`

实例化时候这么写即可：

```php
$client = \Elasticsearch\ClientBuilder::create()->setHosts(['192.168.0.233:9200'])
                                                ->setHandler(new \Yurun\Util\Swoole\Guzzle\Ring\SwooleHandler()) // 关键是这句
                                                ->build();
```
