<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 05/10/2016
 * Time: 15:58
 */

namespace ObjectivePHP\Package\DebugBar;

use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;

class JavascriptRenderer extends BaseJavascriptRenderer
{

    // Use XHR handler by default, instead of jQuery
    protected $ajaxHandlerBindToJquery = false;
    protected $ajaxHandlerBindToXHR = true;

    public function __construct(DebugBar $debugBar, $baseUrl = null, $basePath = null)
    {
        parent::__construct($debugBar, $baseUrl, $basePath);
        //$this->cssFiles['objective'] = __DIR__ . '/Resources/objective-debugbar.css';
    }
}
