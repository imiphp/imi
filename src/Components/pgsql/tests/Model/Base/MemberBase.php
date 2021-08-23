<?php
declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Pgsql\Model\PgModel as Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;

/**
 * tb_member 基类
 * @Entity
 * @Table(name="tb_member", id={"id"})
 * @property int|null $id 
 * @property string|null $username 用户名
 * @property string|null $password 密码
 */
abstract class MemberBase extends Model
{
    /**
     * id
     * @Column(name="id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=true, ndims=0)
     * @var int|null
     */
    protected ?int $id = null;

    /**
     * 获取 id
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * 赋值 id
     * @param int|null $id id
     * @return static
     */
    public function setId(?int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * 用户名
     * username
     * @Column(name="username", type="varchar", length=0, accuracy=32, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
     * @var string|null
     */
    protected ?string $username = null;

    /**
     * 获取 username - 用户名
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * 赋值 username - 用户名
     * @param string|null $username username
     * @return static
     */
    public function setUsername(?string $username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * 密码
     * password
     * @Column(name="password", type="varchar", length=0, accuracy=255, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
     * @var string|null
     */
    protected ?string $password = null;

    /**
     * 获取 password - 密码
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * 赋值 password - 密码
     * @param string|null $password password
     * @return static
     */
    public function setPassword(?string $password)
    {
        $this->password = $password;
        return $this;
    }

}
