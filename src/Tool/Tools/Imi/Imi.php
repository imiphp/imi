<?php

namespace Imi\Tool\Tools\Imi;

use Imi\App;
use Imi\Bean\Annotation;
use Imi\Pool\Annotation\PoolClean;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Operation;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\ArgType;
use Imi\Util\File;
use Imi\Util\Imi as ImiUtil;
use Imi\Util\Text;

/**
 * @Tool("imi")
 */
class Imi
{
    /**
     * 构建框架预加载缓存.
     *
     * @Operation("buildImiRuntime")
     * @Arg(name="file", type=ArgType::STRING, default=null, comments="可以指定生成到目标文件")
     *
     * @return void
     */
    public function buildImiRuntime($file)
    {
        if (null === $file)
        {
            $file = \Imi\Util\Imi::getRuntimePath('imi-runtime.cache');
        }
        ImiUtil::buildRuntime($file);
        echo 'Build imi runtime complete', \PHP_EOL;
    }

    /**
     * 清除框架预加载缓存.
     *
     * @Operation("clearImiRuntime")
     *
     * @return void
     */
    public function clearImiRuntime()
    {
        $file = \Imi\Util\Imi::getRuntimePath('imi-runtime.cache');
        if (is_file($file))
        {
            unlink($file);
            echo 'Clear imi runtime complete', \PHP_EOL;
        }
        else
        {
            echo 'Imi runtime does not exists', \PHP_EOL;
        }
    }

    /**
     * 构建项目预加载缓存.
     *
     * @PoolClean
     *
     * @Operation(name="buildRuntime", co=false)
     *
     * @Arg(name="format", type=ArgType::STRING, default="", comments="返回数据格式，可选：json或其他。json格式框架启动、热重启构建缓存需要。")
     * @Arg(name="changedFilesFile", type=ArgType::STRING, default=null, comments="保存改变的文件列表的文件，一行一个")
     * @Arg(name="confirm", type=ArgType::BOOL, default=false, comments="是否等待输入y后再构建")
     * @Arg(name="sock", type=ArgType::STRING, default=false, comments="如果传了 sock 则走 Unix Socket 通讯")
     *
     * @return void
     */
    public function buildRuntime($format, $changedFilesFile, $confirm, $sock)
    {
        $socket = null;
        $success = false;
        ob_start();
        register_shutdown_function(function () use ($format, &$socket, &$success) {
            $result = ob_get_clean();
            if ($success)
            {
                $result = 'Build app runtime complete' . \PHP_EOL;
            }
            if ($result)
            {
                if ('json' === $format)
                {
                    echo json_encode($result);
                }
                else
                {
                    echo $result;
                }
            }
            if ($socket)
            {
                $data = [
                    'action'    => 'buildRuntimeResult',
                    'result'    => $result,
                ];
                $content = serialize($data);
                $content = pack('N', \strlen($content)) . $content;
                fwrite($socket, $content);
                fclose($socket);
            }
        });

        if ($sock)
        {
            $socket = stream_socket_client('unix://' . $sock, $errno, $errstr, 10);
            if (false === $socket)
            {
                return false;
            }
            stream_set_timeout($socket, 60);
            do
            {
                $meta = fread($socket, 4);
                if ('' === $meta)
                {
                    if (feof($socket))
                    {
                        return;
                    }
                    continue;
                }
                if (false === $meta)
                {
                    return;
                }
                $length = unpack('N', $meta)[1];
                $data = fread($socket, $length);
                if (false === $data || !isset($data[$length - 1]))
                {
                    return;
                }
                $result = unserialize($data);
                if ('buildRuntime' === $result['action'])
                {
                    break;
                }
            } while (true);
        }
        elseif ($confirm)
        {
            $input = fread(\STDIN, 1);
            if ('y' !== $input)
            {
                return;
            }
        }

        if (!Text::isEmpty($changedFilesFile) && App::loadRuntimeInfo(ImiUtil::getRuntimePath('runtime.cache')))
        {
            $files = explode("\n", file_get_contents($changedFilesFile));
            ImiUtil::incrUpdateRuntime($files);
        }
        else
        {
            // 加载服务器注解
            Annotation::getInstance()->init(\Imi\Main\Helper::getAppMains());
        }
        ImiUtil::buildRuntime();
        $success = true;
    }

    /**
     * 清除项目预加载缓存.
     *
     * @Operation("clearRuntime")
     *
     * @return void
     */
    public function clearRuntime()
    {
        $file = \Imi\Util\Imi::getRuntimePath('runtime.cache');
        if (is_file($file))
        {
            unlink($file);
            echo 'Clear app runtime complete', \PHP_EOL;
        }
        else
        {
            echo 'App runtime does not exists', \PHP_EOL;
        }
    }
}
