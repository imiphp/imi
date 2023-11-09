$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

$server = $args[0]

$procss = Start-Process -PassThru -FilePath "php" -ArgumentList "$__DIR__\workerman workerman/start --name $server" -RedirectStandardOutput "$__DIR__\..\logs\$server.log" -RedirectStandardError "$__DIR__\..\logs\$server.error.log"
$procss.Id | Out-File $__DIR__/$server.pid
