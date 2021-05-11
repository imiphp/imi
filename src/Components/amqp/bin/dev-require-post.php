<?php

$dir = dirname(__DIR__);

$json = json_decode(file_get_contents($dir . '/composer.json'), true);

$bakFile = $dir . '/composer.json.bak';
if (is_file($bakFile))
{
    unlink($dir . '/composer.json');
    rename($bakFile, $dir . '/composer.json');
}

if (isset($json['require']))
{
    foreach ($json['require'] as $key => $value)
    {
        if ('imiphp/' !== substr($key, 0, 7))
        {
            continue;
        }

        $requirePackageComposerPath = "{$dir}/vendor/" . $key . '/composer.json';
        $bakFile = $requirePackageComposerPath . '.bak';
        if (is_file($bakFile))
        {
            unlink($requirePackageComposerPath);
            rename($bakFile, $requirePackageComposerPath);
        }
    }
}
