<?php

declare(strict_types=1);

namespace Imi\Swoole\Pool;

use Imi\Swoole\Util\Co\ChannelContainer;

/**
 * 池子中存储的对象
 */
class PoolItem extends \Imi\Pool\PoolItem
{
    public function __destruct()
    {
        $id = (string) spl_object_id($this);
        if (ChannelContainer::hasChannel($id))
        {
            ChannelContainer::removeChannel($id);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function lock(float $timeout = 0): bool
    {
        if ($this->isFree || ChannelContainer::pop((string) spl_object_id($this), $timeout))
        {
            ++$this->usageCount;
            $this->isFree = false;
            $this->lastUseTime = microtime(true);

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function release(): void
    {
        parent::release();
        $id = (string) spl_object_id($this);
        if (ChannelContainer::hasChannel($id))
        {
            ChannelContainer::push($id, true);
        }
    }
}
