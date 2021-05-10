<?php

namespace Imi\JWT\Bean;

use Imi\Bean\Annotation\Bean;
use Imi\JWT\Exception\ConfigNotFoundException;
use Imi\JWT\Exception\InvalidTokenException;
use Imi\JWT\Model\JWTConfig;
use Imi\JWT\Util\Builder;
use Imi\JWT\Util\Parser;
use Lcobucci\JWT\Token;

/**
 * @Bean("JWT")
 */
class JWT
{
    /**
     * 配置列表.
     *
     * @var array
     */
    protected $list = [];

    /**
     * 默认配置名.
     *
     * @var string|null
     */
    protected $default;

    /**
     * 处理后的列表.
     *
     * @var \Imi\JWT\Model\JWTConfig[]
     */
    private $parsedList = [];

    /**
     * @return void
     */
    public function __init()
    {
        foreach ($this->list as $key => $item)
        {
            $this->parsedList[$key] = new JWTConfig($item);
        }
        if (null === $this->default)
        {
            // 如果没有设置默认配置，默认使用第一个配置
            reset($this->list);
            $this->default = key($this->list);
        }
    }

    /**
     * Get 配置列表.
     *
     * @return \Imi\JWT\Model\JWTConfig[]
     */
    public function getList(): array
    {
        return $this->parsedList;
    }

    /**
     * Get 默认配置名.
     *
     * @return string|null
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * 获取配置.
     *
     * @param string|null $name
     *
     * @return \Imi\JWT\Model\JWTConfig|null
     */
    public function getConfig(?string $name = null): ?JWTConfig
    {
        if (null === $name)
        {
            $name = $this->getDefault();
        }

        return $this->parsedList[$name] ?? null;
    }

    /**
     * 获取 Token 构建器对象
     *
     * @param string|null $name
     *
     * @return Builder
     */
    public function getBuilderInstance(?string $name = null): Builder
    {
        $config = $this->getConfig($name);
        if (!$config)
        {
            throw new ConfigNotFoundException('Must option the config @app.beans.JWT.list');
        }
        $builder = new Builder();
        $time = time();
        $builder->permittedFor($config->getAudience())
                ->relatedTo($config->getSubject())
                ->expiresAt($time + $config->getExpires())
                ->issuedBy($config->getIssuer())
                ->canOnlyBeUsedAfter($config->getNotBefore())
                ->identifiedBy($config->getId());
        $issuedAt = $config->getIssuedAt();
        if (true === $issuedAt)
        {
            $builder->issuedAt($time);
        }
        elseif (false !== $issuedAt)
        {
            $builder->issuedAt($issuedAt);
        }
        if ($headers = $config->getHeaders())
        {
            foreach ($headers as $k => $v)
            {
                $builder->withHeader($k, $v);
            }
        }
        $signer = $config->getSignerInstance();
        $key = $config->getPrivateKey();
        $builder->sign($signer, $key);

        return $builder;
    }

    /**
     * 生成 Token.
     *
     * @param mixed         $data
     * @param string|null   $name
     * @param callable|null $beforeGetToken
     *
     * @return \Lcobucci\JWT\Token
     */
    public function getToken($data, ?string $name = null, ?callable $beforeGetToken = null): Token
    {
        $builder = $this->getBuilderInstance($name);
        if ($beforeGetToken)
        {
            $beforeGetToken($builder);
        }
        $config = $this->getConfig($name);
        $builder->withClaim($config->getDataName(), $data);

        return $builder->getToken();
    }

    /**
     * 处理 Token.
     *
     * @param string      $jwt
     * @param string|null $name
     *
     * @return \Lcobucci\JWT\Token
     */
    public function parseToken(string $jwt, ?string $name = null): Token
    {
        $token = (new Parser())->parse($jwt);
        $config = $this->getConfig($name);
        if ($config)
        {
            $signer = $config->getSignerInstance();
            $key = $config->getPublicKey();
            if (!$token->verify($signer, $key))
            {
                throw new InvalidTokenException();
            }
        }

        return $token;
    }
}
