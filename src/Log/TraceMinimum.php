<?php
namespace Imi\Log;

use Imi\Util\Imi;
use Imi\Bean\IBean;
use Imi\Bean\BeanFactory;


class TraceMinimum
{
    /**
     * trace
     *
     * @var array
     */
    private $trace;

    /**
     * 遍历索引
     *
     * @var int
     */
    private $eachIndex;

    /**
     * 是否正在查找真正的调用追踪
     *
     * @var boolean
     */
    private $isFindingBeanRealCall = false;
    
    /**
     * 调用的真实类名
     */
    private $beanRealCallRealClassName = '';

    /**
     * trace精简处理
     *
     * @param array $trace
     * @return array
     */
    public function parse(array $trace)
    {
        $this->trace = &$trace;
        $traceCount = count($trace);
        
        for($this->eachIndex = $traceCount - 1; $this->eachIndex >= 0; --$this->eachIndex)
        {
            $traceLine = $trace[$this->eachIndex];
            if($this->isFindingBeanRealCall)
            {
                if($this->isKeep($traceLine))
                {
                    $this->isFindingBeanRealCall = false;
                }
                else
                {
                    unset($trace[$this->eachIndex]);
                }
            }
            else
            {
                if(isset($traceLine['class']))
                {
                    $ref = new \ReflectionClass($traceLine['class']);
                    if($ref->implementsInterface(IBean::class))
                    {
                        $this->beanRealCallRealClassName = $this->getBeanRealCallRealClassName();
                        if(null === $this->beanRealCallRealClassName)
                        {
                            continue;
                        }
                        $this->isFindingBeanRealCall = true;
                        unset($trace[$this->eachIndex]);
                        --$this->eachIndex;
                        unset($trace[$this->eachIndex]);
                    }
                }
            }
        }
        return array_values($trace);
    }

    /**
     * 是否保留该行追踪
     *
     * @return boolean
     */
    private function isKeep()
    {
        return isset($this->trace[$this->eachIndex]['class']) && $this->trace[$this->eachIndex]['class'] === $this->beanRealCallRealClassName;
    }

    /**
     * 获取调用的真实文件名
     *
     * @return string
     */
    private function getBeanRealCallRealClassName()
    {
        $nextTraceLine = $this->trace[$this->eachIndex - 1] ?? null;
        if(null === $nextTraceLine)
        {
            return null;
        }
        return $nextTraceLine['class'] ? BeanFactory::getObjectClass($nextTraceLine['class']) : null;
    }

}