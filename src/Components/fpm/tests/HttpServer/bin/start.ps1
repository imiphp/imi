param(
    [Parameter(Mandatory=$false)][bool]$d = $false
)

$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

& $__DIR__\stop.ps1

if ($d)
{
    $procss = Start-Process -PassThru -FilePath "php" -ArgumentList "-d request_order=CGP -t $__DIR__\..\..\Web\public -S 127.0.0.1:13000"
    $procss.Id | Out-File $__DIR__/server.pid
}
else
{
    php -d request_order=CGP -t "$__DIR__/../../Web/public" -S 127.0.0.1:13000
}
