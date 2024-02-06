<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

if (\PHP_VERSION_ID >= 80100 && !class_exists(TestEnumRedisModel::class, false))
{
    eval(<<<'PHP'
    namespace Imi\Test\Component\Model;

    use Imi\Model\Annotation\Column;
    use Imi\Model\Annotation\Entity;
    use Imi\Model\Annotation\RedisEntity;
    use Imi\Model\RedisModel;
    use Imi\Test\Component\Enum\TestEnumBean;
    use Imi\Test\Component\Enum\TestEnumBeanBacked;

    /**
     * Test.
     *
     * @property int    $id
     * @property string $name
     * @property int    $age
     */
    #[
        Entity,
        RedisEntity(key: 'TestEnumRedisModel-{id}-{name}')
    ]
    class TestEnumRedisModel extends RedisModel
    {
        /**
         * id.
         */
        #[Column]
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
         */
        #[Column]
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

        #[Column]
        protected ?TestEnumBean $enum = null;

        public function getEnum(): TestEnumBean
        {
            return $this->enum;
        }

        public function setEnum(TestEnumBean $enum): self
        {
            $this->enum = $enum;

            return $this;
        }

        #[Column]
        protected ?TestEnumBeanBacked $enumBacked = null;

        public function getEnumBacked(): TestEnumBeanBacked
        {
            return $this->enumBacked;
        }

        public function setEnumBacked(TestEnumBeanBacked $enumBacked): self
        {
            $this->enumBacked = $enumBacked;

            return $this;
        }
    }
    PHP);
}
