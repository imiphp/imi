<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\SessionTestServer\Controller;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Session\Session;

/**
 * @Controller("/session/")
 */
class SessionController extends HttpController
{
    /**
     * @Action
     */
    public function status(): array
    {
        $username = Session::get('auth.username');
        if ($username)
        {
            $data = [
                'isLogin'   => true,
                'username'  => $username,
            ];
        }
        else
        {
            $data = [
                'isLogin'   => false,
            ];
        }

        return $data;
    }

    /**
     * @Action
     */
    public function login(): array
    {
        Session::set('auth.username', 'admin');

        return [
            'sessionId' => Session::getId(),
        ];
    }

    /**
     * @Action
     */
    public function logout(): void
    {
        Session::delete('auth');
    }

    /**
     * @Action
     */
    public function sendSms(): array
    {
        Session::set('vcode', '1234');

        return [
            'sessionId' => Session::getId(),
        ];
    }

    /**
     * @Action
     *
     * @param string $vcode
     */
    public function verifySms($vcode = ''): array
    {
        $storeVcode = Session::once('vcode');

        return [
            'success'   => '1234' === $storeVcode,
        ];
    }
}
