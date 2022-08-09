#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

cd $__DIR__/../

src/Cli/bin/imi-cli --app-namespace "Imi\JWT" --bootstrap "src/Components/jwt/vendor/autoload.php" generate/facade "Imi\JWT\Facade\JWT" "JWT" && \

src/Cli/bin/imi-cli --app-namespace "Imi\Queue" --bootstrap "src/Components/queue/vendor/autoload.php" generate/facade "Imi\Queue\Facade\Queue" "imiQueue" && \

src/Cli/bin/imi-cli --app-namespace "Imi\Swoole" --bootstrap "src/Components/swoole/vendor/autoload.php" generate/facade "Imi\Swoole\Util\Co\ChannelContainer" "Yurun\Swoole\CoPool\ChannelContainer" && \

src/Cli/bin/imi-cli --app-namespace "Imi" generate/facade "Imi\Server\Session\Session" "SessionManager" --request=true && \

vendor/bin/php-cs-fixer fix
