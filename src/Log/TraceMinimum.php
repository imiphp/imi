<?php
namespace Imi\Log;

use Imi\Util\Imi;


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
     * 是否正在查找匿名类真正的调用追踪
     *
     * @var boolean
     */
    private $isFindingAnonymousRealCall = false;
    
    /**
     * 匿名类调用的真实类名
     */
    private $anonymousRealCallFileName = '';

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
            if($this->isFindingAnonymousRealCall)
            {
                if($this->isKeep($traceLine))
                {
                    $this->isFindingAnonymousRealCall = false;
                }
                else
                {
                    unset($trace[$this->eachIndex]);
                }
            }
            else
            {
                if(isset($traceLine['class']) && 0 === strpos($traceLine['class'], 'class@anonymous'))
                {
                    $this->anonymousRealCallFileName = $this->getAnonymousRealCallFileName();
                    if(null === $this->anonymousRealCallFileName)
                    {
                        continue;
                    }
                    $this->isFindingAnonymousRealCall = true;
                    unset($trace[$this->eachIndex]);
                    --$this->eachIndex;
                    unset($trace[$this->eachIndex]);
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
        return isset($this->trace[$this->eachIndex]['file']) && $this->trace[$this->eachIndex]['file'] === $this->anonymousRealCallFileName;
    }

    /**
     * 获取匿名类调用的真实文件名
     *
     * @return string
     */
    private function getAnonymousRealCallFileName()
    {
        $nextTraceLine = $this->trace[$this->eachIndex - 1] ?? null;
        if(null === $nextTraceLine)
        {
            return null;
        }
        return $nextTraceLine['file'] ?? null;
    }

    /**
     * 根据文件名获取trace行的类名
     *
     * @param string $traceLine
     * @return void
     */
    private function getCallClassName($traceLine)
    {
        if(!isset($traceLine['file']))
        {
            return null;
        }
        $beanCachePath = Imi::getBeanClassCachePath();
        $pattern = '/' . preg_quote($beanCachePath, '/') . '\/([0-9]+\/)?(\S+)/';
        if(preg_match($pattern, $traceLine['file'], $matches) > 0)
        {
            $pathInfo = pathinfo($matches[2]);
            return str_replace(DIRECTORY_SEPARATOR, '\\', $pathInfo['dirname']) . '\\' . $pathInfo['filename'];
        }
        else
        {
            return null;
        }
    }
}