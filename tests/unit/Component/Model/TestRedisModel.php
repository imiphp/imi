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
 * @RedisEntity(key="{id}-{name}")
 *
 * @property int    $id
 * @property string $name
 * @property int    $age
 */
class TestRedisModel extends RedisModel
{
    /**
     * id.
     *
     * @Column(name="id")
     */
    protected int $id;

    /**
     * 获取 id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * 赋值 id.
     *
     * @param int $id id
     *
     * @return static
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * name.
     *
     * @Column(name="name")
     */
    protected string $name;

    /**
     * 获取 name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 赋值 name.
     *
     * @param string $name name
     *
     * @return static
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * age.
     *
     * @Column(name="age")
     */
    protected int $age;

    /**
     * 获取 age.
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * 赋值 age.
     *
     * @param int $age age
     *
     * @return static
     */
    public function setAge($age): self
    {
        $this->age = $age;

        return $this;
    }
}
