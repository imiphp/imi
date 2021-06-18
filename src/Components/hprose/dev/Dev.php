<?php

namespace Imi\Hprose\Dev;

use Composer\Package\Link;
use Composer\Script\Event;
use Composer\Semver\Constraint\Constraint;

class Dev
{
    // @phpstan-ignore-next-line
    public static function preUpdate(Event $event): void
    {
        // @phpstan-ignore-next-line
        $package = $event->getComposer()->getPackage();
        $requires = $package->getRequires();
        foreach ($requires as $name => &$require)
        {
            if ('imiphp/' !== substr($name, 0, 7))
            {
                continue;
            }
            // @phpstan-ignore-next-line
            $require = new Link($require->getSource(), $require->getTarget(), new Constraint('>', '0'), $require->getDescription());
        }
        $package->setRequires($requires);
    }

    // @phpstan-ignore-next-line
    public static function postUpdate(Event $event): void
    {
        $dir = \dirname(__DIR__);

        // @phpstan-ignore-next-line
        $package = $event->getComposer()->getPackage();
        $requires = $package->getRequires();
        foreach ($requires as $name => $require)
        {
            if ('imiphp/' !== substr($name, 0, 7))
            {
                continue;
            }

            $componentDir = \dirname($dir) . '/' . substr($name, 11);
            $path = "{$dir}/vendor/{$name}";
            $cmd = "rm -rf {$path} && ln -s -f {$componentDir} {$path}";
            echo '[cmd] ', $cmd, \PHP_EOL;
            echo `$cmd`, \PHP_EOL;
        }
    }
}
