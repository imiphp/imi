$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

& $__DIR__\stop.ps1

$procss = Start-Process -PassThru -FilePath "php" -ArgumentList "$__DIR__\imi workerman/start"
$procss.Id | Out-File $__DIR__/server.pid
