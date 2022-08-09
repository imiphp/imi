#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

cd $__DIR__/../

src/Cli/bin/imi-cli --app-namespace "Imi" generate/requestContextProxy --target "Imi\Server\Http\Message\Proxy\RequestProxy" --class "Imi\Server\Http\Message\Contract\IHttpRequest" --name request && \

src/Cli/bin/imi-cli --app-namespace "Imi" generate/requestContextProxy --target "Imi\Server\Http\Message\Proxy\RequestProxyObject" --class "Imi\Server\Http\Message\Contract\IHttpRequest" --name request --bean HttpRequestProxy --interface "Imi\Server\Http\Message\Contract\IHttpRequest" --recursion=false && \

src/Cli/bin/imi-cli --app-namespace "Imi" generate/requestContextProxy --target "Imi\Server\Http\Message\Proxy\ResponseProxy" --class "Imi\Server\Http\Message\Contract\IHttpResponse" --name response && \

src/Cli/bin/imi-cli --app-namespace "Imi" generate/requestContextProxy --target "Imi\Server\Http\Message\Proxy\ResponseProxyObject" --class "Imi\Server\Http\Message\Contract\IHttpResponse" --name response --bean HttpResponseProxy --interface "Imi\Server\Http\Message\Contract\IHttpResponse" --recursion=false && \

src/Cli/bin/imi-cli --app-namespace "Imi" generate/requestContextProxy --target "Imi\Server\TcpServer\Message\Proxy\ReceiveDataProxy" --class "Imi\Server\TcpServer\Message\IReceiveData" --name receiveData --bean TcpReceiveDataProxy --interface "Imi\Server\TcpServer\Message\IReceiveData" --recursion=false && \

src/Cli/bin/imi-cli --app-namespace "Imi" generate/requestContextProxy --target "Imi\Server\UdpServer\Message\Proxy\PacketDataProxy" --class "Imi\Server\UdpServer\Message\IPacketData" --name packetData --bean UdpPacketDataProxy --interface "Imi\Server\UdpServer\Message\IPacketData" --recursion=false && \

src/Cli/bin/imi-cli --app-namespace "Imi" generate/requestContextProxy --target "Imi\Server\WebSocket\Message\Proxy\FrameProxy" --class "Imi\Server\WebSocket\Message\IFrame" --name frame --bean WebSocketFrameProxy --interface "Imi\Server\WebSocket\Message\IFrame" --recursion=false && \

vendor/bin/php-cs-fixer fix
