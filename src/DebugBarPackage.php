<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 05/10/2016
 * Time: 15:23
 */

namespace ObjectivePHP\Package\DebugBar;


use ObjectivePHP\Application\ApplicationInterface;

class DebugBarPackage
{
    function __invoke(ApplicationInterface $app)
    {
        $app->getAutoloader()->addPsr4('Dashblog\\Package\\DebugBar\\', 'packages/DebugBar/src');
        $app->getStep('rendering')->plug([$this, 'buildDebugBar']);

    }

    public function buildDebugBar(ApplicationInterface $app)
    {
        $debugBar = new DebugBar($app);
        $debugBar->run();

        $debugBar->modifyResponse();

        $app->getServicesFactory()->registerService(['id' => 'debugbar.debugbar', 'instance' => $debugBar]);


    }


}