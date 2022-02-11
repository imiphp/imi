<?php

declare(strict_types=1);

namespace Imi\Macro;

use Composer\Autoload\ClassLoader;
use Swoole\Coroutine;
use function Yurun\Macro\includeFile;
use Yurun\Macro\MacroParser;

class AutoLoader
{
    protected ClassLoader $composerClassLoader;

    protected bool $hasSwoole = false;

    protected int $loadingCount = 0;

    protected int $lastHookFlags = 0;

    /**
     * 文件锁目录.
     *
     * 如果不设置就在输出文件本身加锁
     */
    protected string $lockFileDir = '';

    /**
     * 调试模式.
     */
    protected bool $debug = false;

    public function __construct(ClassLoader $composerClassLoader)
    {
        $this->composerClassLoader = $composerClassLoader;
        $this->hasSwoole = \extension_loaded('swoole');
        $this->lockFileDir = getenv('IMI_MACRO_LOCK_FILE_DIR') ?: '';
        $this->debug = (bool) getenv('IMI_MACRO_DEBUG');
    }

    /**
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->composerClassLoader->$name(...$arguments);
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $class The name of the class
     *
     * @return true|null True if loaded, null otherwise
     */
    public function loadClass($class)
    {
        if (str_starts_with($class, 'Yurun\Macro\\'))
        {
            return $this->composerClassLoader->loadClass($class);
        }
        $fileName = $this->composerClassLoader->findFile($class);
        if (false === $fileName)
        {
            return null;
        }
        if ($this->loadingCount || ($this->hasSwoole && Coroutine::getCid() >= 1))
        {
            if (1 === (++$this->loadingCount))
            {
                $this->lastHookFlags = $flags = \Swoole\Runtime::getHookFlags();
                \Swoole\Runtime::setHookFlags($flags & ~(\SWOOLE_HOOK_FILE | \SWOOLE_HOOK_STDIO));
            }
            try
            {
                $this->includeFile($fileName);
            }
            finally
            {
                if (0 === (--$this->loadingCount))
                {
                    \Swoole\Runtime::setHookFlags($this->lastHookFlags);
                }
            }
        }
        else
        {
            $this->includeFile($fileName);
        }

        return true;
    }

    private function includeFile(string $fileName): void
    {
        $macroFileName = $fileName . '.macro';
        if (file_exists($macroFileName))
        {
            if ($this->debug)
            {
                MacroParser::includeFile($macroFileName, $macroFileName . '.php', false, $this->lockFileDir);
            }
            else
            {
                MacroParser::includeFile($macroFileName);
            }
        }
        elseif (preg_match('/^\s*#\s*macro$/mUS', file_get_contents($fileName) ?: ''))
        {
            if ($this->debug)
            {
                MacroParser::includeFile($fileName, $fileName . '.macro.php', false, $this->lockFileDir);
            }
            else
            {
                MacroParser::includeFile($fileName);
            }
        }
        else
        {
            includeFile($fileName);
        }
    }

    public function getComposerClassLoader(): ClassLoader
    {
        return $this->composerClassLoader;
    }
}
