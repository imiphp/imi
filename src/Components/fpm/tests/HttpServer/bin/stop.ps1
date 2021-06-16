$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

$content = (Get-Content -Path $__DIR__\server.pid)

Stop-Process -Id $content
