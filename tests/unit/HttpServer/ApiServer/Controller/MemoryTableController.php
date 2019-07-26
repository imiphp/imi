<?php
namespace Imi\Test\HttpServer\ApiServer\Controller;

use Imi\Util\MemoryTableManager;
use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;

/**
 * @Controller("/memoryTable/")
 */
class MemoryTableController extends HttpController
{
    /**
     * 设置行的数据
     * @Action
     * @return void
     */
    public function setAndGet()
    {
        $key = '1';
        $row = [
            'name'  =>  'imi',
        ];
        return [
            'setResult' =>  MemoryTableManager::set('t1', $key, $row),
            'getField'  =>  MemoryTableManager::get('t1', $key, 'name'),
            'getRow'    =>  MemoryTableManager::get('t1', $key),
        ];
    }

    /**
     * 删除行的数据
     * @Action
     * @return void
     */
    public function del()
    {
        $key = '2';
        $row = [
            'name'  =>  'yurun',
        ];
        return [
            'setResult' =>  MemoryTableManager::set('t1', $key, $row),
            'getRow1'   =>  MemoryTableManager::get('t1', $key),
            'delResult' =>  MemoryTableManager::del('t1', $key),
            'getRow2'   =>  MemoryTableManager::get('t1', $key),
        ];
    }

    /**
     * 行数据是否存在
     * @Action
     * @return void
     */
    public function exist()
    {
        $key = '2';
        $row = [
            'name'  =>  'yurun',
        ];
        return [
            'existResult1'  =>  MemoryTableManager::exist('t1', $key),
            'setResult'     =>  MemoryTableManager::set('t1', $key, $row),
            'existResult2'  =>  MemoryTableManager::exist('t1', $key),
        ];
    }

    /**
     * 原子自增
     * @Action
     * @return void
     */
    public function incr()
    {
        $key = '3';
        $row = [
            'name'      =>  'yurun',
            'quantity'  =>  0,
        ];
        return [
            'setResult'     =>  MemoryTableManager::set('t1', $key, $row),
            'incrResult'    =>  MemoryTableManager::incr('t1', $key, 'quantity', 1),
            'getQuantity'   =>  MemoryTableManager::get('t1', $key, 'quantity'),
        ];
    }

    /**
     * 原子自减
     * @Action
     * @return void
     */
    public function decr()
    {
        $key = '4';
        $row = [
            'name'      =>  'yurun',
            'quantity'  =>  0,
        ];
        return [
            'setResult'     =>  MemoryTableManager::set('t1', $key, $row),
            'decrResult'    =>  MemoryTableManager::decr('t1', $key, 'quantity', 1),
            'getQuantity'   =>  MemoryTableManager::get('t1', $key, 'quantity'),
        ];
    }

    /**
     * 获取表行数
     * @Action
     * @return void
     */
    public function count()
    {
        return [
            'count'    =>  MemoryTableManager::count('t1'),
        ];
    }

    /**
     * 设置行的数据
     * @Action
     * @return void
     */
    public function lockCallableSetAndGet()
    {
        $result = null;
        MemoryTableManager::lock('t1', function() use(&$result) {
            $key = '1';
            $row = [
                'name'  =>  'imi',
            ];
            $result = [
                'setResult' =>  MemoryTableManager::set('t1', $key, $row),
                'getField'  =>  MemoryTableManager::get('t1', $key, 'name'),
                'getRow'    =>  MemoryTableManager::get('t1', $key),
            ];
        });
        return $result;
    }

    /**
     * 设置行的数据
     * @Action
     * @return void
     */
    public function lockSetAndGet()
    {
        MemoryTableManager::lock('t1');
        $result = null;
        try {
            $key = '1';
            $row = [
                'name'  =>  'imi',
            ];
            $result = [
                'setResult' =>  MemoryTableManager::set('t1', $key, $row),
                'getField'  =>  MemoryTableManager::get('t1', $key, 'name'),
                'getRow'    =>  MemoryTableManager::get('t1', $key),
            ];
        } catch(\Throwable $th) {
            throw $th;
        } finally {
            MemoryTableManager::unlock('t1');
        }
        return $result;
    }
}
