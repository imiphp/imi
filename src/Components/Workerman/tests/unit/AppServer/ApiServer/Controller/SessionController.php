<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\ApiServer\Controller;

use Imi\Controller\HttpController;
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
     *
     * @return array
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
     *
     * @return void
     */
    public function login(): void
    {
        Session::set('auth.username', 'admin');
    }

    /**
     * @Action
     *
     * @return void
     */
    public function logout(): void
    {
        Session::delete('auth');
    }

    /**
     * @Action
     *
     * @return void
     */
    public function sendSms(): void
    {
        Session::set('vcode', '1234');
    }

    /**
     * @Action
     *
     * @param string $vcode
     *
     * @return array
     */
    public function verifySms($vcode = ''): array
    {
        $storeVcode = Session::once('vcode');

        return [
            'success'   => '1234' === $storeVcode,
        ];
    }
}
