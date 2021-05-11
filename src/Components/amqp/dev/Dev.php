<?php

namespace Imi\AMQP\Dev;

use Composer\Package\Link;
use Composer\Script\Event;
use Composer\Semver\Constraint\Constraint;

class Dev
{
    public static function preUpdate(Event $event): void
    {
        $package = $event->getComposer()->getPackage();
        $requires = $package->getRequires();
        foreach ($requires as $name => &$require)
        {
            if ('imiphp/' !== substr($name, 0, 7))
            {
                continue;
            }
            $require = new Link($require->getSource(), $require->getTarget(), new Constraint('>', '0'), $require->getDescription());
        }
        $package->setRequires($requires);
    }

    public static function postUpdate(Event $event): void
    {
        $dir = \dirname(__DIR__);

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
