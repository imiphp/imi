<?php

namespace Imi\Test\HttpServer\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Task\TaskInfo;
use Imi\Task\TaskManager;
use Imi\Task\TaskParam;
use Imi\Test\HttpServer\Task\TestTask;

/**
 * @Controller("/task/")
 */
class TaskController extends HttpController
{
    /**
     * @Action
     *
     * @return void
     */
    public function test()
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
