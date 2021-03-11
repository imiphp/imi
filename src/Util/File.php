<?php

namespace Imi\Util;

use Imi\Util\File\FileEnumItem;

/**
 * 文件相关工具类.
 */
abstract class File
{
    /**
     * 枚举文件.
     *
     * @param string $dirPath
     *
     * @return \RecursiveIteratorIterator|\ArrayIterator
     */
    public static function enum($dirPath)
    {
        if (!is_dir($dirPath))
        {
            return new \ArrayIterator();
        }
        $iterator = new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($iterator);

        return $files;
    }

    /**
     * 遍历文件和目录.
     *
     * @param string $dirPath
     *
     * @return \RecursiveIteratorIterator|\ArrayIterator
     */
    public static function enumAll($dirPath)
    {
        if (!is_dir($dirPath))
        {
            return new \ArrayIterator();
        }
        $iterator = new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        return $files;
    }

    /**
     * 枚举php文件.
     *
     * @param string $dirPath
     *
     * @return \RegexIterator|\ArrayIterator
     */
    public static function enumPHPFile($dirPath)
    {
        if (!is_dir($dirPath))
        {
            return new \ArrayIterator();
        }
        $directory = new \RecursiveDirectoryIterator($dirPath);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

        return $regex;
    }

    /**
     * 枚举文件，支持自定义中断进入下一级目录.
     *
     * @param string $dirPath
     *
     * @return \Generator
     */
    public static function enumFile(string $dirPath)
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
                yield $item;
                if (is_dir($item) && $item->getContinue())
                {
                    foreach (static::enumFile($item) as $fileItem)
                    {
                        yield $fileItem;
                    }
                }
            }
        }
        closedir($dh);
    }

    /**
     * 组合路径，目录后的/不是必须.
     *
     * @param string ...$args
     *
     * @return string
     */
    public static function path(...$args)
    {
        static $dsds = \DIRECTORY_SEPARATOR . \DIRECTORY_SEPARATOR;
        $result = implode(\DIRECTORY_SEPARATOR, $args);
        $offset = strpos($result, '://');
        if (false === $offset)
        {
            $offset = 0;
        }
        else
        {
            $offset += 3;
        }
        while (false !== ($position = strpos($result, $dsds, $offset)))
        {
            $result = substr_replace($result, \DIRECTORY_SEPARATOR, $position, 2);
        }

        return $result;
    }

    /**
     * 根据文件打开句柄，读取文件所有内容.
     *
     * @param mixed $fp
     *
     * @return string
     */
    public static function readAll($fp)
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
     *
     * @return bool
     */
    public static function createDir($dir, $mode = 0775)
    {
        if (empty($dir))
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
     * @param string $file    文件路径
     * @param string $content
     * @param int    $mode    文件的权限
     *
     * @return bool
     */
    public static function createFile($file, $content = '', $mode = 0775)
    {
        if (empty($file))
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
     *
     * @param string $dir
     *
     * @return bool
     */
    public static function isEmptyDir($dir)
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
            if ($handler)
            {
                closedir($handler);
            }
        }

        return true;
    }

    /**
     * 递归删除目录及目录中所有文件.
     *
     * @param string $dir
     *
     * @return bool
     */
    public static function deleteDir($dir)
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
     * @param string   $fileName
     * @param mixed    $data
     * @param int      $flags
     * @param resource $context
     *
     * @return int|false
     */
    public static function putContents($fileName, $data, $flags = 0, $context = null)
    {
        $dir = \dirname($fileName);
        if (!is_dir($dir) && !static::createDir($dir))
        {
            throw new \RuntimeException(sprintf('Create dir %s failed', $dir));
        }

        return file_put_contents($fileName, $data, $flags, $context);
    }

    /**
     * 获取绝对路径.
     *
     * @param string $path
     *
     * @return string
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
}
