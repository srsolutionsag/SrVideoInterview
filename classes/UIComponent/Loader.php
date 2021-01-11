<?php

namespace srag\Plugins\SrVideoInterview\UIComponent;

use ILIAS\DI\Container;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Component\Input\Field\MultiSelectUserInput;
use ILIAS\UI\Implementation\Component\Input\Field\SrVideoInterviewRenderer;
use ILIAS\UI\Implementation\Component\Input\Field\VideoRecorderInput;

/**
 * Class Loader
 */
class Loader implements \ILIAS\UI\Implementation\Render\Loader
{
    /**
     * @var Container
     */
    protected $dic;

    /**
     * @var \ilSrVideoInterviewPlugin
     */
    protected $plugin;

    /**
     * Loader constructor.
     * @param Container $dic
     * @param           $plugin
     */
    public function __construct(Container $dic, $plugin)
    {
        $this->dic = $dic;
        $this->plugin = $plugin;
    }

    public function getRendererFor(Component $component, array $contexts)
    {
        if ($component instanceof VideoRecorderInput ||
            $component instanceof MultiSelectUserInput
        ) {
            $renderer = new SrVideoInterviewRenderer(
                $this->dic['ui.factory'],
                $this->dic["ui.template_factory"],
                $this->dic["lng"],
                $this->dic["ui.javascript_binding"],
                $this->dic["refinery"]
            );

            $renderer->setPluginInstance($this->plugin);

            return $renderer;
        }

        return $this->dic['ui.component_renderer_loader']->getRendererFor($component, $contexts);
    }

    public function getRendererFactoryFor(Component $component)
    {
        return $this->dic['ui.component_renderer_loader']->getRendererFactoryFor($component);
    }
}
