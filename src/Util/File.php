<?php

# macro

declare(strict_types=1);

namespace Imi\Util;

use Imi\Util\File\FileEnumItem;
use Swoole\Coroutine;

/**
 * 文件相关工具类.
 */
class File
{
    private function __construct()
    {
    }

    /**
     * 枚举文件.
     *
     * @return \RecursiveIteratorIterator|array
     */
    public static function enum(string $dirPath)
    {
        if (!is_dir($dirPath))
        {
            return [];
        }
        $iterator = new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);

        return new \RecursiveIteratorIterator($iterator);
    }

    /**
     * 遍历文件和目录.
     *
     * @return \RecursiveIteratorIterator|\ArrayIterator
     */
    public static function enumAll(string $dirPath)
    {
        if (!is_dir($dirPath))
        {
            return new \ArrayIterator();
        }
        $iterator = new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);

        return new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);
    }

    /**
     * 枚举php文件.
     *
     * @return \RegexIterator|\ArrayIterator
     */
    public static function enumPHPFile(string $dirPath, string $pattern = '/^.+\.php$/i')
    {
        if (!is_dir($dirPath))
        {
            return new \ArrayIterator();
        }
        $directory = new \RecursiveDirectoryIterator($dirPath);
        $iterator = new \RecursiveIteratorIterator($directory);

        return new \RegexIterator($iterator, $pattern, \RecursiveRegexIterator::GET_MATCH);
    }

    /**
     * 枚举文件，支持自定义中断进入下一级目录.
     *
     * @return \Generator|iterable<FileEnumItem>|false
     */
    public static function enumFile(string $dirPath, ?string $pattern = null, array $extensionNames = [])
    {
        #if \extension_loaded('swoole')
        if (
            #if 0
            \extension_loaded('swoole') &&
            #endif
            \Swoole\Coroutine::getCid() > -1)
        {
            $channel = new \Swoole\Coroutine\Channel(16);
            Coroutine::create(function () use ($channel, $dirPath, $pattern, $extensionNames) {
                static::enumFileSwoole($channel, $dirPath, $pattern, $extensionNames);
                $channel->close();
            });
            while (false !== ($result = $channel->pop()))
            {
                yield $result;
            }
        }
        else
        {
            #endif

            yield from self::enumFileSync($dirPath, $pattern, $extensionNames);

            #if \extension_loaded('swoole')
        }
        #endif
    }

    /**
     * 同步枚举文件，支持自定义中断进入下一级目录.
     *
     * @return \Generator|iterable<FileEnumItem>|false
     */
    public static function enumFileSync(string $dirPath, ?string $pattern = null, array $extensionNames = [])
    {
        if (!is_dir($dirPath))
        {
            return false;
        }
        $dh = opendir($dirPath);
        while ($file = readdir($dh))
        {
            if ('.' !== $file && '..' !== $file)
            {
                $item = new FileEnumItem($dirPath, $file);
                $fullPath = $item->getFullPath();
                if (null !== $pattern && !preg_match($pattern, $fullPath))
                {
                    continue;
                }
                if (!$extensionNames || \in_array(pathinfo($fullPath, \PATHINFO_EXTENSION), $extensionNames))
                {
                    yield $item;
                }
                if ($item->getContinue() && is_dir($fullPath))
                {
                    yield from static::enumFileSync($fullPath, $pattern, $extensionNames);
                }
            }
        }
        closedir($dh);
    }

    /**
     * Swoole 环境下枚举文件，将结果 push 到 Channel，支持自定义中断进入下一级目录.
     */
    public static function enumFileSwoole(Coroutine\Channel $channel, string $dirPath, ?string $pattern = null, array $extensionNames = []): bool
    {
        if (!is_dir($dirPath))
        {
            return false;
        }
        $dh = opendir($dirPath);
        while ($file = readdir($dh))
        {
            if ('.' !== $file && '..' !== $file)
            {
                $item = new FileEnumItem($dirPath, $file);
                $fullPath = $item->getFullPath();
                if (null !== $pattern && !preg_match($pattern, $fullPath))
                {
                    continue;
                }
                if (!$extensionNames || \in_array(pathinfo($fullPath, \PATHINFO_EXTENSION), $extensionNames))
                {
                    $channel->push($item);
                }
                if ($item->getContinue() && is_dir($fullPath))
                {
                    static::enumFileSwoole($channel, $fullPath, $pattern, $extensionNames);
                }
            }
        }
        closedir($dh);

        return true;
    }

    /**
     * 组合路径，目录后的/不是必须.
     *
     * @param string ...$args
     */
    public static function path(string ...$args): string
    {
        if (!$args)
        {
            return '';
        }
        $offset = strpos($args[0], '://');
        if (false === $offset)
        {
            $offset = 0;
            $ds = \DIRECTORY_SEPARATOR;
            $dsds = \DIRECTORY_SEPARATOR . \DIRECTORY_SEPARATOR;
        }
        else
        {
            $offset += 3;
            $ds = '/';
            $dsds = '//';
        }
        $result = implode($ds, $args);
        while (false !== ($offset = strpos($result, (string) $dsds, $offset)))
        {
            $result = substr_replace($result, $ds, $offset, 2);
        }

        return $result;
    }

    /**
     * 根据文件打开句柄，读取文件所有内容.
     *
     * @param resource $fp
     */
    public static function readAll($fp): string
    {
        $data = '';
        while (!feof($fp))
        {
            $data .= fread($fp, 4096);
        }

        return $data;
    }

    /**
     * 创建一个目录.
     *
     * @param string $dir  目录路径
     * @param int    $mode 目录的权限
     */
    public static function createDir(string $dir, int $mode = 0775): bool
    {
        if ('' === $dir)
        {
            return false;
        }
        if (is_dir($dir))
        {
            return true;
        }
        if (mkdir($dir, $mode, true))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 创建一个文件.
     *
     * @param string $file 文件路径
     * @param int    $mode 文件的权限
     */
    public static function createFile(string $file, string $content = '', int $mode = 0775): bool
    {
        if ('' === $file)
        {
            return false;
        }
        if (is_file($file))
        {
            return true;
        }
        $dir = \dirname($file);
        self::createDir($dir, $mode);
        $fh = fopen($file, 'a');
        if ($fh)
        {
            if ('' !== $content)
            {
                fwrite($fh, $content);
            }
            fclose($fh);

            return true;
        }

        return false;
    }

    /**
     * 判断是否为空目录.
     */
    public static function isEmptyDir(string $dir): bool
    {
        try
        {
            $handler = opendir($dir);
            if (!$handler)
            {
                return true;
            }
            while ($file = readdir($handler))
            {
                if ('.' !== $file && '..' !== $file)
                {
                    return false;
                }
            }
        }
        finally
        {
            if (isset($handler) && $handler)
            {
                closedir($handler);
            }
        }

        return true;
    }

    /**
     * 递归删除目录及目录中所有文件.
     */
    public static function deleteDir(string $dir): bool
    {
        $dh = opendir($dir);
        while ($file = readdir($dh))
        {
            if ('.' !== $file && '..' !== $file)
            {
                $fullpath = $dir . '/' . $file;
                if (is_dir($fullpath) && !is_link($fullpath))
                {
                    self::deleteDir($fullpath);
                }
                else
                {
                    unlink($fullpath);
                }
            }
        }
        closedir($dh);

        return rmdir($dir);
    }

    /**
     * 写入内容到文件
     * 如果目录不存在自动创建多级目录.
     *
     * @param resource $context
     *
     * @return int|false
     */
    public static function putContents(string $fileName, string $data, int $flags = 0, $context = null)
    {
        $dir = \dirname($fileName);
        if (!static::createDir($dir))
        {
            throw new \RuntimeException(sprintf('Create dir %s failed', $dir));
        }

        return file_put_contents($fileName, $data, $flags, $context);
    }

    /**
     * 获取绝对路径.
     */
    public static function absolute(string $path): string
    {
        $path = str_replace(['/', '\\'], \DIRECTORY_SEPARATOR, $path);
        $parts = explode(\DIRECTORY_SEPARATOR, $path);
        $absolutes = [];
        foreach ($parts as $i => $part)
        {
            if ('.' === $part)
            {
                continue;
            }
            if ('' === $part && $i > 0)
            {
                continue;
            }
            if ('..' === $part)
            {
                array_pop($absolutes);
            }
            else
            {
                $absolutes[] = $part;
            }
        }

        return implode(\DIRECTORY_SEPARATOR, $absolutes);
    }

    public static function getBaseNameBeforeFirstDot(string $path): string
    {
        $path = basename($path);
        $index = strpos($path, '.');
        if (false === $index)
        {
            return '';
        }

        return substr($path, 0, $index);
    }
}
