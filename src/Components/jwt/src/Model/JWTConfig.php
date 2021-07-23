<?php

namespace Imi\JWT\Model;

use Imi\Util\Traits\TDataToProperty;
use Lcobucci\JWT\Signer;

/**
 * JWT 配置.
 */
class JWTConfig
{
    use TDataToProperty;

    /**
     * 签名者.
     *
     * @var string
     */
    private $signer = 'Hmac';

    /**
     * 算法.
     *
     * @var string
     */
    private $algo = 'Sha256';

    /**
     * 自定义数据字段名.
     *
     * @var string
     */
    private $dataName = 'data';

    /**
     * 接收.
     *
     * @var string|null
     */
    private $audience;

    /**
     * 主题.
     *
     * @var string|null
     */
    private $subject;

    /**
     * 超时秒数.
     *
     * @var int|null
     */
    private $expires;

    /**
     * 发行人.
     *
     * @var string|null
     */
    private $issuer;

    /**
     * 实际日期必须大于等于本值
     *
     * @var int
     */
    private $notBefore = 0;

    /**
     * JWT 发出时间
     * 设为 true 则为当前时间
     * 设为 false 不设置
     * 其它值则直接写入.
     *
     * @var bool|mixed
     */
    private $issuedAt = true;

    /**
     * Token id.
     *
     * @var string|null
     */
    private $id;

    /**
     * 头.
     *
     * @var array
     */
    private $headers = [];

    /**
     * 自定义获取 token 回调.
     *
     * @var callable|null
     */
    private $tokenHandler;

    /**
     * 私钥.
     *
     * @var string
     */
    private $privateKey;

    /**
     * 公钥.
     *
     * @var string
     */
    private $publicKey;

    /**
     * Get 签名者.
     *
     * @return string
     */
    public function getSigner(): string
    {
        return $this->signer;
    }

    /**
     * Get 算法.
     *
     * @return string
     */
    public function getAlgo(): string
    {
        return $this->algo;
    }

    /**
     * Get 超时秒数.
     *
     * @return int|null
     */
    public function getExpires(): ?int
    {
        return $this->expires;
    }

    /**
     * Get 发行人.
     *
     * @return string|null
     */
    public function getIssuer(): ?string
    {
        return $this->issuer;
    }

    /**
     * Get 头.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get 自定义获取 token 回调.
     *
     * @return callable|null
     */
    public function getTokenHandler(): ?callable
    {
        return $this->tokenHandler;
    }

    /**
     * Get 私钥.
     *
     * @return string|null
     */
    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    /**
     * Get 公钥.
     *
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * Get 接收.
     *
     * @return string|null
     */
    public function getAudience(): ?string
    {
        return $this->audience;
    }

    /**
     * Get 主题.
     *
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * Get 其它值则直接写入.
     *
     * @return bool|mixed
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * Get token id.
     *
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Get 自定义数据字段名.
     *
     * @return string
     */
    public function getDataName(): string
    {
        return $this->dataName;
    }

    /**
     * Get 实际日期必须大于等于本值
     *
     * @return int
     */
    public function getNotBefore(): int
    {
        return $this->notBefore;
    }

    /**
     * 获取签名者对象
     *
     * @return \Lcobucci\JWT\Signer
     */
    public function getSignerInstance(): Signer
    {
        if (class_exists($this->signer))
        {
            $className = $this->signer;
        }
        else
        {
            $className = 'Lcobucci\JWT\Signer\\' . $this->signer . '\\' . $this->algo;
        }

        return new $className();
    }
}
