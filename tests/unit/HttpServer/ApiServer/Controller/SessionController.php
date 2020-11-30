<?php

declare(strict_types=1);

namespace Imi\Test\HttpServer\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Session\Session;

/**
 * @Controller("/session/")
 */
class SessionController extends HttpController
{
    /**
     * @Action
     *
     * @return void
     */
    public function status()
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
    public function login()
    {
        Session::set('auth.username', 'admin');
    }

    /**
     * @Action
     *
     * @return void
     */
    public function logout()
    {
        Session::delete('auth');
    }

    /**
     * @Action
     *
     * @return void
     */
    public function sendSms()
    {
        Session::set('vcode', '1234');
    }

    /**
     * @Action
     *
     * @param string $vcode
     *
     * @return void
     */
    public function verifySms($vcode = '')
    {
        $storeVcode = Session::once('vcode');

        return [
            'success'   => '1234' === $storeVcode,
        ];
    }
}
