$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

& $__DIR__\stop.ps1

$procss = Start-Process -PassThru -FilePath "php" -ArgumentList "$__DIR__\imi workerman/start --name channel"
$procss.Id | Out-File $__DIR__/channel.pid

$procss = Start-Process -PassThru -FilePath "php" -ArgumentList "$__DIR__\imi workerman/start --name http"
$procss.Id | Out-File $__DIR__/http.pid
