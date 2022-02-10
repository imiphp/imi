$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

if (Test-Path $__DIR__\http.pid)
{
    $processId = $(Get-Content -Path $__DIR__\http.pid)
    if (Get-Process -Id $processId -Ea SilentlyContinue)
    {
        Stop-Process -Id $processId
    }
}

if (Test-Path $__DIR__\websocket.pid)
{
    $processId = $(Get-Content -Path $__DIR__\websocket.pid)
    if (Get-Process -Id $processId -Ea SilentlyContinue)
    {
        Stop-Process -Id $processId
    }
}

if (Test-Path $__DIR__\gateway.pid)
{
    $processId = $(Get-Content -Path $__DIR__\gateway.pid)
    if (Get-Process -Id $processId -Ea SilentlyContinue)
    {
        Stop-Process -Id $processId
    }
}

if (Test-Path $__DIR__\register.pid)
{
    $processId = $(Get-Content -Path $__DIR__\register.pid)
    if (Get-Process -Id $processId -Ea SilentlyContinue)
    {
        Stop-Process -Id $processId
    }
}
