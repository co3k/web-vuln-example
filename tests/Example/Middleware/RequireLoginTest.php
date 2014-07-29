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
use Example\Middleware\RequireLogin;

class RequireLoginTest extends PHPUnit_Framework_TestCase
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
     * ログイン画面ではなにもしない
     */
    public function testSilentInLoginPage()
    {
        $this->app['request']->method('getPathInfo')->willReturn('/login');

        $invoker = new RequireLogin($this->app);
        $this->assertNull($invoker());
    }

    /**
     * 未ログインユーザをログインページに誘導する
     */
    public function testDirectAnonymousToLoginPage()
    {
        $this->app['request']->method('getPathInfo')->willReturn('/login_required_page');

        $invoker = new RequireLogin($this->app);
        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\RedirectResponse', $invoker());
    }

    /**
     * 無効なユーザをログインページに誘導する
     */
    public function testDirectUnknownUserToLoginPage()
    {
        $this->app['request']->method('getPathInfo')->willReturn('/login_required_page');
        $this->app['session']->method('get')->willReturn('unknown_id');
        $this->app['repository.user']->method('getUser')->willReturn(false);

        $invoker = new RequireLogin($this->app);
        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\RedirectResponse', $invoker());
    }

    /**
     * 有効なユーザに対してはなにもしない
     */
    public function testSilentForValidUser()
    {
        $this->app['request']->method('getPathInfo')->willReturn('/login_required_page');
        $this->app['session']->method('get')->willReturn('known_id');
        $this->app['repository.user']->method('getUser')->willReturn(['name' => 'I am a user :)']);

        $invoker = new RequireLogin($this->app);
        $this->assertNull($invoker());
    }
}
