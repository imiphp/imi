<?php

# macro

declare(strict_types=1);

namespace Imi\JWT\Bean;

use Imi\Bean\Annotation\Bean;
use Imi\JWT\Exception\ConfigNotFoundException;
use Imi\JWT\Exception\InvalidTokenException;
use Imi\JWT\Model\JWTConfig;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

/**
 * @Bean("JWT")
 */
class JWT
{
    /**
     * 配置列表.
     */
    protected array $list = [];

    /**
     * 默认配置名.
     */
    protected ?string $default = null;

    /**
     * 处理后的列表.
     *
     * @var \Imi\JWT\Model\JWTConfig[]
     */
    private array $parsedList = [];

    public function __init(): void
    {
        foreach ($this->list as $key => $item)
        {
            $this->parsedList[$key] = new JWTConfig($item);
        }
        if (null === $this->default)
        {
            // 如果没有设置默认配置，默认使用第一个配置
            $this->default = array_key_first($this->list);
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
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * 获取配置.
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
     */
    public function getBuilderInstance(?string $name = null): Builder
    {
        $config = $this->getConfig($name);
        if (!$config)
        {
            throw new ConfigNotFoundException('Must option the config @app.beans.JWT.list');
        }
        if (3 === $this->getJwtPackageVersion())
        {
            $builder = new Builder();
            $now = new \DateTimeImmutable();
            $builder->permittedFor($config->getAudience() ?? '')
                ->relatedTo($config->getSubject() ?? '')
                ->expiresAt($now->modify('+' . ($config->getExpires() ?? 0) . ' second'))
                ->issuedBy($config->getIssuer() ?? '')
                ->canOnlyBeUsedAfter($now->modify('+' . $config->getNotBefore() . ' second'))
                ->identifiedBy($config->getId() ?? '');
            $issuedAt = $config->getIssuedAt();
            if (true === $issuedAt)
            {
                $builder->issuedAt($now);
            }
            elseif (false !== $issuedAt)
            {
                $builder->issuedAt($now->modify('+' . ($issuedAt ?? 0) . ' second'));
            }
            if ($headers = $config->getHeaders())
            {
                foreach ($headers as $k => $v)
                {
                    $builder->withHeader($k, $v);
                }
            }
            $signer = $config->getSignerInstance();
            $key = $config->getPrivateKey() ?? '';
            $builder->sign($signer, \Lcobucci\JWT\Signer\Key\InMemory::plainText($key));
        }
        else
        {
            $configuration = Configuration::forAsymmetricSigner($config->getSignerInstance(), InMemory::plainText($config->getPrivateKey() ?? ''), InMemory::plainText($config->getPublicKey() ?? ''));
            $builder = $configuration->builder();

            $now = new \DateTimeImmutable();
            $builder->permittedFor($config->getAudience() ?? '')
                ->relatedTo($config->getSubject() ?? '')
                ->expiresAt($now->modify('+' . ($config->getExpires() ?? 0) . ' second'))
                ->issuedBy($config->getIssuer() ?? '')
                ->canOnlyBeUsedAfter($now->modify('+' . $config->getNotBefore() . ' second'))
                ->identifiedBy($config->getId() ?? '');
            $issuedAt = $config->getIssuedAt();
            if (true === $issuedAt)
            {
                $builder->issuedAt($now);
            }
            elseif (false !== $issuedAt)
            {
                $builder->issuedAt($now->modify('+' . ($issuedAt ?? 0) . ' second'));
            }
            if ($headers = $config->getHeaders())
            {
                foreach ($headers as $k => $v)
                {
                    $builder->withHeader($k, $v);
                }
            }
        }

        return $builder;
    }

    public function getParserInstance(?string $name = null): Parser
    {
        if (3 === $this->getJwtPackageVersion())
        {
            return new \Lcobucci\JWT\Parser();
        }
        else
        {
            $config = $this->getConfig($name);
            $configuration = Configuration::forAsymmetricSigner($config->getSignerInstance(), InMemory::plainText($config->getPrivateKey() ?? ''), InMemory::plainText($config->getPublicKey() ?? ''));

            return $configuration->parser();
        }
    }

    /**
     * 生成 Token.
     *
     * @param mixed $data
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

        return $builder->getToken($config->getSignerInstance(), InMemory::plainText($config->getPrivateKey() ?? ''));
    }

    /**
     * 处理 Token.
     */
    public function parseToken(string $jwt, ?string $name = null): Token
    {
        $config = $this->getConfig($name);
        if (!$config)
        {
            throw new InvalidTokenException();
        }
        if (3 === $this->getJwtPackageVersion())
        {
            $token = (new \Lcobucci\JWT\Parser())->parse($jwt);
            $signer = $config->getSignerInstance();
            $key = $config->getPublicKey() ?? '';
            if (!$token->verify($signer, InMemory::plainText($key)))
            {
                throw new InvalidTokenException();
            }
        }
        else
        {
            $parser = $this->getParserInstance($name);
            $token = $parser->parse($jwt);
            $signer = $config->getSignerInstance();
            $key = $config->getPublicKey() ?? '';
            $signedWith = new SignedWith($signer, InMemory::plainText($key));
            try
            {
                $signedWith->assert($token);
            }
            catch (\Throwable $th)
            {
                throw new InvalidTokenException($th->getMessage(), $th->getCode(), $th->getPrevious());
            }
        }

        return $token;
    }

    public function getJwtPackageVersion(): int
    {
        #if 0
        return class_exists(\Lcobucci\JWT\Token\Parser::class) ? 4 : 3;
        #endif
        #if class_exists(\Lcobucci\JWT\Token\Parser::class)
        // @phpstan-ignore-next-line
        return 4;
        #else
        return 3;
        #endif
    }
}
