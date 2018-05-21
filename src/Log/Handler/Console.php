<?php
namespace Imi\Log\Handler;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("ConsoleLog")
 */
class Console extends Base
{
	/**
	 * 真正的保存操作实现
	 * @return void
	 */
    protected function __save()
    {
        foreach($this->records as $record)
        {
            echo $this->getLogString($record), PHP_EOL;
        }
    }
}