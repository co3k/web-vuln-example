<?php

/**
 * co3k/web-vuln-example
 *
 * Copyright © 2014 Kousuke Ebihara <kousuke@co3k.org> All Rights Reserved
 *
 * This source code is licensed under the Apache License, Version 2.0. You can get a copy of
 * the license is the LICENSE file which is distributed with this code.
 */

namespace Example\Middleware;

use Silex\Application;

class RequireLogin
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function __invoke()
    {
        $this->app['session']->start();

        if ($this->app['request']->getPathInfo() === '/login') {
            return;
        }

        $userId = $this->app['session']->get('user_id');
        if (null === $userId) {
            return $this->app->redirect('/login');
        }

        $this->app['user'] = $this->app['repository.user']->getUser($userId);
        if (!$this->app['user']) {
            return $this->app->redirect('/login');
        }
    }
}
