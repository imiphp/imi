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
 * @RedisEntity(key="ttl-{id}-{name}", ttl=2)
 */
class TestRedisModel2 extends RedisModel
{
    /**
     * id.
     *
     * @Column(name="id")
     *
     * @var int
     */
    protected int $id;

    /**
     * 获取 id.
     *
     * @return int
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
     *
     * @var string
     */
    protected string $name;

    /**
     * 获取 name.
     *
     * @return string
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
     *
     * @var int
     */
    protected int $age;

    /**
     * 获取 age.
     *
     * @return int
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
