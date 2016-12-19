<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 05/10/2016
 * Time: 15:23
 */

namespace ObjectivePHP\Package\DebugBar;

use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Package\FastRoute\Config\FastRoute;
use ObjectivePHP\Package\FastRoute\FastRouteRouter;
use Zend\Diactoros\Response\TextResponse;

/**
 * Class DebugBarPackage
 * @package ObjectivePHP\Package\DebugBar
 */
class DebugBarPackage
{
    /** @var DebugBar */
    protected $debugBar;

    /**
     * @param ApplicationInterface $app
     */
    public function __invoke(ApplicationInterface $app)
    {
        $this->setDebugBar(new DebugBar($app));

        $this->getDebugBar()->run();
        $app->getServicesFactory()->registerService(['id' => 'debugbar', 'instance' => $this->getDebugBar()]);

        if ($app->getSteps()->has('route')) {
            $this->addAssetRouting($app);
        }

        // TODO: add the possibility to change the step name
        if ($app->getSteps()->has('rendering')) {
            $app->getStep('rendering')->plug([$this, 'buildDebugBar']);
        }
    }

    public function buildDebugBar()
    {
        if ($this->getDebugBar()->isValidUrl()) {
            $this->getDebugBar()->modifyResponse();
        }
    }

    /**
     * @param ApplicationInterface $app
     */
    public function addAssetRouting(ApplicationInterface $app)
    {
        $debugBar = $this->getDebugBar();

        // A new FastRouter is created for the debugbar.
        $app->getStep('route')->get('router')->getMiddleware()->register(new FastRouteRouter());

        // Assets routes
        $app->getConfig()->import(new FastRoute('debugbarcss', '/debugbarcss', function () use ($debugBar) {
                $renderer = $debugBar->getJavascriptRenderer();
                $content = $renderer->dumpAssetsToString('css');

                return new TextResponse($content, 200, ['Content-Type' => 'text/css']);
        }));

        $app->getConfig()->import(new FastRoute('debugbarjs', '/debugbarjs', function () use ($debugBar) {
                $renderer = $debugBar->getJavascriptRenderer();
                $content = $renderer->dumpAssetsToString('js');

                return new TextResponse($content, 200, ['Content-Type' => 'text/javascript']);
        }));
    }

    /**
     * @return DebugBar
     */
    public function getDebugBar(): DebugBar
    {
        return $this->debugBar;
    }

    /**
     * @param DebugBar $debugBar
     *
     * @return $this
     */
    public function setDebugBar(DebugBar $debugBar)
    {
        $this->debugBar = $debugBar;

        return $this;
    }
}
