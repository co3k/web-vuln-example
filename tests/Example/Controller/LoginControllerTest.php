<?php

/**
 * co3k/web-vuln-example
 *
 * Copyright © 2014 Kousuke Ebihara <kousuke@co3k.org> All Rights Reserved
 *
 * This source code is licensed under the Apache License, Version 2.0. You can get a copy of
 * the license is the LICENSE file which is distributed with this code.
 */

use Silex\Application;
use Symfony\Component\HttpFoundation\ParameterBag;
use Example\Controller\LoginController;

class LoginControllerTest extends PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $this->app = new Silex\Application();
        $this->app['request'] = $this->getMock('Symfony\\Component\\HttpFoundation\\Request');
        $this->app['session'] = $this->getMock('Symfony\\Component\\HttpFoundation\\Session\\Session');
        $this->app['repository.user'] = $this->getMockBuilder('Example\\Repository\\User')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * 認証に失敗した場合はログインページにリダイレクト
     */
    public function testAuthenticateFailsForUnknownAccount()
    {
        $this->app['request']->request = new ParameterBag([
            'username' => 'unknown',
            'password' => 'unknown',
        ]);
        $this->app['repository.user']->method('login')->willReturn(false);

        $controller = new LoginController($this->app);
        $response = $controller->authenticate();

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\RedirectResponse', $response);
        $this->assertEquals('/login?username=unknown&error=1', $response->getTargetUrl());
    }

    /**
     * 正常にセッションを再生成できない場合 (意図しない出力がされているなど) は強制的にログインを失敗させる
     *
     * @expectedException Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testAuthenticateFailsForNotInvalidatedExistingSessions()
    {
        $this->app['request']->request = new ParameterBag([
            'username' => 'known',
            'password' => 'known',
        ]);
        $this->app['repository.user']->method('login')->willReturn('knownId');
        $this->app['session']->method('invalidate')->willReturn(false);

        $controller = new LoginController($this->app);
        $response = $controller->authenticate();
    }

    /**
     * 認証に成功した場合は / にリダイレクトさせる
     */
    public function testAuthenticateSuccess()
    {
        $this->app['request']->request = new ParameterBag([
            'username' => 'known',
            'password' => 'known',
        ]);
        $this->app['repository.user']->method('login')->willReturn('knownId');
        $this->app['session']->method('invalidate')->willReturn(true);

        $controller = new LoginController($this->app);
        $response = $controller->authenticate();

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\RedirectResponse', $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }
}
