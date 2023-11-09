$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

& $__DIR__\stop-workerman.ps1

$procss = Start-Process -PassThru -FilePath "powershell" -ArgumentList "$__DIR__\start-server.ps1 websocket"

$procss = Start-Process -PassThru -FilePath "powershell" -ArgumentList "$__DIR__\start-server.ps1 register"

$procss = Start-Process -PassThru -FilePath "powershell" -ArgumentList "$__DIR__\start-server.ps1 gateway"

$procss = Start-Process -PassThru -FilePath "powershell" -ArgumentList "$__DIR__\start-server.ps1 http"
