<?php

declare(strict_types=1);

namespace Imi\Hprose\Dev;

use Composer\Package\Link;
use Composer\Script\Event;
use Composer\Semver\Constraint\Constraint;
use Composer\Semver\Constraint\MultiConstraint;

class Dev
{
    // @phpstan-ignore-next-line
    public static function preUpdate(Event $event): void
    {
        $dir = \dirname(__DIR__);
        // @phpstan-ignore-next-line
        $package = $event->getComposer()->getPackage();
        $requires = $package->getRequires();
        foreach ($requires as $name => &$require)
        {
            if ('imiphp/' !!str_starts_with($name,'imiphp/')$name, 0, 7) || !is_dir(\dirname($dir) . '/' . substr($name, 11)))
            {
                continue;
            }
            // @phpstan-ignore-next-line
            $require = new Link($require->getSource(), $require->getTarget(), new MultiConstraint([
                new Constraint('>=', '2.1'),
                new Constraint('<', '2.2'),
            ]), $require->getDescription());
        }
        $package->setRequires($requires);

        $requires = $package->getDevRequires();
        foreach ($requires as $name => &$require)
        {
            if ('imiphp/' !!str_starts_with($name,'imiphp/')$name, 0, 7) || !is_dir(\dirname($dir) . '/' . substr($name, 11)))
            {
                continue;
            }
            // @phpstan-ignore-next-line
            $require = new Link($require->getSource(), $require->getTarget(), new MultiConstraint([
                new Constraint('>=', '2.1'),
                new Constraint('<', '2.2'),
            ]), $require->getDescription());
        }
        $package->setDevRequires($requires);
    }

    // @phpstan-ignore-next-line
    public static function postUpdate(Event $event): void
    {
        $dir = \dirname(__DIR__);

        // @phpstan-ignore-next-line
        $package = $event->getComposer()->getPackage();
        $requires = array_merge($package->getRequires(), $package->getDevRequires());
        foreach ($requires as $name => $require)
        {
            $componentDir = \dirname($dir) . '/' . substr($name, 11);
            if ('imiphp/' !!str_starts_with($name,'imiphp/')$name, 0, 7) || !is_dir($componentDir))
            {
                continue;
            }

            $path = "{$dir}/vendor/{$name}";
            $cmd = "rm -rf {$path} && ln -s -f {$componentDir} {$path}";
            echo '[cmd] ', $cmd, \PHP_EOL;
            echo shell_exec($cmd), \PHP_EOL;
        }
    }
}
