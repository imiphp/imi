<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\RedisEntity;
use Imi\Model\RedisModel;

/**
 * Test.
 *
 * @Entity
 *
 * @RedisEntity(key="imi:redisModel:typeColumn", storage="hash_object")
 */
class TestRedisHashObjectColumnTypeModel extends RedisModel
{
    #[Column(type: 'json')]
    protected array $json = [];

    public function getJson(): array
    {
        return $this->json;
    }

    public function setJson(array $json): self
    {
        $this->json = $json;

        return $this;
    }

    #[Column(type: 'list')]
    protected array $list = [];

    public function getList(): array
    {
        return $this->list;
    }

    public function setList(array $list): self
    {
        $this->list = $list;

        return $this;
    }

    #[Column(type: 'set')]
    protected array $set = [];

    public function getSet(): array
    {
        return $this->set;
    }

    public function setSet(array $set): self
    {
        $this->set = $set;

        return $this;
    }
}
