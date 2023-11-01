<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\RedisSessionServer\ApiServer\Controller;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Session\Session;

#[Controller(prefix: '/session/')]
class SessionController extends HttpController
{
    #[Action]
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

    #[Action]
    public function login(): void
    {
        Session::set('auth.username', 'admin');
    }

    #[Action]
    public function logout(): void
    {
        Session::delete('auth');
    }

    #[Action]
    public function sendSms(): void
    {
        Session::set('vcode', '1234');
    }

    #[Action]
    public function verifySms(string $vcode = ''): array
    {
        $storeVcode = Session::once('vcode');

        return [
            'success'   => '1234' === $storeVcode,
        ];
    }
}
