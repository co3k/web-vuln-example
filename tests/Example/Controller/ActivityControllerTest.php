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
use Example\Controller\ActivityController;

class ActivityControllerTest extends PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $this->app = new Silex\Application();
        $this->app['request'] = $this->getMock('Symfony\\Component\\HttpFoundation\\Request');
        $this->app['session'] = $this->getMock('Symfony\\Component\\HttpFoundation\\Session\\Session');
        $this->app['twig'] = $this->getMock('Twig_Environment');
        $this->app['repository.activity'] = $this->getMockBuilder('Example\\Repository\\Activity')
            ->disableOriginalConstructor()
            ->getMock();
        $this->app['repository.activity']->method('fetchItems')->will($this->returnValue(
            $this->getMockBuilder('Pagerfanta\\Pagerfanta')->disableOriginalConstructor()->getMock()
        ));
    }

    /**
     * (GET /) 大きすぎる size は強制的に 20 に置き換えられる
     */
    public function testHomeLargerSizeTurnsLowestOne()
    {
        $this->app['twig']->method('render')->with(
            $this->anything(),
            $this->callback(function ($data) { return isset($data['size']) && $data['size'] == 20; })
        );
        $this->app['request']->query = new ParameterBag([
            'size' => 101,
        ]);

        $controller = new ActivityController($this->app);
        $controller->home();
    }

    /**
     * (GET /) 適正な size はそのまま通す
     */
    public function testHomeBetterSizeShouldBePassed()
    {
        $this->app['twig']->method('render')->with(
            $this->anything(),
            $this->callback(function ($data) { return isset($data['size']) && $data['size'] == 99; })
        );
        $this->app['request']->query = new ParameterBag([
            'size' => 99,
        ]);

        $controller = new ActivityController($this->app);
        $controller->home();
    }

    /**
     * (GET /) XMLHttpRequest 経由のリクエストの場合、 Twig を経由せず JsonResponse を返す
     */
    public function testHomeWithXHRReturnsJsonResponse()
    {
        $this->app['request']->query = new ParameterBag();
        $this->app['request']->method('isXmlHttpRequest')->will($this->returnValue(true));
        $this->app['twig']->expects($this->never())->method('render');

        $controller = new ActivityController($this->app);
        $response = $controller->home();

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\JsonResponse', $response);
    }

    /**
     * (POST /activity) 未知のスタンプが指定された場合はエラーとする
     *
     * @expectedException Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testPostErrorWithUnknownStamp()
    {
        $this->app['request']->request = new ParameterBag([
            'stamp' => 'UNKNOWN',
            'body' => '',
        ]);
        $this->app['repository.activity']->method('listAvailableStamps')->will($this->returnValue([
            'KNOWN_1', 'KNOWN_2', 
        ]));

        $controller = new ActivityController($this->app);
        $response = $controller->post();
    }

    /**
     * (POST /activity) 空の body が指定され、かつ、 stamp が指定されていない場合はエラーとする
     *
     * @expectedException Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testPostErrorWithEmptyBody()
    {
        $this->app['request']->request = new ParameterBag([
            'body' => '',
        ]);

        $controller = new ActivityController($this->app);
        $response = $controller->post();
    }

    /**
     * (POST /activity) パラメータがなにも指定されていない場合はエラーとする
     *
     * @expectedException Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testPostErrorWithEmptyParameters()
    {
        $this->app['request']->request = new ParameterBag();

        $controller = new ActivityController($this->app);
        $response = $controller->post();
    }

    /**
     * (POST /activity) body が指定されている場合は stamp を空として保存したうえで / にリダイレクト
     */
    public function testPostWithBody()
    {
        $this->app['request']->request = new ParameterBag([
            'body' => 'SPECIFIED_BODY',
        ]);
        $this->app['repository.activity']->method('create')->with(
            $this->equalTo('SPECIFIED_BODY'),
            $this->equalTo(null)
        );

        $controller = new ActivityController($this->app);
        $response = $controller->post();

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\RedirectResponse', $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }

    /**
     * (POST /activity) stamp が指定されている場合は body を空として保存したうえで / にリダイレクト
     */
    public function testPostWithStampAndBody()
    {
        $this->app['request']->request = new ParameterBag([
            'body' => 'SPECIFIED_BODY',
            'stamp' => 'KNOWN_1',
        ]);
        $this->app['repository.activity']->method('listAvailableStamps')->will($this->returnValue([
            'KNOWN_1', 'KNOWN_2', 
        ]));
        $this->app['repository.activity']->method('create')->with(
            $this->equalTo(''),
            $this->equalTo('KNOWN_1')
        );

        $controller = new ActivityController($this->app);
        $response = $controller->post();

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\RedirectResponse', $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }

    /**
     * (POST /activity) XMLHttpRequest 経由のリクエストの場合、 RedirectResponse ではなく JsonResponse を返す
     */
    public function testPostWithXHRReturnsJsonResponse()
    {
        $this->app['request']->request = new ParameterBag([
            'body' => 'BODY',
        ]);
        $this->app['request']->method('isXmlHttpRequest')->will($this->returnValue(true));

        $controller = new ActivityController($this->app);
        $response = $controller->post();

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\JsonResponse', $response);
    }
}
