<?php

declare(strict_types=1);

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
     */
    private string $signer = 'Hmac';

    /**
     * 算法.
     */
    private string $algo = 'Sha256';

    /**
     * 自定义数据字段名.
     */
    private string $dataName = 'data';

    /**
     * 接收.
     */
    private ?string $audience = null;

    /**
     * 主题.
     */
    private ?string $subject = null;

    /**
     * 超时秒数.
     */
    private ?int $expires = null;

    /**
     * 发行人.
     */
    private ?string $issuer = null;

    /**
     * 实际日期必须大于等于本值
     */
    private int $notBefore = 0;

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
     */
    private ?string $id = null;

    /**
     * 头.
     */
    private array $headers = [];

    /**
     * 自定义获取 token 回调.
     *
     * @var callable|null
     */
    private $tokenHandler = null;

    /**
     * 私钥.
     */
    private string $privateKey = '';

    /**
     * 公钥.
     */
    private string $publicKey = '';

    /**
     * Get 签名者.
     */
    public function getSigner(): string
    {
        return $this->signer;
    }

    /**
     * Get 算法.
     */
    public function getAlgo(): string
    {
        return $this->algo;
    }

    /**
     * Get 超时秒数.
     */
    public function getExpires(): ?int
    {
        return $this->expires;
    }

    /**
     * Get 发行人.
     */
    public function getIssuer(): ?string
    {
        return $this->issuer;
    }

    /**
     * Get 头.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get 自定义获取 token 回调.
     */
    public function getTokenHandler(): ?callable
    {
        return $this->tokenHandler;
    }

    /**
     * Get 私钥.
     */
    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    /**
     * Get 公钥.
     */
    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * Get 接收.
     */
    public function getAudience(): ?string
    {
        return $this->audience;
    }

    /**
     * Get 主题.
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
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Get 自定义数据字段名.
     */
    public function getDataName(): string
    {
        return $this->dataName;
    }

    /**
     * Get 实际日期必须大于等于本值
     */
    public function getNotBefore(): int
    {
        return $this->notBefore;
    }

    /**
     * 获取签名者对象
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
        if ('Ecdsa' === $this->signer)
        {
            return new $className(new \Lcobucci\JWT\Signer\Ecdsa\MultibyteStringConverter());
        }

        return new $className();
    }
}
