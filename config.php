<?php

/**
 * co3k/web-vuln-example
 *
 * Copyright © 2014 Kousuke Ebihara <kousuke@co3k.org> All Rights Reserved
 *
 * This source code is licensed under the Apache License, Version 2.0. You can get a copy of
 * the license is the LICENSE file which is distributed with this code.
 */

$app = new Silex\Application();
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/views',
]);
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['debug'] = true;
$app['db_path'] = realpath(__DIR__.'/db').'/example.db';
$app['db'] = function ($app) {
    $config = new \Doctrine\DBAL\Configuration();

    return Doctrine\DBAL\DriverManager::getConnection([
        'driver' => 'pdo_sqlite',
        'path' => $app['db_path'],
    ], $config);
};

$app['repository.user'] = function ($app) { return new Example\Repository\User($app['db']); };
$app['repository.activity'] = function ($app) { return new Example\Repository\Activity($app['db']); };
$app['controller.login'] = $app->share(function() use ($app) { return new Example\Controller\LoginController($app); });
$app['controller.activity'] = $app->share(function() use ($app) { return new Example\Controller\ActivityController($app); });

return $app;
