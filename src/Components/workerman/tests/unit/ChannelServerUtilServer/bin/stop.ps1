$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

Stop-Process -Id $(Get-Content -Path $__DIR__\channel.pid)

Stop-Process -Id $(Get-Content -Path $__DIR__\http.pid)
