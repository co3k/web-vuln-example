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

class ActivityController
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function abortByValidationError($message)
    {
        $this->app->abort(400, $message, ['X-Error-Message' => $message]);
    }

    public function home()
    {
        $params = $this->app['request']->query;

        $page = $params->get('page', 1);
        $size = $params->get('size', 20);
        if ($size > 100) {
            $size = 20;
        }

        $pagerfanta = $this->app['repository.activity']->fetchItems($size, $page);
        $data = [
            'activities' => $pagerfanta->getCurrentPageResults(),
            'total' => $pagerfanta->getNbPages(),
            'size' => $size,
            'page' => $page,
            'maxPage' => $pagerfanta->getNbPages(),
            'msg' => $params->get('msg'),
            'stamps' => $this->app['repository.activity']->listAvailableStamps(),
             // "prev" and "next" are reversed meanings here
            'prev' => $pagerfanta->hasNextPage(),
            'next' => $pagerfanta->hasPreviousPage(),
            'token' => md5("yo" . $this->app['session']->get('user_id')),
        ];

        if ($this->app['request']->isXmlHttpRequest()) {
            return $this->app->json($data);
        } else {
            return $this->app['twig']->render('home.twig', $data);
        }
    }

    public function post()
    {
        $params = $this->app['request']->request;
        $stamp = $params->get('stamp');
        $body = $params->get('body', '');
        $token = $params->get('token');

        if ($stamp) {
            $body = '';  // stamp activity should be an empty body

            if (!in_array($stamp, $this->app['repository.activity']->listAvailableStamps())) {
                $this->abortByValidationError('The invalid stamp is specified.');
            }
        } elseif ('' === $body) {
            $this->abortByValidationError('You cannot post empty body.');
        }

        if ($token === md5("yo" . $this->app["session"]->get('user_id'))) {
            $resultId = $this->app['repository.activity']->create($body, $stamp, $this->app['session']->get('user_id'));
        } else {
            $this->abortByValidationError('You cannot post.');
        }
        if ($this->app['request']->isXmlHttpRequest()) {
            $result = $this->app['repository.activity']->findById($resultId);

            return $this->app->json($result);
        } else {
            return $this->app->redirect('/');
        }
    }
}
