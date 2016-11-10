<?php

namespace ObjectivePHP\Package\DebugBar;


use ObjectivePHP\Package\DebugBar\Config as DebugBarConfig;
use DebugBar\DataCollector\{
    MemoryCollector, MessagesCollector, PDO\PDOCollector, PhpInfoCollector, RequestDataCollector, TimeDataCollector, ExceptionsCollector
};
use DebugBar\DebugBar as BaseDebugBar;
use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Message\Request\HttpRequest;

class DebugBar extends BaseDebugBar
{

    /** @var ApplicationInterface */
    protected $app;

    /** @var bool */
    protected $isRunning = false;

    /**
     * DebugBar constructor.
     *
     * @param $app
     */
    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }


    public function run()
    {

        if ($this->isRunning) {
            return;
        }

        $debugBar = $this;

        if ($this->shouldCollect('phpinfo', true)) {
            $this->addCollector(new PhpInfoCollector());
        }

        if ($this->shouldCollect('messages', true)) {
            $this->addCollector(new MessagesCollector());
        }

        if ($this->shouldCollect('time', true)) {
            $this->addCollector(new TimeDataCollector());

            $this->startMeasure('application', 'Application');
        }

        if ($this->shouldCollect('memory', true)) {
            $this->addCollector(new MemoryCollector());
        }

        if ($this->shouldCollect('exceptions', true)) {
            $this->addCollector(new ExceptionsCollector());
        }

        if ($this->shouldCollect('request', true)) {
            $this->addCollector(new RequestDataCollector());
        }

        // TODO: need a pdo object
        if ($this->shouldCollect('db', false)) {
            if ($debugBar->hasCollector('time')) {
                $timeCollector = $debugBar->getCollector('time');
            } else {
                $timeCollector = null;
            }
            $this->addCollector(new PDOCollector($TODO, $timeCollector));
        }

    }

    public function shouldCollect($name, $default = false) : bool
    {
        $config = $this->app->getConfig()->subset(DebugBarConfig\DebugBar::class);

        if (isset($config[$name])) {
            return (bool)$config[$name];
        }

        return $default;
    }

    /**
     * Starts a measure
     *
     * @param string $name  Internal name, used to stop the measure
     * @param string $label Public name
     */
    public function startMeasure($name, $label = null)
    {
        if ($this->hasCollector('time')) {
            /** @var \DebugBar\DataCollector\TimeDataCollector $collector */
            $collector = $this->getCollector('time');
            $collector->startMeasure($name, $label);
        }
    }

    /**
     * Stops a measure
     *
     * @param string $name
     */
    public function stopMeasure($name)
    {
        if ($this->hasCollector('time')) {
            /** @var \DebugBar\DataCollector\TimeDataCollector $collector */
            $collector = $this->getCollector('time');
            try {
                $collector->stopMeasure($name);
            } catch (\Exception $e) {
                //  $this->addThrowable($e);
            }
        }
    }

    public function modifyResponse()
    {

        $response = $this->app->getResponse();
        $request = $this->app->getRequest();

        if ($request instanceof HttpRequest) {
            // If request is redirection
            if ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
                $this->stackData();
            }
            // If request if Ajax
            /** @var HttpRequest $request */
            else {
                if ($request->getHeaderLine('X-Request-With') == 'XMLHttpRequest') {
                    $this->sendDataInHeaders(true);
                } elseif (($response->getHeaderLine('Content-Type') && strpos($response->getHeaderLine('Content-Type'),
                        'html') === false)
                ) {
                    // Just collect + store data, don't inject it.
                    $this->collect();
                } elseif ($this->shouldCollect('inject_debug_bar')) {
                    $this->injectDebugbar();
                }

                $this->injectDebugbar();

            }

        }

    }

    public function injectDebugbar()
    {
        $response = $this->app->getResponse();

        $response->getBody()->rewind();
        $content = $response->getBody()->getContents();
        $renderer = $this->getJavascriptRenderer(__DIR__.'/../vendor/maximebf/debugbar/src/DebugBar/Resources/');

        $renderedContent = $renderer->renderHead() . $renderer->render();
        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
        } else {
            $content = $content . $renderedContent;
        }

        // Update the new content and reset the content length
        $response->getBody()->rewind();
        $response->getBody()->write($content);
        $response->withoutHeader('Content-Length');

    }

    public function getJavascriptRenderer($baseUrl = null, $basePath = null)
    {
        if ($this->jsRenderer === null) {
            $this->jsRenderer = new JavascriptRenderer($this, $baseUrl, $basePath);
        }

        return $this->jsRenderer;
    }


}