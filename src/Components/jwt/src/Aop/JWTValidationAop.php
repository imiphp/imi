<?php

namespace Imi\JWT\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\JWT\Annotation\JWTValidation;
use Imi\JWT\Exception\AnnotationNotFoundException;
use Imi\JWT\Exception\ConfigNotFoundException;
use Imi\JWT\Exception\InvalidAuthorizationException;
use Imi\JWT\Exception\InvalidTokenException;
use Imi\RequestContext;
use Imi\Util\ClassObject;
use Imi\Util\Http\Consts\RequestHeader;

/**
 * @Aspect
 */
class JWTValidationAop
{
    /**
     * 环绕注入.
     *
     * @PointCut(
     *      type=\Imi\Aop\PointCutType::ANNOTATION,
     *      allow={
     *          \Imi\JWT\Annotation\JWTValidation::class,
     *      }
     * )
     * @Around
     *
     * @return mixed
     */
    public function around(AroundJoinPoint $joinPoint)
    {
        $target = $joinPoint->getTarget();
        $class = BeanFactory::getObjectClass($target);
        $method = $joinPoint->getMethod();
        /** @var JWTValidation|null $jwtValidation */
        $jwtValidation = AnnotationManager::getMethodAnnotations($class, $method, JWTValidation::class)[0] ?? null;
        if (!$jwtValidation)
        {
            throw new AnnotationNotFoundException(sprintf('%s::%s() must have @%s annotation', $class, $method, JWTValidation::class));
        }
        /** @var \Imi\JWT\Bean\JWT $jwt */
        $jwt = App::getBean('JWT');
        $config = $jwt->getConfig($jwtValidation->name);
        if (!$config)
        {
            throw new ConfigNotFoundException('Must option the config @app.beans.JWT.list');
        }
        $tokenHandler = $config->getTokenHandler();
        if ($tokenHandler)
        {
            $jwtStr = $tokenHandler();
        }
        else
        {
            /** @var \Imi\Server\Http\Message\Request $request */
            $request = RequestContext::get('request');
            $authorization = $request->getHeaderLine(RequestHeader::AUTHORIZATION);
            [$bearer, $jwtStr] = explode(' ', $authorization, 2);
            if ('Bearer' !== $bearer)
            {
                throw new InvalidAuthorizationException(sprintf('Invalid Authorization value %s', $authorization));
            }
        }
        $token = $jwt->parseToken($jwtStr, $jwtValidation->name ?? $jwt->getDefault());

        // 验证
        $validationData = new \Lcobucci\JWT\ValidationData();
        if (false !== $jwtValidation->id)
        {
            $validationData->setId($jwtValidation->id ?? $config->getId());
        }
        if (false !== $jwtValidation->issuer)
        {
            $validationData->setIssuer($jwtValidation->issuer ?? $config->getIssuer());
        }
        if (false !== $jwtValidation->audience)
        {
            $validationData->setAudience($jwtValidation->audience ?? $config->getAudience());
        }
        if (false !== $jwtValidation->subject)
        {
            $validationData->setSubject($jwtValidation->subject ?? $config->getSubject());
        }
        if (!$token->validate($validationData))
        {
            throw new InvalidTokenException();
        }

        if ($jwtValidation->tokenParam || $jwtValidation->dataParam)
        {
            $args = ClassObject::convertArgsToKV($class, $joinPoint->getMethod(), $joinPoint->getArgs());
            if ($jwtValidation->tokenParam)
            {
                $args[$jwtValidation->tokenParam] = $token;
            }
            if ($jwtValidation->dataParam)
            {
                $data = $token->getClaim($config->getDataName());
                $args[$jwtValidation->dataParam] = $data;
            }
            $args = array_values($args);
        }

        return $joinPoint->proceed($args ?? null);
    }
}
