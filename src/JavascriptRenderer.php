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
        $this->cssVendors['fontawesome'] = __DIR__ . '/Resources/vendor/font-awesome.css';
    }

    /**
     * @inheritdoc
     */
    public function renderHead()
    {
        $html  = "<link rel='stylesheet' type='text/css' property='stylesheet' href='/debugbarcss'>";
        $html .= "<script type='text/javascript' src='/debugbarjs'></script>";

        if ($this->isJqueryNoConflictEnabled()) {
            $html .= '<script type="text/javascript">jQuery.noConflict(true);</script>' . "\n";
        }

        return $html;
    }

    /**
     * Return assets as a string
     *
     * @param string $type 'js' or 'css'
     * @return string
     */
    public function dumpAssetsToString($type)
    {
        $files = $this->getAssets($type);
        $content = '';
        foreach ($files as $file) {
            $content .= file_get_contents($file) . "\n";
        }
        return $content;
    }
}
