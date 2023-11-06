<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\ApiServer\Controller;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

#[Controller(prefix: '/rest')]
class RestController extends HttpController
{
    /**
     * query.
     *
     * @return array
     */
    #[Action]
    #[Route(url: '', method: ['GET'])]
    public function query()
    {
        return [
            'list' => [1, 2, 3],
        ];
    }

    /**
     * find.
     *
     * @return array
     */
    #[Action]
    #[Route(url: './{id}', method: ['GET'])]
    public function find(int $id)
    {
        return [
            'id' => $id,
        ];
    }

    /**
     * create.
     *
     * @return array
     */
    #[Action]
    #[Route(url: '', method: ['POST'])]
    public function create(string $name)
    {
        return [
            'operation' => 'create',
            'name'      => $name,
            'success'   => true,
        ];
    }

    /**
     * update.
     *
     * @return array
     */
    #[Action]
    #[Route(url: './{id}', method: ['PUT'])]
    public function update(int $id, string $name)
    {
        return [
            'id'        => $id,
            'name'      => $name,
            'operation' => 'update',
            'success'   => true,
        ];
    }

    /**
     * delete.
     *
     * @return array
     */
    #[Action]
    #[Route(url: './{id}', method: ['DELETE'])]
    public function delete(int $id)
    {
        return [
            'id'        => $id,
            'operation' => 'delete',
            'success'   => true,
        ];
    }
}
