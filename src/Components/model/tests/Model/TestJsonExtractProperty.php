<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\ExtractProperty;
use Imi\Model\Test\Model\Base\TestJsonBase;

/**
 * tb_test_json.
 *
 * @property \Imi\Util\LazyArrayObject|array $jsonData json数据
 * @property int|null                        $userId
 * @property int|null                        $userId2
 */
#[Inherit]
#[Entity(camel: false)]
class TestJsonExtractProperty extends TestJsonBase
{
    #[Inherit]
    #[ExtractProperty(fieldName: 'ex.userId')]
    #[ExtractProperty(fieldName: 'ex.userId', alias: 'userId2')]
    protected $jsonData = null;

    #[Column(virtual: true)]
    protected ?int $userId = null;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    #[Column(virtual: true)]
    protected ?int $userId2 = null;

    public function getUserId2(): ?int
    {
        return $this->userId2;
    }

    public function setUserId2(?int $userId2): self
    {
        $this->userId2 = $userId2;

        return $this;
    }
}
