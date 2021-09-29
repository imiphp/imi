$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

php "$__DIR__\..\..\..\..\..\..\Cli\bin\imi-cli" rr/stop --app-namespace "Imi\RoadRunner\Test\HttpServer" -w "$__DIR__\..\"
