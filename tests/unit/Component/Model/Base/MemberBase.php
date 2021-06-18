<?php

namespace Imi\Test\Component\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * tb_member 基类.
 *
 * @Entity
 * @Table(name="tb_member", id={"id"})
 * @DDL("CREATE TABLE `tb_member` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `username` varchar(32) NOT NULL COMMENT '用户名',   `password` varchar(255) NOT NULL COMMENT '密码',   PRIMARY KEY (`id`) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT")
 *
 * @property int    $id
 * @property string $username 用户名
 * @property string $password 密码
 */
abstract class MemberBase extends Model
{
    /**
     * id.
     *
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
     *
     * @var int
     */
    protected $id;

    /**
     * 获取 id.
     *
     * @return int
     */
    public function getId()
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
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * 用户名
     * username.
     *
     * @Column(name="username", type="varchar", length=32, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $username;

    /**
     * 获取 username - 用户名.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * 赋值 username - 用户名.
     *
     * @param string $username username
     *
     * @return static
     */
    public function setUsername($username)
    {
        if (isset($username[32]))
        {
            throw new \InvalidArgumentException('The maximum length of $username is 32');
        }
        $this->username = $username;

        return $this;
    }

    /**
     * 密码
     * password.
     *
     * @Column(name="password", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $password;

    /**
     * 获取 password - 密码
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * 赋值 password - 密码
     *
     * @param string $password password
     *
     * @return static
     */
    public function setPassword($password)
    {
        if (isset($password[255]))
        {
            throw new \InvalidArgumentException('The maximum length of $password is 255');
        }
        $this->password = $password;

        return $this;
    }
}
