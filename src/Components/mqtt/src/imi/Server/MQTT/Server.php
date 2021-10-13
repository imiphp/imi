<?php

declare(strict_types=1);

namespace Imi\Server\MQTT;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean(name="MQTTServer", env="swoole")
 * MQTT 服务器类.
 */
class Server extends \Imi\Swoole\Server\TcpServer\Server
{
    /**
     * {@inheritDoc}
     */
    public function __construct(string $name, array $config, bool $isSubServer = false)
    {
        parent::__construct($name, $config, $isSubServer);
        if (!isset($this->config['dataParser']))
        {
            $this->config['dataParser'] = \Imi\Server\MQTT\DataParser\MQTTDataParser::class;
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function createServer(): void
    {
        parent::createServer();
        $this->config['configs']['open_mqtt_protocol'] = true;
    }

    /**
     * {@inheritDoc}
     */
    protected function createSubServer(): void
    {
        parent::createSubServer();
        $this->config['configs']['open_mqtt_protocol'] = true;
    }
}
