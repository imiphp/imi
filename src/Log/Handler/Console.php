<?php
namespace Imi\Log\Handler;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("ConsoleLog")
 */
class Console extends Base
{
    /**
     * 要限制输出的字符数量，为null则不限制
     * 
     * @var int
     */
    protected $length;
    
    /**
     * 真正的保存操作实现
     * @return void
     */
    protected function __save()
    {
        $length = $this->length;
        foreach($this->records as $record)
        {
            $content = $this->getLogString($record);
            if($length > 0)
            {
                $content = mb_substr($content, 0, $length) . '...';
            }
            fwrite(STDOUT, $content . PHP_EOL);
        }
    }
}