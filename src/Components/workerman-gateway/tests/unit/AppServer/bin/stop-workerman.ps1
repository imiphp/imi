$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

Stop-Process -Id $(Get-Content -Path $__DIR__\http.pid)

Stop-Process -Id $(Get-Content -Path $__DIR__\websocket.pid)

Stop-Process -Id $(Get-Content -Path $__DIR__\gateway.pid)

Stop-Process -Id $(Get-Content -Path $__DIR__\register.pid)
