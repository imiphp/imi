<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\ApiServer\Controller;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Swoole\Task\TaskInfo;
use Imi\Swoole\Task\TaskManager;
use Imi\Swoole\Task\TaskParam;
use Imi\Swoole\Test\HttpServer\Task\TestTask;

/**
 * @Controller("/task/")
 */
class TaskController extends HttpController
{
    /**
     * @Action
     */
    public function test(): array
    {
        $data = [
            'time'  => strtotime('2019-06-21'),
        ];
        $taskInfo = new TaskInfo(new TestTask(), new TaskParam($data));
        $tasks = [
            new TaskInfo(new TestTask(), new TaskParam([
                'time'  => strtotime('2018-06-21'),
            ])),
            new TaskInfo(new TestTask(), new TaskParam($data)),
        ];
        $result = [
            'post'      => TaskManager::post($taskInfo),
            'nPost'     => TaskManager::nPost('Test1', $data),
            'nPostWait' => TaskManager::nPostWait('Test1', $data, 10),
            'postWait'  => TaskManager::postWait($taskInfo, 10),
            'postCo'    => TaskManager::postCo($tasks, 10),
        ];

        return $result;
    }
}
