<?php

/**
 * co3k/web-vuln-example
 *
 * Copyright © 2014 Kousuke Ebihara <kousuke@co3k.org> All Rights Reserved
 *
 * This source code is licensed under the Apache License, Version 2.0. You can get a copy of
 * the license is the LICENSE file which is distributed with this code.
 */

require_once __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../config.php';

$app->before(new Example\Middleware\RequireLogin($app));
$app->after(new Example\Middleware\DisableXSSFilter($app));

$app->get('/', 'controller.activity:home');
$app->post('/activity', 'controller.activity:post');
$app->get('/login', 'controller.login:loginForm');
$app->post('/login', 'controller.login:authenticate');
$app->post('/logout', 'controller.login:deauthenticate');

$app->run();
