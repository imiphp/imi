$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

& $__DIR__\stop-workerman.ps1

$procss = Start-Process -PassThru -FilePath "php" -ArgumentList "$__DIR__\workerman workerman/start --name register"
$procss.Id | Out-File $__DIR__/register.pid

$procss = Start-Process -PassThru -FilePath "php" -ArgumentList "$__DIR__\workerman workerman/start --name gateway"
$procss.Id | Out-File $__DIR__/gateway.pid

$procss = Start-Process -PassThru -FilePath "php" -ArgumentList "$__DIR__\workerman workerman/start --name websocket"
$procss.Id | Out-File $__DIR__/websocket.pid

$procss = Start-Process -PassThru -FilePath "php" -ArgumentList "$__DIR__\workerman workerman/start --name http"
$procss.Id | Out-File $__DIR__/http.pid
