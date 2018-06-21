<?php

namespace Imi\Util;

abstract class File
{
    /**
     * 枚举文件
     * @param string $dirPath
     * @return \RecursiveIterator
     */
    public static function enum($dirPath)
    {
        if (!is_dir($dirPath)) {
            return;
        }
        $iterator = new \RecursiveDirectoryIterator($dirPath);
        $files = new \RecursiveIteratorIterator($iterator);
        foreach ($files as $file) {
            yield $file;
        }
    }

    /**
     * 枚举php文件
     * @param string $dirPath
     * @return \RegexIterator
     */
    public static function enumPHPFile($dirPath)
    {
        if (!is_dir($dirPath)) {
            return;
        }
        $directory = new \RecursiveDirectoryIterator($dirPath);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);
        foreach ($regex as $item) {
            yield $item[0];
        }
    }

    /**
     * 组合路径，目录后的/不是必须
     * @param string $path
     * @param string $fileName
     * @return string
     */
    public static function path($path, $fileName)
    {
        $result = $path;
        if (substr($path, -1, 1) !== DIRECTORY_SEPARATOR && (!isset($fileName[0]) || DIRECTORY_SEPARATOR !== $fileName[0])) {
            $result .= DIRECTORY_SEPARATOR;
        }
        return $result . $fileName;
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
     * 读取文件所有内容，优先使用协程，如果不支持则使用传统阻塞方式
     * @param string $fileName
     * @return string
     */
    public static function readFile($fileName)
    {
        if (Coroutine::isIn()) {
            return Coroutine::readFile($fileName);
        } else {
            return file_get_contents($fileName);
        }
    }

    /**
     * 写入文件，优先使用协程，如果不支持则使用传统阻塞方式
     * @param string $fileName
     * @param string $content
     * @param integer $flags
     * @return boolean
     */
    public static function writeFile($fileName, $content, $flags = 0)
    {
        if (Coroutine::isIn()) {
            return Coroutine::writeFile($fileName, $content, $flags);
        } else {
            return false !== file_put_contents($fileName, $content, $flags);
        }
    }
}