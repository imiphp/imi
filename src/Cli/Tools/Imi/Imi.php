<?php

declare(strict_types=1);

namespace Imi\Cli\Tools\Imi;

use Imi\Bean\Scanner;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\ArgType;
use Imi\Cli\Contract\BaseCommand;
use Imi\Pool\Annotation\PoolClean;
use Imi\Util\Imi as ImiUtil;
use Imi\Util\Text;

/**
 * @Command("imi")
 */
class Imi extends BaseCommand
{
    /**
     * 构建框架预加载缓存.
     *
     * @CommandAction("buildImiRuntime")
     * @Option(name="file", type=ArgType::STRING, default=null, comments="可以指定生成到目标文件")
     *
     * @return void
     */
    public function buildImiRuntime(?string $file): void
    {
        if (null === $file)
        {
            $file = \Imi\Util\Imi::getRuntimePath('imi-runtime.cache');
        }
        ImiUtil::buildRuntime($file);
        $this->output->writeln('<info>Build imi runtime complete</info>');
    }

    /**
     * 清除框架预加载缓存.
     *
     * @CommandAction("clearImiRuntime")
     *
     * @return void
     */
    public function clearImiRuntime(): void
    {
        $file = ImiUtil::getRuntimePath('imi-runtime.cache');
        if (is_file($file))
        {
            unlink($file);
            $this->output->writeln('<info>Clear imi runtime complete</info>');
        }
        else
        {
            $this->output->writeln('<error>Imi runtime does not exists</error>');
        }
    }

    /**
     * 构建项目预加载缓存.
     *
     * @PoolClean
     *
     * @CommandAction(name="buildRuntime", co=false)
     *
     * @Option(name="changedFilesFile", type=ArgType::STRING, default=null, comments="保存改变的文件列表的文件，一行一个")
     * @Option(name="confirm", type=ArgType::BOOL, default=false, comments="是否等待输入y后再构建")
     *
     * @return void
     */
    public function buildRuntime(?string $changedFilesFile, bool $confirm): void
    {
        if ($confirm)
        {
            $input = fread(\STDIN, 1);
            if ('y' !== $input)
            {
                exit;
            }
        }

        if (!Text::isEmpty($changedFilesFile) && \Imi\Util\Imi::loadRuntimeInfo(ImiUtil::getRuntimePath('runtime.cache')))
        {
            $files = explode("\n", file_get_contents($changedFilesFile));
            ImiUtil::incrUpdateRuntime($files);
        }
        else
        {
            Scanner::scanVendor();
            Scanner::scanApp();
        }
        ImiUtil::buildRuntime();
        echo 'Build app runtime complete' . \PHP_EOL;
    }

    /**
     * 清除项目预加载缓存.
     *
     * @CommandAction("clearRuntime")
     *
     * @return void
     */
    public function clearRuntime(): void
    {
        $file = \Imi\Util\Imi::getRuntimePath('runtime.cache');
        if (is_file($file))
        {
            unlink($file);
            $this->output->writeln('<info>Clear app runtime complete</info>');
        }
        else
        {
            $this->output->writeln('<error>App runtime does not exists</error>');
        }
    }
}
