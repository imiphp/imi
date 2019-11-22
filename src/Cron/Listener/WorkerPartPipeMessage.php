<?php
namespace Imi\Cron\Listener;

use Imi\App;
use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Listener;
use Imi\Util\Process\ProcessType;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Server\Event\Param\PipeMessageEventParam;
use Imi\Server\Event\Listener\IPipeMessageEventListener;

/**
 * @Listener("IMI.MAIN_SERVER.PIPE_MESSAGE")
 */
class WorkerPartPipeMessage implements IPipeMessageEventListener
{
    /**
     * @Inject("CronManager")
     *
     * @var \Imi\Cron\CronManager
     */
    protected $cronManager;

    /**
     * @Inject("CronWorker")
     *
     * @var \Imi\Cron\CronWorker
     */
    protected $cronWorker;

    /**
     * 事件处理方法
     * @param PipeMessageEventParam $e
     * @return void
     */
    public function handle(PipeMessageEventParam $e)
    {
        if(ProcessType::WORKER !== App::get(ProcessAppContexts::PROCESS_TYPE))
        {
            return;
        }
        $data = json_decode($e->message, true);
        if('CronTask' !== ($data['action'] ?? null))
        {
            return;
        }
        $this->cronWorker->exec($data['id'], $data['data']);
    }

}
