<?php
namespace Imi\Process;

class TaskPool
{
    /**
     * 工作协程数量
     *
     * @var int
     */
    private $coCount;

    /**
     * 队列最大长度
     *
     * @var int
     */
    private $queueLength;

    /**
     * 任务队列
     *
     * @var \Swoole\Coroutine\Channel
     */
    private $taskQueue;

    /**
     * 是否正在运行
     *
     * @var boolean
     */
    private $running = false;

    /**
     * 任务类
     *
     * @var string
     */
    public $taskClass;

    /**
     * 任务参数类名
     *
     * @var string
     */
    public $taskParamClass;

    /**
     * 创建协程的函数
     * 
     * 有些框架自定义了新建协程的方法，用于控制上下文生命周期，所以加了这个属性用于兼容
     *
     * @var callable
     */
    public $createCoCallable = 'go';

    /**
     * 构造方法
     *
     * @param int $coCount 工作协程数量
     * @param int $queueLength 队列最大长度
     * @param string $taskClass 任务类
     * @param string $taskParamClass 任务参数类名
     */
    public function __construct($coCount, $queueLength, $taskClass, $taskParamClass = TaskParam::class)
    {
        $this->coCount = $coCount;
        $this->queueLength = $queueLength;
        $this->taskClass = $taskClass;
        $this->taskParamClass = $taskParamClass;
    }

    /**
     * 运行协程池
     *
     * @return void
     */
    public function run()
    {
        if($this->taskQueue)
        {
            $this->taskQueue->close();
        }
        $this->taskQueue = new \Swoole\Coroutine\Channel($this->queueLength);
        $this->running = true;
        for($i = 0; $i < $this->coCount; ++$i)
        {
            go(function() use($i){
                $this->task($i);
            });
        }
    }

    /**
     * 停止协程池
     * 
     * 不会中断正在执行的任务
     * 
     * 等待当前任务全部执行完后，才算全部停止
     *
     * @return void
     */
    public function stop()
    {
        $this->running = false;
        $this->taskQueue->close();
        $this->taskQueue = null;
    }

    /**
     * 增加任务，并挂起协程等待返回任务执行结果
     *
     * @param mixed $data
     * @return mixed
     */
    public function addTask($data)
    {
        $channel = new \Swoole\Coroutine\Channel(1);
        try {
            if($this->taskQueue->push([
                'data'      =>  $data,
                'channel'   =>  $channel,
            ]))
            {
                $result = $channel->pop();
                if(false === $result)
                {
                    return false;
                }
                else
                {
                    return $result['result'];
                }
            }
            else
            {
                throw new \RuntimeException(sprintf('AddTask failed! Channel errCode = %s', $this->taskQueue->errCode));
            }
        } catch(\Throwable $th) {
            throw $th;
        } finally {
            $channel->close();
        }
    }

    /**
     * 增加任务，异步回调
     * 
     * 执行完成后新建一个协程调用 $callback，为 null 不执行回调
     *
     * @param mixed $data
     * @param callable $callback
     * @return boolean
     */
    public function addTaskAsync($data, $callback = null)
    {
        return $this->taskQueue->push([
            'data'      =>  $data,
            'callback'  =>  $callback,
        ]);
    }

    /**
     * 任务监听
     *
     * @param int $index
     * @return void
     */
    protected function task($index)
    {
        $taskObject = new $this->taskClass;
        do {
            $task = $this->taskQueue->pop();
            if(false !== $task)
            {
                try {
                    $param = new $this->taskParamClass($index, $task['data']);
                    $result = $taskObject->run($param);
                } catch(\Throwable $th) {
                    throw $th;
                } finally {
                    if(isset($task['channel']))
                    {
                        $task['channel']->push([
                            'param'     =>  $param,
                            'result'    =>  $result,
                        ]);
                    }
                    else if(isset($task['callback']))
                    {
                        ($this->createCoCallable)(function() use($task, $param, $result){
                            $task['callback']($param, $result);
                        });
                    }
                }
            }
        } while($this->running);
    }

}