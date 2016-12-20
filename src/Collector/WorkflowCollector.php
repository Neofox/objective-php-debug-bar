<?php
/**
 * Created by OPCODING
 * User: jerome
 * Date: 20/12/2016
 * Time: 11:56
 */

namespace ObjectivePHP\Package\DebugBar\Collector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Application\Middleware\MiddlewareInterface;
use ObjectivePHP\Application\Workflow\Hook;
use ObjectivePHP\Application\Workflow\Step;

class WorkflowCollector extends DataCollector implements Renderable
{

    /** @var ApplicationInterface */
    protected $app;

    /**
     * Called by the DebugBar when data needs to be collected
     *
     * @return array Collected data
     */
    public function collect()
    {
        $output = [];

        /** @var Step $step */
        foreach ($this->getApp()->getSteps() as $step) {
            $output[$step->getName()] = '';

            /** @var Hook $hook */
            foreach ($step as $alias => $hook) {
                if (is_int($alias)) {
                    $alias = 'unaliased';
                }

                $output[$step->getName()] .= "\n {$alias}: {$hook->getMiddleware()->getDescription()}";
            }
        }

        return $output;
    }

    /**
     * Returns the unique name of the collector
     *
     * @return string
     */
    public function getName()
    {
        return 'workflow';
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        $name = $this->getName();
        return array(
            "$name" => array(
                "icon" => "gear",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "$name",
                "default" => "{}",
            ),
        );
    }

    /**
     * @return ApplicationInterface
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param ApplicationInterface $app
     *
     * @return $this
     */
    public function setApp($app)
    {
        $this->app = $app;

        return $this;
    }
}
