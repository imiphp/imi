<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_member 基类.
 *
 * @Entity(camel=true, bean=true, incrUpdate=false)
 *
 * @Table(name="tb_member", usePrefix=false, id={"id"}, dbPoolName=null)
 *
 * @property int|null    $id
 * @property string|null $username 用户名
 * @property string|null $password 密码
 */
abstract class MemberBase extends Model
{
    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEY = 'id';

    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEYS = ['id'];

    /**
     * id.
     *
     * @Column(name="id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true, ndims=0, virtual=false)
     */
    protected ?int $id = null;

    /**
     * 获取 id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * 赋值 id.
     *
     * @param int|null $id id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->id = null === $id ? null : (int) $id;

        return $this;
    }

    /**
     * 用户名.
     * username.
     *
     * @Column(name="username", type="varchar", length=32, accuracy=0, nullable=false, default="''::character varying", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
    protected ?string $username = '';

    /**
     * 获取 username - 用户名.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * 赋值 username - 用户名.
     *
     * @param string|null $username username
     *
     * @return static
     */
    public function setUsername($username)
    {
        if (\is_string($username) && mb_strlen($username) > 32)
        {
            throw new \InvalidArgumentException('The maximum length of $username is 32');
        }
        $this->username = null === $username ? null : $username;

        return $this;
    }

    /**
     * 密码.
     * password.
     *
     * @Column(name="password", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
    protected ?string $password = null;

    /**
     * 获取 password - 密码.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * 赋值 password - 密码.
     *
     * @param string|null $password password
     *
     * @return static
     */
    public function setPassword($password)
    {
        if (\is_string($password) && mb_strlen($password) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $password is 255');
        }
        $this->password = null === $password ? null : $password;

        return $this;
    }
}
