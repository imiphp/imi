<?php

namespace Imi\Util;

/**
 * 文件相关工具类
 */
abstract class File
{
    /**
     * 枚举文件
     * @param string $dirPath
     * @return \RecursiveIteratorIterator|\ArrayIterator
     */
    public static function enum($dirPath)
    {
        if (!is_dir($dirPath)) {
            return new \ArrayIterator();
        }
        $iterator = new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($iterator);
        return $files;
    }

    /**
     * 遍历文件和目录
     *
     * @param string $dirPath
     * @return \RecursiveIteratorIterator|\ArrayIterator
     */
    public static function enumAll($dirPath)
    {
        if (!is_dir($dirPath)) {
            return new \ArrayIterator();
        }
        $iterator = new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);
        return $files;
    }

    /**
     * 枚举php文件
     * @param string $dirPath
     * @return \RegexIterator|ArrayIterator
     */
    public static function enumPHPFile($dirPath)
    {
        if (!is_dir($dirPath)) {
            return new \ArrayIterator();
        }
        $directory = new \RecursiveDirectoryIterator($dirPath);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);
        return $regex;
    }

    /**
     * 组合路径，目录后的/不是必须
     *
     * @param string ...$args
     * @return string
     */
    public static function path(...$args)
    {
        static $dsds = DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR;
        $result = implode(DIRECTORY_SEPARATOR, $args);
        $offset = strpos($result, '://');
        if(false === $offset)
        {
            $offset = 0;
        }
        else
        {
            $offset += 3;
        }
        while(false !== ($position = strpos($result, $dsds, $offset)))
        {
            $result = substr_replace($result, DIRECTORY_SEPARATOR, $position, 2);
        }
        return $result;
    }

    /**
     * 根据文件打开句柄，读取文件所有内容
     * @param mixed $fp
     * @return string
     */
    public static function readAll($fp)
    {
        $data = '';
        while (!feof($fp)) {
            $data .= fread($fp, 4096);
        }
        return $data;
    }

    /**
     * 创建一个目录
     * author:lovefc
     * @param $dir 目录路径
     * @param $mode 目录的权限
     * @return false|true
     */
    public static function createDir($dir, $mode = 0775)
    {
        if (empty($dir))
        {
            return false;
        }
        if (!is_dir($dir))
        {
            if(@mkdir($dir, $mode, true))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
           return true;
        }
    }

    /**
     * 创建一个文件
     * author:lovefc
     * @param $dir 文件路径
     * @param $mode 文件的权限
     * @return false|true
     */
    public static function createFile($file, $mode = 0775)
    {
        if (empty($file))
        {
            return false;
        }
        if (is_file($file))
        {
            return true;
        }
        $dir = dirname($file);
        self::createDir($dir, $mode);
        $fh = @fopen($file, 'a');
        if ($fh)
        {
            fclose($fh);
            return true;
        }
        return false;
    }

    /**
     * 判断是否为空目录
     *
     * @param string $dir
     * @return boolean
     */
    public static function isEmptyDir($dir)
    {
        try {
            $handler = @opendir($dir);
            if(!$handler)
            {
                return true;
            }
            while ($file = readdir($handler))
            {
                if('.' !== $file && '..' !== $file)
                {
                    return false;
                }
            }
        } catch(\Throwable $th) {
            throw $th;
        } finally {
            if($handler)
            {
                closedir($handler);
            }
        }
        return true;
    }

    /**
     * 递归删除目录及目录中所有文件
     *
     * @param string $dir
     * @return boolean
     */
    public static function deleteDir($dir)
    {
        $dh = opendir($dir);
        while ($file = readdir($dh))
        {
            if('.' !== $file && '..' !== $file)
            {
                $fullpath = $dir . '/' . $file;
                if(is_dir($fullpath))
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
        if(rmdir($dir))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}