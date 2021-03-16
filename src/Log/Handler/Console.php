<?php

declare(strict_types=1);

namespace Imi\Log\Handler;

use Imi\Bean\Annotation\Bean;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @Bean("ConsoleLog")
 */
class Console extends Base
{
    /**
     * 要限制输出的字符数量，为null则不限制.
     */
    protected ?int $length = null;

    /**
     * 真正的保存操作实现.
     */
    protected function __save(array $records): void
    {
        $output = new ConsoleOutput();
        $length = $this->length;
        foreach ($records as $record)
        {
            $content = $this->getLogString($record);
            if ($length > 0)
            {
                $content = mb_substr($content, 0, $length) . '...';
            }
            $output->writeln($content);
        }
    }
}
