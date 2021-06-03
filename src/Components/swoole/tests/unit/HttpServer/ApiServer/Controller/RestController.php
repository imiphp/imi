<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\ApiServer\Controller;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

/**
 * @Controller("/rest")
 */
class RestController extends HttpController
{
    /**
     * query.
     *
     * @Action
     * @Route(url="", method={"GET"})
     *
     * @return array
     */
    public function query()
    {
        return [
            'list' => [1, 2, 3],
        ];
    }

    /**
     * find.
     *
     * @Action
     * @Route(url="./{id}", method={"GET"})
     *
     * @param int $id
     *
     * @return array
     */
    public function find($id)
    {
        return [
            'id' => $id,
        ];
    }

    /**
     * create.
     *
     * @Action
     * @Route(url="", method={"POST"})
     *
     * @param string $name
     *
     * @return array
     */
    public function create($name)
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
     * @Action
     * @Route(url="./{id}", method={"PUT"})
     *
     * @param int    $id
     * @param string $name
     *
     * @return array
     */
    public function update($id, $name)
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
     * @Action
     * @Route(url="./{id}", method={"DELETE"})
     *
     * @param int $id
     *
     * @return array
     */
    public function delete($id)
    {
        return [
            'id'        => $id,
            'operation' => 'delete',
            'success'   => true,
        ];
    }
}
