<?php

/**
 * co3k/web-vuln-example
 *
 * Copyright © 2014 Kousuke Ebihara <kousuke@co3k.org> All Rights Reserved
 *
 * This source code is licensed under the Apache License, Version 2.0. You can get a copy of
 * the license is the LICENSE file which is distributed with this code.
 */

namespace Example\Controller;

use Silex\Application;

class LoginController
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function loginForm()
    {
        $params = $this->app['request']->query;

        return $this->app['twig']->render('login.twig', [
            'username' => $params->get('username'),
            'isError' => $params->get('error', false),
        ]);
    }

    public function authenticate()
    {
        $params = $this->app['request']->request;

        $username = $params->get('username');
        $userId = $this->app['repository.user']->login($username, $params->get('password'));
        if (!$userId) {
            return $this->app->redirect('/login?username='.$username.'&error=1');
        }

        if (!$this->app['session']->invalidate()) {
            $this->app->abort(500, 'Cannot regenerate session id');
        }

        $this->app['session']->set('user_id', $userId);
        $this->app['session']->set('username', $username);

        return $this->app->redirect('/');
    }

    public function deauthenticate()
    {
        $this->app['session']->invalidate();

        return $this->app->redirect('/');
    }
}
