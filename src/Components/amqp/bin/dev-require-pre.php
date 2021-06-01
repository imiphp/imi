<?php

$dir = dirname(__DIR__);

$cmd = "rm -rf {$dir}/vendor/imiphp && mkdir -p {$dir}/vendor/imiphp";
echo '[cmd] ', $cmd, \PHP_EOL;
echo shell_exec($cmd), \PHP_EOL;
$json = json_decode(file_get_contents($dir . '/composer.json'), true);
$bakFile = $dir . '/composer.json.bak';
if (!is_file($bakFile))
{
    copy($dir . '/composer.json', $bakFile);
}
if (isset($json['require']))
{
    foreach ($json['require'] as $key => $value)
    {
        if ('imiphp/' !== substr($key, 0, 7))
        {
            continue;
        }

        $path = "{$dir}/vendor/{$key}";
        $componentDir = dirname($dir) . '/' . substr($key, 11);
        $cmd = "rm -rf {$path} && ln -s -f {$componentDir} {$path}";
        echo '[cmd] ', $cmd, \PHP_EOL;
        echo shell_exec($cmd), \PHP_EOL;

        preg_match('/(\d+\.?(\d+(\.\d+))?)/', $value, $matches);
        $requirePackageComposerPath = "{$dir}/vendor/" . $key . '/composer.json';
        $bakFile = $requirePackageComposerPath . '.bak';
        if (!is_file($bakFile))
        {
            copy($requirePackageComposerPath, $bakFile);
        }

        $composerJson = json_decode(file_get_contents($requirePackageComposerPath), true);
        $version = explode('.', $matches[1]);
        array_pop($version);
        $version[] = '9999';
        $composerJson['version'] = implode('.', $version);
        file_put_contents($requirePackageComposerPath, json_encode($composerJson, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE));

        $json['repositories'][$key] = [
            'type'    => 'path',
            'url'     => 'vendor/' . $key,
            'options' => [
                'symlink' => false,
            ],
        ];
    }

    file_put_contents($dir . '/composer.json', json_encode($json, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE));
}
