param(
    [Parameter(Mandatory=$false)][bool]$d = $false
)

$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

& $__DIR__\stop.ps1

if ($d)
{
    Start-Process -PassThru -FilePath "php" -ArgumentList """$__DIR__\..\..\..\..\..\..\Cli\bin\imi-cli"" rr/start --app-namespace ""Imi\RoadRunner\Test\HttpServer"" -w ""$__DIR__\.."""
}
else
{
    php "$__DIR__\..\..\..\..\..\..\Cli\bin\imi-cli" rr/start --app-namespace "Imi\RoadRunner\Test\HttpServer" -w "$__DIR__\..\"
}
