<?php
/**
 * Created by Wennlong Li
 * User: wenlong
 * Date: 2018/9/28
 * Time: 下午1:50
 */

namespace Imi\Resource;


interface IResource
{
    /**
     * 获取hashcode
     * @return string
     */
    public function hashcode() : string ;

    /**
     * 打开
     * @param callable $callback
     * @return boolean
     */
    public function open($callback = null);

    /**
     * 关闭
     * @return void
     */
    public function close();

    /**
     * 重置资源，当资源被使用后重置一些默认的设置
     * @return void
     */
    public function reset();

}