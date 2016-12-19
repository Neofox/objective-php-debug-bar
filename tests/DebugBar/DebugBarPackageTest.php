<?php

namespace Tests\ObjectivePHP\Package\DebugBar;

use ObjectivePHP\Application\AbstractApplication;
use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Message\Request\HttpRequest;
use ObjectivePHP\Message\Response\HttpResponse;
use ObjectivePHP\Package\DebugBar\DebugBar;
use ObjectivePHP\Package\DebugBar\DebugBarPackage;

class DebugBarPackageTest extends \PHPUnit_Framework_TestCase
{
    public function testPackageIsCallable()
    {
        $package = new DebugBarPackage();
        $this->assertTrue(is_callable($package));
    }

    public function testBuildDebugBar()
    {
        $package = new DebugBarPackage();

        $response = new HttpResponse();
        $response->getBody()->write('<body><p>Hello</p></body>');
        $request = $this->getMockBuilder(HttpRequest::class)->getMock();
        /** @var ApplicationInterface $app */
        $app = $this->getMockBuilder(AbstractApplication::class)->setMethods(['getConfig'])->getMockForAbstractClass();
        $app->setResponse($response);
        $app->setRequest($request);
        $debugbar = $this->getMockBuilder(DebugBar::class)->setConstructorArgs([$app])->getMock();
        $debugbar->method('isValidUrl')->willReturn(true);
        $package->setDebugBar($debugbar);

        // run the package
        $package->__invoke($app);

        $this->assertTrue($app->getServicesFactory()->has('debugbar'));

        $app->getResponse()->getBody()->rewind();
        // TODO: to be fix with new assets tests
//        $this->assertContains(
//            'var phpdebugbar = new PhpDebugBar.DebugBar();',
//            $app->getResponse()->getBody()->getContents()
//        );
        $app->getResponse()->getBody()->rewind();
        $this->assertContains('<p>Hello</p>', $app->getResponse()->getBody()->getContents());

        /** @var DebugBar $debugbar */
        $debugbar = $app->getServicesFactory()->get('debugbar');
    }
}
